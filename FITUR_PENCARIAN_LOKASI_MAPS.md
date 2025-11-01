# Fitur Pencarian Lokasi di Maps

## Deskripsi

Fitur ini memungkinkan pengguna untuk mencari lokasi awal perjalanan dengan cara mengetik nama lokasi (bukan hanya memilih dari daftar yang sudah tersedia). Sistem akan mencari lokasi tersebut menggunakan OpenStreetMap Nominatim API dan mendapatkan koordinat latitude dan longitude-nya.

## Cara Penggunaan

### 1. Pilih Tipe Lokasi Awal

Pada halaman "Cari Rute", pengguna akan melihat dropdown "Tipe Lokasi" dengan 2 pilihan:

-   **Lokasi Tersedia (GPS/Daftar Wisata)**: Menggunakan lokasi dari daftar yang sudah tersedia (GPS saat ini, Pusat Dolok Sanggul, atau destinasi wisata yang ada di database)
-   **Cari Lokasi di Maps**: Mencari lokasi bebas dengan mengetik nama lokasi

### 2. Mencari Lokasi di Maps

#### Langkah-langkah:

1. Pilih "Cari Lokasi di Maps" dari dropdown "Tipe Lokasi"
2. Form pencarian akan muncul
3. Ketik nama lokasi yang diinginkan (contoh: "Pasar Dolok Sanggul", "Hotel Aek Rangat", dll)
4. Tekan Enter atau klik tombol "Cari"
5. Sistem akan menampilkan daftar hasil pencarian
6. Klik salah satu hasil untuk memilih lokasi tersebut
7. Koordinat latitude dan longitude akan otomatis terisi
8. Lokasi terpilih akan ditampilkan dengan marker merah di peta

#### Contoh Pencarian:

-   "Pasar Dolok Sanggul"
-   "Hotel Aek Rangat"
-   "Gereja HKBP Dolok Sanggul"
-   "Terminal Dolok Sanggul"
-   "Kantor Bupati Humbang Hasundutan"

### 3. Hasil Pencarian

Setiap hasil pencarian akan menampilkan:

-   **Nama Lokasi**: Nama singkat/ringkas dari lokasi
-   **Alamat Lengkap**: Alamat detail dari OpenStreetMap
-   **Koordinat**: Latitude dan Longitude dengan 6 digit desimal

### 4. Memilih Lokasi

Setelah mengklik salah satu hasil:

-   Lokasi akan ditandai sebagai "Lokasi Terpilih" dengan alert hijau
-   Marker merah akan muncul di peta pada posisi lokasi tersebut
-   Peta akan otomatis zoom ke lokasi yang dipilih
-   Koordinat akan tersimpan dan siap untuk pencarian rute

### 5. Mengganti Lokasi

Jika ingin mengganti lokasi yang sudah dipilih:

-   Klik tombol "Ganti" pada alert lokasi terpilih
-   Form pencarian akan direset dan siap untuk pencarian baru

## Teknologi yang Digunakan

### 1. OpenStreetMap Nominatim API

-   **Endpoint**: `https://nominatim.openstreetmap.org/search`
-   **Format**: JSON
-   **Fitur**:
    -   Geocoding (nama lokasi → koordinat)
    -   Reverse Geocoding (koordinat → nama lokasi)
    -   Dukungan untuk alamat detail

### 2. Search Strategy

Sistem menggunakan strategi pencarian 2 tingkat:

#### Level 1: Pencarian Lokal (Prioritas)

```
Query: {input} + ", Humbang Hasundutan, Sumatera Utara, Indonesia"
ViewBox: 98.3,2.0,98.8,2.5 (Area Kabupaten Humbahas)
```

#### Level 2: Pencarian Regional (Fallback)

Jika tidak ditemukan di level 1:

```
Query: {input} + ", Sumatera Utara, Indonesia"
```

### 3. Frontend Technologies

