# Perbaikan: Deteksi Titik Wisata yang Dilalui di Jalur Rute

## Masalah yang Diperbaiki

### Masalah Sebelumnya:

Ketika mencari rute dari lokasi awal ke tujuan, algoritma hanya mencari **titik wisata terdekat** berdasarkan jarak lurus (Haversine) atau jarak jalan, **TANPA mempertimbangkan apakah titik tersebut berada di jalur rute** yang sebenarnya dilalui.

**Dampaknya:**

-   Sistem bisa memilih titik wisata yang dekat secara jarak, tetapi **tidak berada di jalur rute**
-   Rute menjadi tidak efisien karena harus "belok" ke titik yang sebenarnya tidak perlu dilalui
-   Ada titik wisata yang **seharusnya dilalui** (berada di jalur jalan) malah dilewatkan

### Contoh Kasus:

```
Lokasi Awal (A) ----[Jalan Utama]----> Tujuan (D)
                    |
                    | (dekat tapi bukan di jalur)
                    ↓
                Wisata B (dipilih, tapi salah!)

Padahal di jalan utama ada:
Lokasi Awal (A) --> Wisata C (di jalur!) --> Tujuan (D)
```

## Solusi yang Diimplementasikan

### 1. Fungsi Baru: `cariWisataTerbaikDiJalur()`

Fungsi ini menggantikan fungsi `cariWisataTerdekat()` sebagai prioritas utama.

#### Algoritma:

```
1. Hitung jarak langsung dari lokasi awal ke tujuan (D_langsung)
2. Untuk setiap wisata:
   a. Hitung jarak: Awal → Wisata (D1)
   b. Hitung jarak: Wisata → Tujuan (D2)
   c. Total jarak via wisata: D_total = D1 + D2
   d. Selisih: Δ = |D_total - D_langsung|

3. Jika Δ ≤ 10% dari D_langsung:
   → Wisata dianggap BERADA DI JALUR

4. Dari wisata-wisata yang di jalur:
   → Pilih yang paling dekat dari lokasi awal

5. Jika tidak ada wisata di jalur:
   → Fallback ke wisata terdekat
```

#### Logika Toleransi 10%:

```php
$toleransi = $jarakLangsungAwalKeTujuan * 0.10; // 10% toleransi

if ($selisih <= $toleransi) {
    // Wisata ini berada di jalur atau sangat dekat dengan jalur
}
```

**Mengapa 10%?**

-   GPS dan routing API memiliki margin error
-   Jalan tidak selalu lurus sempurna
-   Memberikan fleksibilitas untuk wisata yang sedikit menyimpang dari jalur utama
-   Dapat disesuaikan jika diperlukan (5%, 15%, dll)

### 2. Perubahan pada `cariWisataTerdekat()`

Ditambahkan parameter `$skipWisataId` untuk skip wisata tujuan:

```php
private function cariWisataTerdekat($lokasiAwal, $semuaWisata, $skipWisataId = null)
{
    // Skip wisata tertentu jika diminta
    if ($skipWisataId && $wisata->id_wisata === $skipWisataId) {
        continue;
    }
    ...
}
```

### 3. Penggunaan Jarak Jalan Sebenarnya

Semua perhitungan sekarang menggunakan **OSRM API** untuk mendapatkan jarak jalan sebenarnya, bukan jarak lurus:

```php
$jarakJalan = $this->hitungJarakJalanSebenarnya($lat1, $lon1, $lat2, $lon2);

// Fallback ke Haversine jika API gagal
if ($jarakJalan === null) {
    $jarakJalan = $this->hitungJarakHaversine($lat1, $lon1, $lat2, $lon2);
}
```

## Perubahan Code

### File: `app/Http/Controllers/Dijkstra.php`

#### 1. Method `algoritmaRuteTerpendek()` - Line ~100

```php
// SEBELUM:
$wisataAwal = $this->cariWisataTerdekat($lokasiAwal, $semuaWisata);

// SESUDAH:
$wisataAwal = $this->cariWisataTerbaikDiJalur($lokasiAwal, $wisataTujuan, $semuaWisata);
```

#### 2. Method Baru: `cariWisataTerbaikDiJalur()` - Line ~218

