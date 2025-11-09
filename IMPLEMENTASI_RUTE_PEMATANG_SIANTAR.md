# Implementasi Rute Via Pematang Siantar

## Ringkasan

Implementasi fitur rute alternatif via Pematang Siantar yang menampilkan opsi rute tambahan pada sistem pariwisata, mirip dengan cara Google Maps menampilkan rute alternatif (via Berastagi dan via Pematang Siantar).

## Koordinat Pematang Siantar

-   Latitude: `2.9676002181287195`
-   Longitude: `99.06843670021658`

## Perubahan Backend

### File: `app/Http/Controllers/Dijkstra.php`

#### 1. Method Baru: `buatRuteViaPematangSiantar()`

Ditambahkan method baru untuk membuat rute yang melewati Pematang Siantar sebagai waypoint:

```php
private function buatRuteViaPematangSiantar($wisataAwal, $wisataTujuan, $nomorRute, $jarakKeWisataAwal = 0)
```

**Fitur:**

-   Menghitung jarak dari wisata awal ke Pematang Siantar
-   Menghitung jarak dari Pematang Siantar ke tujuan
-   Menggunakan OSRM API untuk rute jalan sebenarnya, dengan fallback ke Haversine
-   Mengembalikan data rute lengkap dengan informasi transit
-   Menambahkan flag khusus `via_pematang_siantar` untuk identifikasi

#### 2. Update Method: `cariSemuaRuteAlternatif()`

Ditambahkan logika untuk memasukkan rute via Pematang Siantar ke dalam array rute alternatif:

```php
// Tambahkan rute via Pematang Siantar sebagai alternatif
$ruteViaPematangSiantar = $this->buatRuteViaPematangSiantar($wisataAwal, $wisataTujuan, count($ruteAlternatif) + 1, $jarakKeWisataAwal);
if ($ruteViaPematangSiantar && !$this->ruteUdahAda($ruteViaPematangSiantar['jalur'], $ruteAlternatif)) {
    $ruteAlternatif[] = $ruteViaPematangSiantar;
}
```

## Perubahan Frontend

### File: `resources/views/pengunjung/hasil-rute.blade.php`

#### 1. Tombol Rute Alternatif Baru

Ditambahkan tombol ketiga untuk rute via Pematang Siantar:

```blade
<button class="btn btn-rute-alternatif btn-outline-warning btn-sm"
    id="btnRute3" data-rute="3" style="display: none;">
    <i class="fas fa-route"></i> Rute via Pematang Siantar
</button>
```

**Catatan:** Tombol disembunyikan secara default dan hanya ditampilkan jika rute via Pematang Siantar tersedia.

#### 2. Variabel Global JavaScript

Ditambahkan variabel baru untuk menangani rute ketiga:

```javascript
let garisRute3 = null; // Garis untuk rute 3 (via Pematang Siantar)
let infoRute3 = null; // Info jarak dan waktu untuk rute 3
```

#### 3. Event Handler

Ditambahkan event handler untuk tombol rute 3:

```javascript
$("#btnRute3").click(function () {
    pilihRuteAlternatif(3);
});
```

#### 4. Update Function: `pilihRuteAlternatif()`

Ditambahkan logika untuk menangani pemilihan rute 3:

```javascript
if (nomorRute === 3 && !garisRute3) {
    console.log("Data rute 3 belum tersedia, menunggu...");
    $("#infoRuteAktif").html(
        '<i class="fas fa-spinner fa-spin text-warning"></i> Memuat rute 3...'
    );
    setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
    return;
}
```

```javascript
else if (nomorRute === 3) {
    $('#btnRute3').addClass('active btn-warning').removeClass('btn-outline-warning');
    $('#infoRuteAktif').html('<i class="fas fa-route text-warning"></i> Rute 3 (Via Pematang Siantar) sedang aktif');
}
```

#### 5. Update Function: `tampilkanRuteAktif()`

Ditambahkan logika untuk menampilkan/menyembunyikan garis rute 3:

```javascript
if (garisRute3 && peta.hasLayer(garisRute3)) {
    peta.removeLayer(garisRute3);
}
```

```javascript
else if (ruteAktif === 3 && garisRute3) {
    garisRute3.addTo(peta);
}
```

#### 6. Update Function: `updateInfoRute()`

Ditambahkan logika untuk menampilkan informasi jarak dan waktu rute 3:

```javascript
else if (ruteAktif === 3) {
    // Rute 3 (Via Pematang Siantar) - gunakan data dari API jika tersedia
    // ... (logika update jarak dan waktu)
}
```

#### 7. Pembuatan Rute 3 di Peta

Ditambahkan logika untuk menggambar rute via Pematang Siantar di peta:

