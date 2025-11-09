<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Wisata;
use App\Models\Rute;

class Dijkstra extends Controller
{
    public function halamanCariRute(Request $request) {
        $wisata = Wisata::all();
        $tujuanId = $request->query('tujuan'); // Ambil parameter tujuan dari URL
        return view('pengunjung.cari-rute', compact('wisata', 'tujuanId'));
    }

    public function cariRuteTerpendek(Request $request)
    {
        // Tingkatkan execution time limit untuk algoritma yang kompleks
        set_time_limit(120); // 2 menit

        // Validasi input
        $request->validate([
            'lokasi_tujuan' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $koordinatPengguna = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

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

        $wisataTujuan = Wisata::findOrFail($request->lokasi_tujuan);

        // Cari rute terpendek menggunakan algoritma Dijkstra
        $hasilRute = $this->algoritmaRuteTerpendek($lokasiAwal, $wisataTujuan);

        return view('pengunjung.hasil-rute', compact('hasilRute', 'lokasiAwal', 'namaLokasiAwal', 'wisataTujuan'));
    }

    private function tentukanNamaLokasiAwal($lokasiAwal)
    {
        switch ($lokasiAwal) {
            case 'current':
                return 'Lokasi Anda Saat Ini';
            case 'dolok_sanggul':
                return 'Pusat Dolok Sanggul';
            default:
                // Jika memilih wisata sebagai lokasi awal
                if (is_numeric($lokasiAwal)) {
                    $wisata = Wisata::find($lokasiAwal);
                    return $wisata ? $wisata->nama_wisata : 'Lokasi Tidak Diketahui';
                }
                return 'Lokasi Tidak Diketahui';
        }
    }

    /**
     * Mendapatkan koordinat lokasi awal berdasarkan pilihan pengguna
     */
    private function dapatkanKoordinatLokasiAwal($lokasiAwal, $koordinatPengguna)
    {
        switch ($lokasiAwal) {
            case 'current':
                return $koordinatPengguna;
            case 'dolok_sanggul':
                return [
                    'latitude' => 2.252977,
                    'longitude' => 98.748272
                ];
            default:
                // Jika memilih wisata sebagai lokasi awal
                if (is_numeric($lokasiAwal)) {
                    $wisata = Wisata::find($lokasiAwal);
                    if ($wisata) {
                        return [
                            'latitude' => $wisata->latitude,
                            'longitude' => $wisata->longitude
                        ];
                    }
                }
                return $koordinatPengguna; // Fallback ke koordinat pengguna
        }
    }

    private function algoritmaRuteTerpendek($lokasiAwal, $wisataTujuan)
    {
        // Ambil semua data wisata dan rute
        $semuaWisata = Wisata::all();
        $semuaRute = Rute::with(['lokasiAsal', 'lokasiTujuan'])->get();

        // Buat graf dari data rute
        $graf = $this->buatGraf($semuaWisata, $semuaRute);

        // PERBAIKAN: Cari wisata yang berada di jalur rute langsung terlebih dahulu
        $wisataAwal = $this->cariWisataTerbaikDiJalur($lokasiAwal, $wisataTujuan, $semuaWisata);

        // Hitung jarak dari lokasi awal ke wisata terdekat menggunakan jalan sebenarnya
        $jarakKeWisataAwal = $this->hitungJarakJalanSebenarnya(
            $lokasiAwal['latitude'], $lokasiAwal['longitude'],
            $wisataAwal->latitude, $wisataAwal->longitude
        );

        if ($jarakKeWisataAwal === null) {
            $jarakKeWisataAwal = $this->hitungJarakHaversine(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisataAwal->latitude, $wisataAwal->longitude
            );
        }

        // Cari SEMUA RUTE ALTERNATIF menggunakan modified Dijkstra
        $semuaRuteAlternatif = $this->cariSemuaRuteAlternatif($graf, $semuaWisata, $wisataAwal, $wisataTujuan, $jarakKeWisataAwal);

        // Ambil rute terbaik (terpendek) sebagai rute utama
        $ruteTerbaik = !empty($semuaRuteAlternatif) ? $semuaRuteAlternatif[0] : null;

        // Jika tidak ada rute dalam database, buat rute langsung
        if (empty($semuaRuteAlternatif)) {
            $jarakLangsung = $this->hitungJarakJalanSebenarnya(
                $wisataAwal->latitude, $wisataAwal->longitude,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );

            if ($jarakLangsung === null) {
                $jarakLangsung = $this->hitungJarakHaversine(
                    $wisataAwal->latitude, $wisataAwal->longitude,
                    $wisataTujuan->latitude, $wisataTujuan->longitude
                );
            }

            $ruteTerbaik = [
                'jalur' => [$wisataAwal->id_wisata, $wisataTujuan->id_wisata],
                'jarak_rute' => $jarakLangsung,
                'waktu_rute' => $this->estimasiWaktuTempuh($jarakLangsung),
                'jumlah_transit' => 0,
                'wisata_transit' => []
            ];

            $semuaRuteAlternatif = [$ruteTerbaik];
        }

        // Jarak ke wisata awal sudah dihitung di atas, hapus duplikasi
        // Hitung jarak dari lokasi awal ke wisata terdekat (sudah dihitung sebelumnya)
        // $jarakKeWisataAwal sudah tersedia dari perhitungan sebelumnya

        // Total jarak = jarak dari lokasi awal (pengguna/Pusat Dolok Sanggul/wisata) ke wisata terdekat + jarak rute wisata
        $totalJarak = $jarakKeWisataAwal + $ruteTerbaik['jarak_rute'];

        return [
            'jalur' => $ruteTerbaik['jalur'],
            'jarak_total' => $totalJarak,
            'waktu_tempuh' => $this->estimasiWaktuTempuh($totalJarak),
            'wisata_awal' => $wisataAwal,
            'jarak_ke_wisata_awal' => $jarakKeWisataAwal,
            'jarak_rute_wisata' => $ruteTerbaik['jarak_rute'],
            'semua_rute_alternatif' => $semuaRuteAlternatif  // Data rute alternatif untuk ditampilkan
        ];
    }

    private function buatGraf($semuaWisata, $semuaRute)
    {
        $graf = [];

        foreach ($semuaRute as $rute) {
            // Konversi jarak dari string ke angka
            $jarakNumerik = $this->konversiJarakKeAngka($rute->jarak);

            // Graf dua arah (bisa pergi dan pulang)
            $graf[$rute->lokasi_asal][] = [
                'tujuan' => $rute->lokasi_tujuan,
                'jarak' => $jarakNumerik
            ];
            $graf[$rute->lokasi_tujuan][] = [
                'tujuan' => $rute->lokasi_asal,
                'jarak' => $jarakNumerik
            ];
        }

        return $graf;
    }

    /**
     * Konversi string jarak seperti "19 km" atau "23,2 km" menjadi angka
     */
    private function konversiJarakKeAngka($jarakString)
    {
        // Hapus "km" dan spasi, lalu konversi koma menjadi titik untuk desimal
        $jarakBersih = str_replace(['km', ' ', ','], ['', '', '.'], strtolower(trim($jarakString)));

        // Konversi ke float
        $jarakAngka = floatval($jarakBersih);

        // Jika konversi gagal, return 0
        return $jarakAngka > 0 ? $jarakAngka : 0;
    }

    /**
     * Cari wisata terbaik yang berada di jalur rute
     * Prioritas: wisata yang dilalui di jalur rute langsung ke tujuan
     * OPTIMIZED: Menggunakan Haversine untuk filter awal, lalu OSRM untuk kandidat terbaik
     */
    private function cariWisataTerbaikDiJalur($lokasiAwal, $wisataTujuan, $semuaWisata)
    {
        // STEP 1: Hitung jarak lurus (Haversine) untuk filter cepat
        $jarakLurusAwalKeTujuan = $this->hitungJarakHaversine(
            $lokasiAwal['latitude'], $lokasiAwal['longitude'],
            $wisataTujuan->latitude, $wisataTujuan->longitude
        );

        $kandidatWisata = [];

        // STEP 2: Filter wisata menggunakan Haversine (cepat, tanpa API call)
        foreach ($semuaWisata as $wisata) {
            // Skip jika wisata adalah tujuan akhir
            if ($wisata->id_wisata === $wisataTujuan->id_wisata) {
                continue;
            }

            // Hitung jarak lurus
            $jarakLurusAwalKeWisata = $this->hitungJarakHaversine(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisata->latitude, $wisata->longitude
            );

            $jarakLurusWisataKeTujuan = $this->hitungJarakHaversine(
                $wisata->latitude, $wisata->longitude,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );

            $totalJarakLurus = $jarakLurusAwalKeWisata + $jarakLurusWisataKeTujuan;
            $selisihLurus = abs($totalJarakLurus - $jarakLurusAwalKeTujuan);

            // Filter: hanya wisata dengan selisih <= 15% (lebih longgar untuk filter awal)
            $toleransiFilter = $jarakLurusAwalKeTujuan * 0.15;

            if ($selisihLurus <= $toleransiFilter) {
                $kandidatWisata[] = [
                    'wisata' => $wisata,
                    'jarak_lurus' => $jarakLurusAwalKeWisata,
                    'selisih_lurus' => $selisihLurus
                ];
            }
        }

        // STEP 3: Jika tidak ada kandidat, gunakan wisata terdekat
        if (empty($kandidatWisata)) {
            Log::info("Tidak ada wisata di jalur (filter Haversine), mencari wisata terdekat");
            return $this->cariWisataTerdekatCepat($lokasiAwal, $semuaWisata, $wisataTujuan->id_wisata);
        }

        // STEP 4: Sort kandidat berdasarkan jarak lurus (terdekat dulu)
        usort($kandidatWisata, function($a, $b) {
            return $a['jarak_lurus'] <=> $b['jarak_lurus'];
        });

        // STEP 5: Ambil maksimal 5 kandidat teratas untuk validasi dengan OSRM
        $kandidatTeratas = array_slice($kandidatWisata, 0, 5);

        Log::info("Filter kandidat: " . count($kandidatWisata) . " wisata, akan validasi " . count($kandidatTeratas) . " teratas dengan OSRM");

        // STEP 6: Validasi dengan OSRM (hanya untuk kandidat terpilih)
        $jarakJalanAwalKeTujuan = $this->hitungJarakJalanSebenarnya(
            $lokasiAwal['latitude'], $lokasiAwal['longitude'],
            $wisataTujuan->latitude, $wisataTujuan->longitude
        );

        if ($jarakJalanAwalKeTujuan === null) {
            $jarakJalanAwalKeTujuan = $jarakLurusAwalKeTujuan;
        }

        $wisataTerbaik = null;
        $jarakTerpendek = PHP_FLOAT_MAX;

        foreach ($kandidatTeratas as $kandidat) {
            $wisata = $kandidat['wisata'];

            // Hitung jarak jalan dari awal ke wisata
            $jarakJalanAwalKeWisata = $this->hitungJarakJalanSebenarnya(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisata->latitude, $wisata->longitude
            );

            if ($jarakJalanAwalKeWisata === null) {
                $jarakJalanAwalKeWisata = $kandidat['jarak_lurus'];
            }

            // Hitung jarak jalan dari wisata ke tujuan
            $jarakJalanWisataKeTujuan = $this->hitungJarakJalanSebenarnya(
                $wisata->latitude, $wisata->longitude,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );

            if ($jarakJalanWisataKeTujuan === null) {
                $jarakJalanWisataKeTujuan = $this->hitungJarakHaversine(
                    $wisata->latitude, $wisata->longitude,
                    $wisataTujuan->latitude, $wisataTujuan->longitude
                );
            }

            $totalJarakJalan = $jarakJalanAwalKeWisata + $jarakJalanWisataKeTujuan;
            $selisihJalan = abs($totalJarakJalan - $jarakJalanAwalKeTujuan);

            // Toleransi final 10% dari jarak jalan
            $toleransiFinal = $jarakJalanAwalKeTujuan * 0.10;

            if ($selisihJalan <= $toleransiFinal && $jarakJalanAwalKeWisata < $jarakTerpendek) {
                $jarakTerpendek = $jarakJalanAwalKeWisata;
                $wisataTerbaik = $wisata;

                Log::info("Wisata di jalur: {$wisata->nama_wisata}, jarak: " . number_format($jarakJalanAwalKeWisata, 2) . " km, selisih: " . number_format($selisihJalan, 2) . " km");
            }
        }

        // STEP 7: Fallback ke kandidat terdekat jika tidak ada yang lolos validasi
        if ($wisataTerbaik === null) {
            $wisataTerbaik = $kandidatTeratas[0]['wisata'];
            Log::info("Menggunakan kandidat terdekat: {$wisataTerbaik->nama_wisata}");
        } else {
            Log::info("Wisata terbaik di jalur: {$wisataTerbaik->nama_wisata}");
        }

        return $wisataTerbaik;
    }

    /**
     * Cari wisata terdekat menggunakan Haversine (cepat, tanpa API call)
     */
    private function cariWisataTerdekatCepat($lokasiAwal, $semuaWisata, $skipWisataId = null)
    {
        $wisataTerdekat = null;
        $jarakTerpendek = PHP_FLOAT_MAX;

        foreach ($semuaWisata as $wisata) {
            // Skip wisata tertentu jika diminta
            if ($skipWisataId && $wisata->id_wisata === $skipWisataId) {
                continue;
            }

            // Gunakan Haversine (cepat)
            $jarakLurus = $this->hitungJarakHaversine(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisata->latitude, $wisata->longitude
            );

            if ($jarakLurus < $jarakTerpendek) {
                $jarakTerpendek = $jarakLurus;
                $wisataTerdekat = $wisata;
            }
        }

        return $wisataTerdekat;
    }

    private function cariWisataTerdekat($lokasiAwal, $semuaWisata, $skipWisataId = null)
    {
        $wisataTerdekat = null;
        $jarakTerpendek = PHP_FLOAT_MAX;

        foreach ($semuaWisata as $wisata) {
            // Skip wisata tertentu jika diminta
            if ($skipWisataId && $wisata->id_wisata === $skipWisataId) {
                continue;
            }

            // Prioritaskan jarak jalan sebenarnya daripada jarak lurus
            $jarakJalan = $this->hitungJarakJalanSebenarnya(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisata->latitude, $wisata->longitude
            );

            // Jika API gagal, fallback ke Haversine
            if ($jarakJalan === null) {
                $jarakJalan = $this->hitungJarakHaversine(
                    $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                    $wisata->latitude, $wisata->longitude
                );
            }

            if ($jarakJalan < $jarakTerpendek) {
                $jarakTerpendek = $jarakJalan;
                $wisataTerdekat = $wisata;
            }
        }

        return $wisataTerdekat;
    }

    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2)
    {
        $radiusBumi = 6371; // Radius bumi dalam kilometer

        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($deltaLon/2) * sin($deltaLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $radiusBumi * $c;
    }

    /**
     * Hitung jarak menggunakan routing jalan sebenarnya via OSRM API
     * Return null jika gagal, sehingga bisa fallback ke Haversine
     */
    private function hitungJarakJalanSebenarnya($lat1, $lon1, $lat2, $lon2)
    {
        try {
            $urlOSRM = "http://router.project-osrm.org/route/v1/driving/"
                     . $lon1 . "," . $lat1 . ";"
                     . $lon2 . "," . $lat2
                     . "?overview=false";

            $responOSRM = $this->panggilAPIRouting($urlOSRM);

            if ($responOSRM && isset($responOSRM['routes'][0]['distance'])) {
                // Konversi dari meter ke kilometer
                return round($responOSRM['routes'][0]['distance'] / 1000, 2);
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    private function cariSimpulJarakTerpendek($jarak, $simpulBelumDikunjungi)
    {
        $simpulTerpendek = null;
        $jarakTerpendek = PHP_INT_MAX;

        foreach ($simpulBelumDikunjungi as $simpul => $value) {
            if ($jarak[$simpul] < $jarakTerpendek) {
                $jarakTerpendek = $jarak[$simpul];
                $simpulTerpendek = $simpul;
            }
        }

        return $simpulTerpendek;
    }

    private function rekonstruksiJalur($jalurSebelumnya, $awal, $tujuan)
    {
        $jalur = [];
        $simpulSaatIni = $tujuan;

        while ($simpulSaatIni !== null) {
            array_unshift($jalur, $simpulSaatIni);
            $simpulSaatIni = $jalurSebelumnya[$simpulSaatIni];
        }

        // Jika jalur tidak dimulai dari awal, berarti tidak ada jalur
        if ($jalur[0] !== $awal) {
            return [];
        }

        return $jalur;
    }

    private function buatJalurAlternatif($wisataAwal, $wisataTujuan, $semuaWisata)
    {
        // Jika tidak ada jalur di database, buat jalur langsung
        return [$wisataAwal->id_wisata, $wisataTujuan->id_wisata];
    }

    private function estimasiWaktuTempuh($jarakKm)
    {
        // Asumsi kecepatan rata-rata 40 km/jam
        $kecepatanRataRata = 40;
        $waktuJam = $jarakKm / $kecepatanRataRata;

        $jam = floor($waktuJam);
        $menit = round(($waktuJam - $jam) * 60);

        if ($jam > 0) {
            return $jam . ' jam ' . $menit . ' menit';
        } else {
            return $menit . ' menit';
        }
    }

    /**
     * Dapatkan data koordinat untuk rute alternatif
     */
    public function dapatkanDataRuteAlternatif(Request $request)
    {
        $ruteIndex = $request->rute_index;
        $jalurWisata = $request->jalur;

        if (!is_array($jalurWisata) || count($jalurWisata) < 2) {
            return response()->json(['error' => 'Jalur tidak valid'], 400);
        }

        $wisataData = Wisata::whereIn('id_wisata', $jalurWisata)->get()->keyBy('id_wisata');

        $koordinatRute = [];
        foreach ($jalurWisata as $id) {
            if (isset($wisataData[$id])) {
                $koordinatRute[] = [
                    'lat' => (float) $wisataData[$id]->latitude,
                    'lng' => (float) $wisataData[$id]->longitude,
                    'nama' => $wisataData[$id]->nama_wisata
                ];
            }
        }

        return response()->json([
            'success' => true,
            'rute_index' => $ruteIndex,
            'koordinat_rute' => $koordinatRute,
            'jumlah_titik' => count($koordinatRute)
        ]);
    }

    public function dapatkanDataRute(Request $request)
    {
        $wisataIds = $request->jalur;
        $wisataData = Wisata::whereIn('id_wisata', $wisataIds)->get()->keyBy('id_wisata');

        $koordinatRute = [];
        foreach ($wisataIds as $id) {
            if (isset($wisataData[$id])) {
                $koordinatRute[] = [
                    'lat' => (float) $wisataData[$id]->latitude,
                    'lng' => (float) $wisataData[$id]->longitude,
                    'nama' => $wisataData[$id]->nama_wisata
                ];
            }
        }

        return response()->json($koordinatRute);
    }

    public function dapatkanRuteJalanSebenarnya(Request $request)
    {
        $koordinatAwal = $request->koordinat_awal;
        $koordinatTujuan = $request->koordinat_tujuan;

        Log::info('📍 Request rute jalan:', [
            'dari' => $koordinatAwal,
            'ke' => $koordinatTujuan
        ]);

        try {
            // Validasi koordinat
            if (!isset($koordinatAwal['lat'], $koordinatAwal['lng'], $koordinatTujuan['lat'], $koordinatTujuan['lng'])) {
                Log::error('❌ Koordinat tidak valid');
                return response()->json([
                    'success' => false,
                    'error' => 'Koordinat tidak valid'
                ], 400);
            }

            // Menggunakan OSRM (Open Source Routing Machine) untuk mendapatkan rute jalan sebenarnya
            $urlOSRM = "http://router.project-osrm.org/route/v1/driving/"
                     . $koordinatAwal['lng'] . "," . $koordinatAwal['lat'] . ";"
                     . $koordinatTujuan['lng'] . "," . $koordinatTujuan['lat']
                     . "?overview=full&geometries=geojson";

            $responOSRM = $this->panggilAPIRouting($urlOSRM);

            if ($responOSRM && isset($responOSRM['routes'][0]['geometry']['coordinates'])) {
                $koordinatJalan = $responOSRM['routes'][0]['geometry']['coordinates'];

                Log::info('✅ Rute OSRM berhasil didapat: ' . count($koordinatJalan) . ' titik koordinat');

                // Konversi format dari [lng, lat] ke [lat, lng] untuk Leaflet
                $koordinatRute = array_map(function($coord) {
                    return [$coord[1], $coord[0]]; // [lat, lng]
                }, $koordinatJalan);

                $jarak = round($responOSRM['routes'][0]['distance'] / 1000, 2);
                $durasi = round($responOSRM['routes'][0]['duration'] / 60, 0);

                Log::info("✅ Mengembalikan rute: Jarak = {$jarak} km, Durasi = {$durasi} menit");

                return response()->json([
                    'success' => true,
                    'koordinat_rute' => $koordinatRute,
                    'jarak' => $jarak, // km
                    'durasi' => $durasi, // menit
                    'fallback' => false,
                    'provider' => 'OSRM'
                ]);
            }

            // Coba alternatif provider: GraphHopper API (public server)
            Log::warning('⚠️ OSRM gagal, mencoba GraphHopper API...');
            $responGraphHopper = $this->panggilGraphHopperAPI($koordinatAwal, $koordinatTujuan);

            if ($responGraphHopper !== null) {
                return $responGraphHopper;
            }

            // Fallback terakhir: jika semua API gagal, gunakan garis lurus
            Log::warning('⚠️ Semua API routing gagal, menggunakan fallback garis lurus');
            return $this->buatGarisLurusFallback($koordinatAwal, $koordinatTujuan);

        } catch (\Exception $e) {
            // Fallback: jika ada error, gunakan garis lurus
            Log::error('❌ Exception pada dapatkanRuteJalanSebenarnya: ' . $e->getMessage());
            Log::error('   Stack: ' . $e->getTraceAsString());
            return $this->buatGarisLurusFallback($koordinatAwal, $koordinatTujuan);
        }
    }

    /**
     * Cari semua rute alternatif menggunakan modified Dijkstra dengan K-shortest paths
     */
    private function cariSemuaRuteAlternatif($graf, $semuaWisata, $wisataAwal, $wisataTujuan, $jarakKeWisataAwal = 0)
    {
        $ruteAlternatif = [];
        $maxRute = 4; // Maksimal 4 rute alternatif (termasuk rute utama)

        // Gunakan Yen's algorithm untuk mencari K-shortest paths
        $rutePertama = $this->dijkstraStandard($graf, $semuaWisata, $wisataAwal, $wisataTujuan);

        if (!empty($rutePertama['jalur'])) {
            $ruteAlternatif[] = $this->formatRuteInfo($rutePertama, $semuaWisata, 1, $jarakKeWisataAwal);

            // Log rute pertama
            Log::info('Rute 1 ditemukan: ' . implode(' -> ', $rutePertama['jalur']));

            // Cari rute alternatif lainnya dengan menghapus edge satu per satu
            for ($k = 1; $k < $maxRute; $k++) {
                $ruteKandidat = []; // Reset kandidat untuk setiap iterasi
                $ruteTerbaik = null;
                $jarakTerbaik = PHP_INT_MAX;

                Log::info("Mencari rute alternatif ke-" . ($k + 1));

                // Untuk setiap rute yang sudah ditemukan, coba buat variasi
                foreach ($ruteAlternatif as $ruteExisting) {
                    $jalurExisting = $ruteExisting['jalur'];

                    // Coba hapus setiap edge dalam jalur dan cari rute baru
                    for ($i = 0; $i < count($jalurExisting) - 1; $i++) {
                        $grafTemp = $graf;
                        $this->hapusEdgeDariGraf($grafTemp, $jalurExisting[$i], $jalurExisting[$i + 1]);

                        $ruteBaru = $this->dijkstraStandard($grafTemp, $semuaWisata, $wisataAwal, $wisataTujuan);

                        if (!empty($ruteBaru['jalur']) && !$this->ruteUdahAda($ruteBaru['jalur'], $ruteAlternatif)) {
                            $ruteBaruFormatted = $this->formatRuteInfo($ruteBaru, $semuaWisata, $k + 1, $jarakKeWisataAwal);

                            // Pastikan ada perbedaan jarak yang signifikan (minimal 1 km)
                            $jarakSignifikan = true;
                            foreach ($ruteAlternatif as $ruteExisting) {
                                $selisihJarak = abs($ruteBaruFormatted['jarak_rute'] - $ruteExisting['jarak_rute']);
                                if ($selisihJarak < 1.0) { // kurang dari 1 km perbedaan
                                    $jarakSignifikan = false;
                                    Log::info('Rute ditolak karena jarak terlalu mirip: ' .
                                              implode(' -> ', $ruteBaru['jalur']) .
                                              ' (' . $ruteBaruFormatted['jarak_rute'] . ' km vs ' .
                                              $ruteExisting['jarak_rute'] . ' km)');
                                    break;
                                }
                            }

                            if ($jarakSignifikan) {
                                Log::info('Rute kandidat ditemukan: ' . implode(' -> ', $ruteBaru['jalur']) .
                                          ' (Jarak: ' . $ruteBaruFormatted['jarak_rute'] . ' km)');

                                // Cari rute terpendek dari kandidat baru
                                if ($ruteBaruFormatted['jarak_rute'] < $jarakTerbaik) {
                                    $ruteTerbaik = $ruteBaruFormatted;
                                    $jarakTerbaik = $ruteBaruFormatted['jarak_rute'];
                                }
                            }
                        }
                    }
                }

                // Tambahkan rute terbaik jika ditemukan
                if ($ruteTerbaik !== null) {
                    $ruteAlternatif[] = $ruteTerbaik;
                    Log::info('Rute ' . ($k + 1) . ' dipilih: ' . implode(' -> ', $ruteTerbaik['jalur']) .
                              ' (Jarak: ' . $ruteTerbaik['jarak_rute'] . ' km)');
                } else {
                    // Jika tidak ada rute baru ditemukan, hentikan pencarian
                    Log::info('Tidak ada rute alternatif lagi yang ditemukan pada iterasi ' . ($k + 1));
                    break;
                }
            }            // Tambahkan rute langsung sebagai alternatif terakhir jika belum ada
            $ruteLangsung = $this->buatRuteLangsung($wisataAwal, $wisataTujuan, count($ruteAlternatif) + 1, $jarakKeWisataAwal);
            if (!$this->ruteUdahAda($ruteLangsung['jalur'], $ruteAlternatif)) {
                $ruteAlternatif[] = $ruteLangsung;
                Log::info('Rute langsung ditambahkan: ' . implode(' -> ', $ruteLangsung['jalur']));
            } else {
                Log::info('Rute langsung sudah ada, tidak ditambahkan');
            }

            // Tambahkan rute via Pematang Siantar sebagai alternatif
            $ruteViaPematangSiantar = $this->buatRuteViaPematangSiantar($wisataAwal, $wisataTujuan, count($ruteAlternatif) + 1, $jarakKeWisataAwal);
            if ($ruteViaPematangSiantar && !$this->ruteUdahAda($ruteViaPematangSiantar['jalur'], $ruteAlternatif)) {
                $ruteAlternatif[] = $ruteViaPematangSiantar;
                Log::info('Rute via Pematang Siantar ditambahkan: ' . implode(' -> ', $ruteViaPematangSiantar['jalur']));
            } else {
                Log::info('Rute via Pematang Siantar tidak ditambahkan (sudah ada atau tidak valid)');
            }

            Log::info('Total rute alternatif yang ditemukan: ' . count($ruteAlternatif));
        }

        return $ruteAlternatif;
    }

    /**
     * Implementasi Dijkstra standard
     */
    private function dijkstraStandard($graf, $semuaWisata, $wisataAwal, $wisataTujuan)
    {
        $jarak = [];
        $jalurSebelumnya = [];
        $simpulBelumDikunjungi = [];

        // Inisialisasi jarak
        foreach ($semuaWisata as $wisata) {
            $jarak[$wisata->id_wisata] = PHP_INT_MAX;
            $jalurSebelumnya[$wisata->id_wisata] = null;
            $simpulBelumDikunjungi[$wisata->id_wisata] = true;
        }

        // Jarak dari wisata awal ke dirinya sendiri adalah 0
        $jarak[$wisataAwal->id_wisata] = 0;

        while (!empty($simpulBelumDikunjungi)) {
            // Cari simpul dengan jarak terpendek
            $simpulSaatIni = $this->cariSimpulJarakTerpendek($jarak, $simpulBelumDikunjungi);

            if ($simpulSaatIni === null || $jarak[$simpulSaatIni] === PHP_INT_MAX) {
                break;
            }

            unset($simpulBelumDikunjungi[$simpulSaatIni]);

            // Update jarak ke tetangga
            if (isset($graf[$simpulSaatIni])) {
                foreach ($graf[$simpulSaatIni] as $tetangga) {
                    $jarakBaru = $jarak[$simpulSaatIni] + $tetangga['jarak'];

                    if ($jarakBaru < $jarak[$tetangga['tujuan']]) {
                        $jarak[$tetangga['tujuan']] = $jarakBaru;
                        $jalurSebelumnya[$tetangga['tujuan']] = $simpulSaatIni;
                    }
                }
            }
        }

        // Rekonstruksi jalur terpendek
        $jalurTerpendek = $this->rekonstruksiJalur($jalurSebelumnya, $wisataAwal->id_wisata, $wisataTujuan->id_wisata);

        return [
            'jalur' => $jalurTerpendek,
            'jarak_total' => $jarak[$wisataTujuan->id_wisata] !== PHP_INT_MAX ? $jarak[$wisataTujuan->id_wisata] : 0
        ];
    }

    /**
     * Format informasi rute untuk display
     */
    private function formatRuteInfo($rute, $semuaWisata, $nomorRute, $jarakKeWisataAwal = 0)
    {
        $wisataMap = $semuaWisata->keyBy('id_wisata');
        $wisataTransit = [];
        $semuaDestinasiDilalui = [];

        // Ambil info wisata transit (kecuali awal dan tujuan)
        for ($i = 1; $i < count($rute['jalur']) - 1; $i++) {
            $wisataId = $rute['jalur'][$i];
            if (isset($wisataMap[$wisataId])) {
                $wisataTransit[] = [
                    'id' => $wisataId,
                    'nama' => $wisataMap[$wisataId]->nama_wisata,
                    'latitude' => $wisataMap[$wisataId]->latitude,
                    'longitude' => $wisataMap[$wisataId]->longitude
                ];
            }
        }

        // Ambil info SEMUA destinasi yang dilalui (termasuk awal dan tujuan)
        foreach ($rute['jalur'] as $index => $wisataId) {
            if (isset($wisataMap[$wisataId])) {
                $semuaDestinasiDilalui[] = [
                    'id' => $wisataId,
                    'nama' => $wisataMap[$wisataId]->nama_wisata,
                    'latitude' => $wisataMap[$wisataId]->latitude,
                    'longitude' => $wisataMap[$wisataId]->longitude,
                    'posisi' => $index === 0 ? 'awal' : ($index === count($rute['jalur']) - 1 ? 'tujuan' : 'transit'),
                    'urutan' => $index + 1
                ];
            }
        }

        // Total jarak termasuk jarak dari lokasi pengguna ke wisata awal (jika ada)
        $totalJarakRute = $rute['jarak_total'] + $jarakKeWisataAwal;

        return [
            'nomor_rute' => $nomorRute,
            'jalur' => $rute['jalur'],
            'jarak_rute' => $totalJarakRute, // Sudah termasuk jarak dari lokasi pengguna
            'waktu_rute' => $this->estimasiWaktuTempuh($totalJarakRute), // Estimasi berdasarkan total jarak
            'jumlah_transit' => count($wisataTransit),
            'wisata_transit' => $wisataTransit,
            'semua_destinasi_dilalui' => $semuaDestinasiDilalui, // Data baru untuk semua destinasi
            'tingkat_kemudahan' => $this->tentukanTingkatKemudahan(count($wisataTransit), $totalJarakRute),
            'warna_rute' => $this->tentukanWarnaRute($nomorRute)
        ];
    }

    /**
     * Hapus edge dari graf
     */
    private function hapusEdgeDariGraf(&$graf, $dari, $ke)
    {
        if (isset($graf[$dari])) {
            $graf[$dari] = array_filter($graf[$dari], function($edge) use ($ke) {
                return $edge['tujuan'] !== $ke;
            });
        }

        if (isset($graf[$ke])) {
            $graf[$ke] = array_filter($graf[$ke], function($edge) use ($dari) {
                return $edge['tujuan'] !== $dari;
            });
        }
    }

    /**
     * Cek apakah rute sudah ada
     */
    private function ruteUdahAda($jalurBaru, $ruteExisting)
    {
        foreach ($ruteExisting as $rute) {
            // Cek apakah array jalur benar-benar identik
            if ($rute['jalur'] === $jalurBaru) {
                Log::info('Duplikasi ditemukan (identik): ' . implode(' -> ', $jalurBaru));
                return true;
            }

            // Cek apakah jalur sama dengan urutan terbalik (untuk rute yang bisa bolak-balik)
            if ($rute['jalur'] === array_reverse($jalurBaru)) {
                Log::info('Duplikasi ditemukan (terbalik): ' . implode(' -> ', $jalurBaru));
                return true;
            }

            // Cek set destinasi yang sama (untuk rute dengan titik transit yang sama tapi urutan berbeda)
            $setRuteExisting = array_unique($rute['jalur']);
            $setRuteBaru = array_unique($jalurBaru);
            sort($setRuteExisting);
            sort($setRuteBaru);

            if ($setRuteExisting === $setRuteBaru && count($rute['jalur']) === count($jalurBaru)) {
                Log::info('Duplikasi ditemukan (set sama): ' . implode(' -> ', $jalurBaru) .
                          ' vs existing: ' . implode(' -> ', $rute['jalur']));
                return true;
            }
        }
        return false;
    }    /**
     * Buat rute langsung
     */
    private function buatRuteLangsung($wisataAwal, $wisataTujuan, $nomorRute, $jarakKeWisataAwal = 0)
    {
        $jarakLangsung = $this->hitungJarakJalanSebenarnya(
            $wisataAwal->latitude, $wisataAwal->longitude,
            $wisataTujuan->latitude, $wisataTujuan->longitude
        );

        if ($jarakLangsung === null) {
            $jarakLangsung = $this->hitungJarakHaversine(
                $wisataAwal->latitude, $wisataAwal->longitude,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );
        }

        // Total jarak termasuk jarak dari lokasi pengguna ke wisata awal (jika ada)
        $totalJarakLangsung = $jarakLangsung + $jarakKeWisataAwal;

        return [
            'nomor_rute' => $nomorRute,
            'jalur' => [$wisataAwal->id_wisata, $wisataTujuan->id_wisata],
            'jarak_rute' => $totalJarakLangsung, // Sudah termasuk jarak dari lokasi pengguna
            'waktu_rute' => $this->estimasiWaktuTempuh($totalJarakLangsung), // Estimasi berdasarkan total jarak
            'jumlah_transit' => 0,
            'wisata_transit' => [],
            'semua_destinasi_dilalui' => [
                [
                    'id' => $wisataAwal->id_wisata,
                    'nama' => $wisataAwal->nama_wisata,
                    'latitude' => $wisataAwal->latitude,
                    'longitude' => $wisataAwal->longitude,
                    'posisi' => 'awal',
                    'urutan' => 1
                ],
                [
                    'id' => $wisataTujuan->id_wisata,
                    'nama' => $wisataTujuan->nama_wisata,
                    'latitude' => $wisataTujuan->latitude,
                    'longitude' => $wisataTujuan->longitude,
                    'posisi' => 'tujuan',
                    'urutan' => 2
                ]
            ],
            'tingkat_kemudahan' => 'Mudah',
            'warna_rute' => $this->tentukanWarnaRute($nomorRute)
        ];
    }

    /**
     * Buat rute via Pematang Siantar sebagai waypoint
     */
    private function buatRuteViaPematangSiantar($wisataAwal, $wisataTujuan, $nomorRute, $jarakKeWisataAwal = 0)
    {
        // Koordinat Pematang Siantar sebagai waypoint
        $pematangSiantarLat = 2.9676002181287195;
        $pematangSiantarLng = 99.06843670021658;

        // Hitung jarak dari wisata awal ke Pematang Siantar
        $jarakKeWaypoint = $this->hitungJarakJalanSebenarnya(
            $wisataAwal->latitude, $wisataAwal->longitude,
            $pematangSiantarLat, $pematangSiantarLng
        );

        if ($jarakKeWaypoint === null) {
            $jarakKeWaypoint = $this->hitungJarakHaversine(
                $wisataAwal->latitude, $wisataAwal->longitude,
                $pematangSiantarLat, $pematangSiantarLng
            );
        }

        // Hitung jarak dari Pematang Siantar ke tujuan
        $jarakDariWaypoint = $this->hitungJarakJalanSebenarnya(
            $pematangSiantarLat, $pematangSiantarLng,
            $wisataTujuan->latitude, $wisataTujuan->longitude
        );

        if ($jarakDariWaypoint === null) {
            $jarakDariWaypoint = $this->hitungJarakHaversine(
                $pematangSiantarLat, $pematangSiantarLng,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );
        }

        // Total jarak termasuk jarak dari lokasi pengguna ke wisata awal
        $totalJarakViaPematang = $jarakKeWaypoint + $jarakDariWaypoint + $jarakKeWisataAwal;

        return [
            'nomor_rute' => $nomorRute,
            'jalur' => [$wisataAwal->id_wisata, 'pematang_siantar', $wisataTujuan->id_wisata],
            'jarak_rute' => $totalJarakViaPematang,
            'waktu_rute' => $this->estimasiWaktuTempuh($totalJarakViaPematang),
            'jumlah_transit' => 1,
            'wisata_transit' => [
                [
                    'id' => 'pematang_siantar',
                    'nama' => 'Pematang Siantar',
                    'latitude' => $pematangSiantarLat,
                    'longitude' => $pematangSiantarLng
                ]
            ],
            'semua_destinasi_dilalui' => [
                [
                    'id' => $wisataAwal->id_wisata,
                    'nama' => $wisataAwal->nama_wisata,
                    'latitude' => $wisataAwal->latitude,
                    'longitude' => $wisataAwal->longitude,
                    'posisi' => 'awal',
                    'urutan' => 1
                ],
                [
                    'id' => 'pematang_siantar',
                    'nama' => 'Pematang Siantar',
                    'latitude' => $pematangSiantarLat,
                    'longitude' => $pematangSiantarLng,
                    'posisi' => 'transit',
                    'urutan' => 2
                ],
                [
                    'id' => $wisataTujuan->id_wisata,
                    'nama' => $wisataTujuan->nama_wisata,
                    'latitude' => $wisataTujuan->latitude,
                    'longitude' => $wisataTujuan->longitude,
                    'posisi' => 'tujuan',
                    'urutan' => 3
                ]
            ],
            'tingkat_kemudahan' => 'Sedang',
            'warna_rute' => $this->tentukanWarnaRute($nomorRute),
            'via_pematang_siantar' => true // Flag khusus untuk route ini
        ];
    }

    /**
     * Tentukan tingkat kemudahan berdasarkan transit dan jarak
     */
    private function tentukanTingkatKemudahan($jumlahTransit, $jarak)
    {
        if ($jumlahTransit === 0) {
            return 'Mudah';
        } elseif ($jumlahTransit <= 2 && $jarak <= 50) {
            return 'Sedang';
        } else {
            return 'Sulit';
        }
    }

    /**
     * Tentukan warna untuk setiap rute
     */
    private function tentukanWarnaRute($nomorRute)
    {
        $warnaRute = [
            1 => '#007bff', // Biru - Rute Utama
            2 => '#28a745', // Hijau - Rute Alternatif 1
            3 => '#ffc107', // Kuning - Rute Alternatif 2
            4 => '#dc3545', // Merah - Rute Alternatif 3
            5 => '#6f42c1', // Ungu - Rute Alternatif 4
        ];

        return $warnaRute[$nomorRute] ?? '#6c757d';
    }

    private function panggilAPIRouting($url)
    {
        try {
            // Tambahkan logging untuk debugging
            Log::info('🔄 Memanggil OSRM API: ' . $url);

            $konteks = stream_context_create([
                'http' => [
                    'timeout' => 15, // Naikkan timeout menjadi 15 detik untuk rute panjang
                    'user_agent' => 'Pariwisata Humbang Hasundutan/1.0',
                    'ignore_errors' => true,
                    'method' => 'GET',
                    'header' => "Accept: application/json\r\n"
                ]
            ]);

            $waktuMulai = microtime(true);
            $responJSON = @file_get_contents($url, false, $konteks);
            $waktuSelesai = microtime(true);
            $durasi = round(($waktuSelesai - $waktuMulai) * 1000); // dalam milliseconds

            if ($responJSON === false) {
                // Dapatkan error detail
                $error = error_get_last();
                Log::error('❌ OSRM API gagal: ' . ($error['message'] ?? 'Unknown error'));
                Log::error('   URL: ' . $url);
                Log::error('   Durasi: ' . $durasi . 'ms');
                return null;
            }

            Log::info('✅ OSRM API berhasil (dalam ' . $durasi . 'ms)');

            $data = json_decode($responJSON, true);

            if ($data === null) {
                Log::error('❌ JSON decode gagal. Response: ' . substr($responJSON, 0, 200));
                return null;
            }

            if (isset($data['code']) && $data['code'] !== 'Ok') {
                Log::warning('⚠️ OSRM mengembalikan error code: ' . ($data['code'] ?? 'unknown'));
                Log::warning('   Message: ' . ($data['message'] ?? 'no message'));
            }

            return $data;

        } catch (\Exception $e) {
            Log::error("❌ Exception pada API Routing: " . $e->getMessage());
            Log::error("   Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Panggil GraphHopper API sebagai alternatif OSRM
     */
    private function panggilGraphHopperAPI($koordinatAwal, $koordinatTujuan)
    {
        try {
            // GraphHopper public server (free, no API key needed)
            $url = "https://graphhopper.com/api/1/route"
                 . "?point=" . $koordinatAwal['lat'] . "," . $koordinatAwal['lng']
                 . "&point=" . $koordinatTujuan['lat'] . "," . $koordinatTujuan['lng']
                 . "&vehicle=car"
                 . "&locale=id"
                 . "&points_encoded=false"
                 . "&type=json";

            Log::info('🔄 Memanggil GraphHopper API: ' . $url);

            $konteks = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'user_agent' => 'Pariwisata Humbang Hasundutan/1.0',
                    'ignore_errors' => true,
                    'method' => 'GET',
                    'header' => "Accept: application/json\r\n"
                ]
            ]);

            $waktuMulai = microtime(true);
            $responJSON = @file_get_contents($url, false, $konteks);
            $waktuSelesai = microtime(true);
            $durasi = round(($waktuSelesai - $waktuMulai) * 1000);

            if ($responJSON === false) {
                Log::error('❌ GraphHopper API gagal');
                return null;
            }

            $data = json_decode($responJSON, true);

            if ($data && isset($data['paths'][0]['points']['coordinates'])) {
                $koordinatJalan = $data['paths'][0]['points']['coordinates'];

                Log::info('✅ Rute GraphHopper berhasil didapat: ' . count($koordinatJalan) . ' titik koordinat');

                // Konversi format dari [lng, lat] ke [lat, lng] untuk Leaflet
                $koordinatRute = array_map(function($coord) {
                    return [$coord[1], $coord[0]]; // [lat, lng]
                }, $koordinatJalan);

                $jarak = round($data['paths'][0]['distance'] / 1000, 2);
                $durasi = round($data['paths'][0]['time'] / 60000, 0);

                Log::info("✅ Mengembalikan rute GraphHopper: Jarak = {$jarak} km, Durasi = {$durasi} menit");

                return response()->json([
                    'success' => true,
                    'koordinat_rute' => $koordinatRute,
                    'jarak' => $jarak,
                    'durasi' => $durasi,
                    'fallback' => false,
                    'provider' => 'GraphHopper'
                ]);
            }

            Log::warning('⚠️ GraphHopper tidak mengembalikan data rute');
            return null;

        } catch (\Exception $e) {
            Log::error("❌ Exception pada GraphHopper API: " . $e->getMessage());
            return null;
        }
    }

    private function buatGarisLurusFallback($koordinatAwal, $koordinatTujuan)
    {
        Log::warning('📏 Menggunakan garis lurus sebagai fallback');

        $koordinatRute = [
            [$koordinatAwal['lat'], $koordinatAwal['lng']],
            [$koordinatTujuan['lat'], $koordinatTujuan['lng']]
        ];

        $jarak = $this->hitungJarakHaversine(
            $koordinatAwal['lat'], $koordinatAwal['lng'],
            $koordinatTujuan['lat'], $koordinatTujuan['lng']
        );

        Log::info("📏 Fallback: Jarak lurus = {$jarak} km");

        return response()->json([
            'success' => true,
            'koordinat_rute' => $koordinatRute,
            'jarak' => round($jarak, 2),
            'durasi' => round($jarak / 40 * 60, 0), // estimasi dengan kecepatan 40 km/jam
            'fallback' => true,
            'warning' => 'Menggunakan garis lurus (estimasi). Rute jalan sebenarnya tidak tersedia.'
        ]);
    }
}