-   **jQuery**: Untuk AJAX dan DOM manipulation
-   **Leaflet.js**: Untuk menampilkan marker di peta
-   **Bootstrap 5**: Untuk styling dan responsive design

## Perubahan pada Code

### 1. File: `resources/views/pengunjung/cari-rute.blade.php`

#### Penambahan HTML:

-   Dropdown "Tipe Lokasi" untuk memilih antara predefined atau search
-   Form pencarian lokasi dengan input text dan tombol "Cari"
-   Container untuk menampilkan hasil pencarian (list)
-   Alert untuk menampilkan lokasi yang dipilih
-   Hidden inputs untuk menyimpan koordinat dan nama lokasi custom

#### Penambahan JavaScript Functions:

-   `cariLokasiDiMaps()`: Memanggil Nominatim API
-   `cariLokasiGlobal()`: Fallback search jika tidak ditemukan lokal
-   `tampilkanHasilPencarian()`: Render hasil pencarian ke HTML
-   `pilihLokasiDariPencarian()`: Handle pemilihan lokasi dari hasil
-   `resetFormPencarian()`: Reset form ke state awal

#### Event Handlers:

-   Change handler untuk dropdown tipe lokasi
-   Click handler untuk tombol pencarian
-   Enter key handler pada input pencarian
-   Click handler untuk setiap item hasil pencarian
-   Submit validation untuk form

### 2. File: `app/Http/Controllers/Dijkstra.php`

#### Method: `cariRuteTerpendek()`

Ditambahkan logika untuk mendeteksi tipe lokasi:

```php
// Tentukan tipe lokasi (predefined atau search)
$tipeLokasiAwal = $request->tipe_lokasi ?? 'predefined';

// Dapatkan koordinat lokasi awal yang sebenarnya berdasarkan tipe
if ($tipeLokasiAwal === 'search') {
    // Lokasi dari pencarian maps
    $lokasiAwal = $koordinatPengguna;
    $namaLokasiAwal = $request->nama_lokasi_custom ?? 'Lokasi Pencarian';
} else {
    // Lokasi dari dropdown (predefined)
    $request->validate(['lokasi_awal' => 'required']);
    $lokasiAwal = $this->dapatkanKoordinatLokasiAwal($request->lokasi_awal, $koordinatPengguna);
    $namaLokasiAwal = $this->tentukanNamaLokasiAwal($request->lokasi_awal);
}
```

#### Perubahan Validasi:

-   `lokasi_awal` tidak lagi required secara global
-   Validasi conditional berdasarkan `tipe_lokasi`
-   Menambahkan support untuk `nama_lokasi_custom`

## Flow Diagram

```
1. User Memilih Tipe Lokasi
   ↓
2a. [Predefined] → Pilih dari Dropdown → Set Koordinat
   ↓
2b. [Search] → Ketik Nama Lokasi → Cari di Maps
   ↓
3. Nominatim API Returns Results
   ↓
4. User Memilih Salah Satu Hasil
   ↓
5. Koordinat & Nama Tersimpan
   ↓
6. Marker Ditampilkan di Peta
   ↓
7. User Pilih Lokasi Tujuan
   ↓
8. Submit Form → Algoritma Dijkstra
   ↓
9. Tampilkan Hasil Rute
```

## Validasi Form

### Client-Side Validation (JavaScript):

1. **Tipe Predefined**:

    - Lokasi awal harus dipilih
    - Koordinat latitude & longitude harus terisi

2. **Tipe Search**:

    - Lokasi harus dicari dan dipilih
    - Koordinat latitude & longitude harus terisi
    - Nama lokasi custom harus terisi

3. **Lokasi Tujuan**:
    - Harus dipilih (required untuk semua tipe)

### Server-Side Validation (Laravel):