```javascript
// RUTE 3: Via Pematang Siantar
const pematangSiantar = {
    lat: 2.9676002181287195,
    lng: 99.06843670021658,
    nama: "Pematang Siantar",
};

const ruteViaPematang = semuaRuteAlternatif.find(
    (rute) => rute.via_pematang_siantar === true
);

if (ruteViaPematang) {
    // Tampilkan tombol dan legend
    $("#btnRute3").show();
    $("#legendRute3").show();

    // Gambar rute dari lokasi awal ke Pematang Siantar
    // Gambar rute dari Pematang Siantar ke tujuan
    // Tambahkan marker untuk Pematang Siantar
    // Hitung total jarak dan waktu
}
```

#### 8. Update Legend Peta

Ditambahkan item legend untuk rute 3:

```blade
<div class="legend-item mb-2" id="legendRute3" style="display: none;">
    <div class="legend-line me-2"
        style="background-color: #ffc107; height: 3px; width: 20px;"></div>
    <small>Jalur Rute 3 (Via Pematang Siantar)</small>
</div>
```

## Warna Rute

-   **Rute 1 (Transit)**: Biru (#007bff)
-   **Rute 2 (Langsung)**: Hijau (#28a745)
-   **Rute 3 (Via Pematang Siantar)**: Kuning/Orange (#ffc107)

## Fitur Utama

### 1. Perhitungan Rute Otomatis

-   Sistem secara otomatis menghitung rute via Pematang Siantar
-   Menggunakan OSRM API untuk mendapatkan rute jalan sebenarnya
-   Fallback ke Haversine jika API gagal

### 2. Tampilan Visual

-   Rute ditampilkan dengan warna kuning/orange (#ffc107)
-   Marker khusus untuk titik transit Pematang Siantar
-   Garis rute mengikuti jalan sebenarnya (tidak garis lurus)

### 3. Informasi Detail

-   Jarak total rute (km)
-   Estimasi waktu tempuh
-   Jumlah transit (1 - Pematang Siantar)
-   Semua destinasi yang dilalui

### 4. Interaktivitas

-   Tombol untuk beralih antar rute
-   Hover tooltip dengan informasi detail
-   Popup marker dengan nama lokasi

### 5. Tabel Rute Alternatif

-   Rute via Pematang Siantar akan muncul di tabel jika tersedia
-   Menampilkan badge "1 Transit"
-   Informasi jarak dan waktu yang akurat

## Alur Kerja

1. **Backend**: Saat user mencari rute, sistem menghitung:

    - Rute 1: Rute terpendek dengan transit (Dijkstra)
    - Rute 2: Rute langsung tanpa transit
    - Rute 3: Rute via Pematang Siantar (jika ada)

2. **Frontend**: Saat halaman hasil rute dimuat:

    - Cek apakah ada rute via Pematang Siantar
    - Jika ada, tampilkan tombol dan legend
    - Gambar semua rute di peta (default: rute 1 aktif)

3. **User Interaction**: User dapat:
    - Klik tombol rute untuk beralih tampilan
    - Klik row di tabel untuk melihat rute tertentu
    - Hover di tabel untuk highlight rute di peta

## Testing

### Cara Testing:

1. Akses halaman pencarian rute
2. Pilih lokasi awal (misal: Dolok Sanggul atau lokasi saat ini)
3. Pilih destinasi tujuan
4. Klik "Cari Rute"
5. Pada halaman hasil rute:
    - Periksa apakah tombol "Rute via Pematang Siantar" muncul
    - Klik tombol tersebut untuk melihat rute
    - Periksa jarak dan waktu tempuh
    - Periksa apakah garis rute muncul di peta
    - Periksa apakah marker Pematang Siantar muncul

### Expected Result:

-   Tombol "Rute via Pematang Siantar" muncul jika rute tersedia
-   Rute ditampilkan dengan warna kuning/orange
-   Marker Pematang Siantar muncul sebagai titik transit
-   Jarak dan waktu ditampilkan dengan benar
-   Rute mengikuti jalan sebenarnya (bukan garis lurus)
-   Tabel rute alternatif menampilkan rute via Pematang Siantar

## Catatan Penting

1. **Availability**: Rute via Pematang Siantar hanya akan muncul jika perhitungan backend berhasil
2. **API Dependency**: Menggunakan OSRM API untuk routing, pastikan koneksi internet tersedia
3. **Performance**: Perhitungan rute mungkin membutuhkan waktu beberapa detik
4. **Fallback**: Jika API gagal, sistem akan menggunakan perhitungan Haversine (jarak lurus)
5. **Mobile Friendly**: Tombol dan tampilan responsive untuk perangkat mobile

## Referensi

-   GEMINI.md - Spesifikasi requirement original
-   Google Maps - Inspirasi tampilan rute alternatif

## Tanggal Implementasi

9 November 2025