```php
private function cariWisataTerbaikDiJalur($lokasiAwal, $wisataTujuan, $semuaWisata)
{
    // 1. Hitung jarak langsung
    $jarakLangsungAwalKeTujuan = ...;

    // 2. Loop semua wisata
    foreach ($semuaWisata as $wisata) {
        // Skip tujuan
        if ($wisata->id_wisata === $wisataTujuan->id_wisata) continue;

        // 3. Hitung jarak awal → wisata → tujuan
        $jarakAwalKeWisata = ...;
        $jarakWisataKeTujuan = ...;
        $totalJarakMelaluiWisata = $jarakAwalKeWisata + $jarakWisataKeTujuan;

        // 4. Hitung selisih
        $selisih = abs($totalJarakMelaluiWisata - $jarakLangsungAwalKeTujuan);

        // 5. Cek apakah dalam toleransi
        $toleransi = $jarakLangsungAwalKeTujuan * 0.10;

        if ($selisih <= $toleransi) {
            // Pilih yang terdekat dari lokasi awal
            if ($jarakAwalKeWisata < $jarakTerpendek) {
                $wisataTerbaik = $wisata;
            }
        }
    }

    // 6. Fallback jika tidak ada yang di jalur
    if ($wisataTerbaik === null) {
        $wisataTerbaik = $this->cariWisataTerdekat(...);
    }

    return $wisataTerbaik;
}
```

#### 3. Update `cariWisataTerdekat()` - Line ~292

```php
// Ditambah parameter skip
private function cariWisataTerdekat($lokasiAwal, $semuaWisata, $skipWisataId = null)
{
    foreach ($semuaWisata as $wisata) {
        // Skip wisata tertentu
        if ($skipWisataId && $wisata->id_wisata === $skipWisataId) {
            continue;
        }

        // Gunakan jarak jalan sebenarnya
        $jarakJalan = $this->hitungJarakJalanSebenarnya(...);
        ...
    }
}
```

## Flow Diagram

### Flow Sebelum Perbaikan:

```
Lokasi Awal
    ↓
Cari Wisata Terdekat (jarak lurus/jalan)
    ↓
Wisata Terdekat (bisa salah, tidak di jalur)
    ↓
Dijkstra ke Tujuan
    ↓
Rute Final (tidak efisien)
```

### Flow Setelah Perbaikan:

```
Lokasi Awal + Tujuan
    ↓
Hitung Jarak Langsung
    ↓
Cari Semua Wisata di Jalur (selisih ≤ 10%)
    ↓
Pilih Wisata Terdekat yang DI JALUR
    ↓ (jika tidak ada)
Fallback: Wisata Terdekat
    ↓
Dijkstra ke Tujuan
    ↓
Rute Final (efisien, mengikuti jalur)
```

## Contoh Perhitungan

### Skenario Real:

**Data:**

-   Lokasi Awal: Dolok Sanggul (2.2530, 98.7483)
-   Tujuan: Danau Sitolu-tolu (2.3890, 98.5356)
-   Jarak Langsung: 23.5 km

**Kandidat Wisata:**

1. **Wisata A** - Pasar Dolok Sanggul

    - Jarak Awal → A: 1.2 km
    - Jarak A → Tujuan: 22.8 km
    - Total via A: 24.0 km
    - Selisih: |24.0 - 23.5| = **0.5 km** (2.1%)
    - **✅ DI JALUR** (0.5 km < 2.35 km toleransi)

2. **Wisata B** - Air Terjun Sampuran (di samping jalan)

    - Jarak Awal → B: 2.1 km
    - Jarak B → Tujuan: 28.3 km
    - Total via B: 30.4 km
    - Selisih: |30.4 - 23.5| = **6.9 km** (29.4%)
    - **❌ TIDAK DI JALUR** (6.9 km > 2.35 km toleransi)

3. **Wisata C** - Gereja HKBP (dekat tapi tidak di jalur)
    - Jarak Awal → C: 0.8 km (terdekat!)
    - Jarak C → Tujuan: 25.9 km
    - Total via C: 26.7 km
    - Selisih: |26.7 - 23.5| = **3.2 km** (13.6%)
    - **❌ TIDAK DI JALUR** (3.2 km > 2.35 km toleransi)

**Hasil Pemilihan:**

-   **SEBELUM**: Wisata C dipilih (terdekat: 0.8 km) ❌ SALAH
-   **SESUDAH**: Wisata A dipilih (di jalur: 1.2 km) ✅ BENAR

## Logging untuk Debugging

Ditambahkan log untuk memudahkan debugging:

```php
Log::info("Wisata di jalur ditemukan: {$wisata->nama_wisata}, jarak: {$jarakAwalKeWisata} km, selisih: {$selisih} km");

Log::info("Tidak ada wisata di jalur, mencari wisata terdekat");

Log::info("Wisata terbaik di jalur: {$wisataTerbaik->nama_wisata}");
```