```php
$request->validate([
    'lokasi_tujuan' => 'required',
    'latitude' => 'required|numeric',
    'longitude' => 'required|numeric'
]);

// Conditional validation
if ($tipeLokasiAwal === 'predefined') {
    $request->validate(['lokasi_awal' => 'required']);
}
```

## User Experience Improvements

### 1. Visual Feedback

-   Loading spinner saat mencari lokasi
-   Alert success saat lokasi dipilih
-   Marker berwarna merah untuk lokasi pencarian
-   Highlight pada item hasil pencarian saat hover
-   Auto-zoom peta ke lokasi yang dipilih

### 2. Error Handling

-   Pesan error jika API gagal
-   Pesan info jika tidak ada hasil
-   Validasi form sebelum submit
-   Fallback search jika pencarian lokal gagal

### 3. Responsive Design

-   Form responsive untuk mobile dan desktop
-   Hasil pencarian dengan scroll untuk banyak item
-   Button states (disabled, loading, active)

## Contoh Penggunaan

### Skenario 1: Mencari dari Hotel

```
1. Pilih: "Cari Lokasi di Maps"
2. Ketik: "Hotel Aek Rangat"
3. Pilih dari hasil pencarian
4. Pilih tujuan: "Danau Sitolu-tolu"
5. Submit → Dapatkan rute terpendek
```

### Skenario 2: Mencari dari Pasar

```
1. Pilih: "Cari Lokasi di Maps"
2. Ketik: "Pasar Dolok Sanggul"
3. Pilih dari hasil pencarian
4. Pilih tujuan: "Air Terjun Sampuran Na Pitu"
5. Submit → Dapatkan rute terpendek
```

## Limitasi & Catatan

### 1. API Rate Limit

Nominatim API memiliki rate limit:

-   Maximum 1 request per second
-   Tidak untuk penggunaan komersial intensif
-   Untuk production, pertimbangkan self-hosting Nominatim

### 2. Akurasi Pencarian

-   Hasil tergantung pada data OpenStreetMap
-   Beberapa lokasi mungkin tidak tercatat
-   Nama lokasi harus cukup spesifik

### 3. Coverage Area

-   Prioritas pencarian di area Humbang Hasundutan
-   Fallback ke Sumatera Utara jika tidak ditemukan
-   Bisa diperluas ke Indonesia jika diperlukan

## Future Improvements

1. **Autocomplete**: Tambahkan autocomplete saat mengetik
2. **Recent Searches**: Simpan riwayat pencarian pengguna
3. **Popular Locations**: Tampilkan lokasi populer sebagai quick select
4. **Geofencing**: Batasi pencarian hanya di area Humbahas
5. **Offline Support**: Cache hasil pencarian untuk offline mode
6. **Custom POI**: Izinkan admin menambah Point of Interest khusus

## Troubleshooting

### Masalah: Pencarian tidak menemukan lokasi

**Solusi**:

-   Coba kata kunci yang lebih spesifik
-   Tambahkan konteks (contoh: "Pasar + nama desa")
-   Gunakan bahasa Indonesia atau lokal

### Masalah: Koordinat tidak akurat

**Solusi**:

-   OpenStreetMap data mungkin belum update
-   Verifikasi lokasi di peta sebelum submit
-   Gunakan opsi GPS jika berada di lokasi

### Masalah: API tidak response

**Solusi**:

-   Cek koneksi internet
-   Tunggu beberapa detik (rate limit)
-   Refresh halaman dan coba lagi

## Kesimpulan

Fitur pencarian lokasi di maps memberikan fleksibilitas lebih kepada pengguna untuk menentukan titik awal perjalanan mereka. Dengan integrasi OpenStreetMap Nominatim API, sistem dapat melakukan geocoding secara real-time dan memberikan hasil yang akurat untuk area Humbang Hasundutan dan sekitarnya.

Fitur ini melengkapi opsi lokasi yang sudah ada (GPS dan daftar wisata) dan membuat sistem pencarian rute menjadi lebih powerful dan user-friendly.