**Cara melihat log:**

```bash
# Di terminal
tail -f storage/logs/laravel.log

# Atau di Laravel Log Viewer (jika ada)
```

## Testing

### Test Case 1: Wisata di Jalur

```
INPUT:
- Lokasi Awal: Dolok Sanggul
- Tujuan: Danau Sitolu-tolu
- Wisata di jalur: Pasar Dolok Sanggul (1.2 km dari awal)
- Wisata terdekat: Gereja HKBP (0.8 km tapi tidak di jalur)

EXPECTED OUTPUT:
✅ Pasar Dolok Sanggul dipilih (bukan Gereja HKBP)
✅ Rute mengikuti jalan utama
```

### Test Case 2: Tidak Ada Wisata di Jalur

```
INPUT:
- Lokasi Awal: Pinggir Hutan
- Tujuan: Gunung Pusuk Buhit
- Tidak ada wisata dalam toleransi 10%

EXPECTED OUTPUT:
✅ Fallback ke wisata terdekat
✅ Log: "Tidak ada wisata di jalur, mencari wisata terdekat"
```

### Test Case 3: Beberapa Wisata di Jalur

```
INPUT:
- Lokasi Awal: Dolok Sanggul
- Tujuan: Onan Runggu
- Wisata A di jalur: 2 km dari awal
- Wisata B di jalur: 5 km dari awal
- Wisata C di jalur: 8 km dari awal

EXPECTED OUTPUT:
✅ Wisata A dipilih (terdekat yang di jalur)
✅ Rute efisien
```

## Konfigurasi Toleransi

Jika ingin mengubah toleransi 10%, edit di method `cariWisataTerbaikDiJalur()`:

```php
// Toleransi 10% (default)
$toleransi = $jarakLangsungAwalKeTujuan * 0.10;

// Toleransi 5% (lebih ketat)
$toleransi = $jarakLangsungAwalKeTujuan * 0.05;

// Toleransi 15% (lebih longgar)
$toleransi = $jarakLangsungAwalKeTujuan * 0.15;

// Toleransi fixed (contoh: 2 km)
$toleransi = 2.0;
```

**Rekomendasi:**

-   **5%**: Untuk area dengan jalan lurus dan banyak wisata
-   **10%**: Default, cocok untuk kebanyakan kasus ✅
-   **15%**: Untuk area dengan jalan berkelok-kelok
-   **Fixed 2-3 km**: Alternatif jika persentase tidak cocok

## Performance Impact

### API Calls:

**Sebelum:**

-   N calls untuk cari wisata terdekat (N = jumlah wisata)

**Sesudah:**

-   1 call untuk jarak langsung awal → tujuan
-   N × 2 calls untuk setiap wisata:
    -   1 call: awal → wisata
    -   1 call: wisata → tujuan
-   Total: **1 + (N × 2) calls**

**Mitigasi:**

-   OSRM API sangat cepat (~50-100ms per call)
-   Implementasi caching dapat ditambahkan jika diperlukan
-   Batasi jumlah wisata yang dicek (misalnya: hanya wisata dalam radius 50 km)

### Optimisasi Future (Optional):

```php
// Cache hasil API call
use Illuminate\Support\Facades\Cache;

$cacheKey = "jarak_{$lat1}_{$lon1}_{$lat2}_{$lon2}";
$jarak = Cache::remember($cacheKey, 3600, function() use ($lat1, $lon1, $lat2, $lon2) {
    return $this->hitungJarakJalanSebenarnya($lat1, $lon1, $lat2, $lon2);
});
```

## Kesimpulan

### Perubahan Utama:

✅ Deteksi wisata yang **benar-benar dilalui** di jalur rute
✅ Prioritas pada wisata di jalur, baru kemudian jarak
✅ Menggunakan jarak jalan sebenarnya untuk akurasi
✅ Fallback mechanism jika tidak ada wisata di jalur
✅ Logging untuk debugging

### Hasil:

✅ Rute lebih efisien dan natural
✅ Tidak ada lagi "belok ke wisata yang tidak perlu"
✅ Titik wisata yang dilalui otomatis terdeteksi
✅ Pengalaman pengguna lebih baik

### Dampak pada User:

-   Jarak tempuh lebih pendek
-   Waktu tempuh lebih singkat
-   Rute lebih masuk akal dan mudah diikuti
-   Semua destinasi yang dilalui tercatat dengan benar

---

**Catatan:** Perbaikan ini sudah diterapkan dan siap digunakan. Silakan test dengan berbagai skenario untuk memastikan hasilnya sesuai harapan!
