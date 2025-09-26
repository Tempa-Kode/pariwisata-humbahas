<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Wisata;
use App\Models\Rute;

class Dijkstra extends Controller
{
    public function halamanCariRute() {
        $wisata = Wisata::all();
        return view('pengunjung.cari-rute', compact('wisata'));
    }

    public function cariRuteTerpendek(Request $request)
    {
        // Validasi input
        $request->validate([
            'lokasi_awal' => 'required',
            'lokasi_tujuan' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $lokasiAwal = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        // Tentukan nama lokasi awal berdasarkan pilihan
        $namaLokasiAwal = $this->tentukanNamaLokasiAwal($request->lokasi_awal);

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

    private function algoritmaRuteTerpendek($lokasiAwal, $wisataTujuan)
    {
        // Ambil semua data wisata dan rute
        $semuaWisata = Wisata::all();
        $semuaRute = Rute::with(['lokasiAsal', 'lokasiTujuan'])->get();

        // Buat graf dari data rute
        $graf = $this->buatGraf($semuaWisata, $semuaRute);

        // Cari titik wisata terdekat dari lokasi awal menggunakan Haversine
        $wisataAwal = $this->cariWisataTerdekat($lokasiAwal, $semuaWisata);

        // Hitung jarak dari posisi pengguna ke wisata terdekat
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
        // Hitung jarak dari posisi pengguna ke wisata terdekat
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

        // Total jarak = jarak dari posisi pengguna ke wisata terdekat + jarak rute wisata
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

    private function cariWisataTerdekat($lokasiAwal, $semuaWisata)
    {
        $wisataTerdekat = null;
        $jarakTerpendek = PHP_FLOAT_MAX;

        foreach ($semuaWisata as $wisata) {
            $jarak = $this->hitungJarakHaversine(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisata->latitude, $wisata->longitude
            );

            if ($jarak < $jarakTerpendek) {
                $jarakTerpendek = $jarak;
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

        try {
            // Menggunakan OSRM (Open Source Routing Machine) untuk mendapatkan rute jalan sebenarnya
            $urlOSRM = "http://router.project-osrm.org/route/v1/driving/"
                     . $koordinatAwal['lng'] . "," . $koordinatAwal['lat'] . ";"
                     . $koordinatTujuan['lng'] . "," . $koordinatTujuan['lat']
                     . "?overview=full&geometries=geojson";

            $responOSRM = $this->panggilAPIRouting($urlOSRM);

            if ($responOSRM && isset($responOSRM['routes'][0]['geometry']['coordinates'])) {
                $koordinatJalan = $responOSRM['routes'][0]['geometry']['coordinates'];

                // Konversi format dari [lng, lat] ke [lat, lng] untuk Leaflet
                $koordinatRute = array_map(function($coord) {
                    return [$coord[1], $coord[0]]; // [lat, lng]
                }, $koordinatJalan);

                return response()->json([
                    'success' => true,
                    'koordinat_rute' => $koordinatRute,
                    'jarak' => round($responOSRM['routes'][0]['distance'] / 1000, 2), // km
                    'durasi' => round($responOSRM['routes'][0]['duration'] / 60, 0) // menit
                ]);
            }

            // Fallback: jika OSRM gagal, gunakan garis lurus
            return $this->buatGarisLurusFallback($koordinatAwal, $koordinatTujuan);

        } catch (\Exception $e) {
            // Fallback: jika ada error, gunakan garis lurus
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
            $konteks = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Pariwisata Humbang Hasundutan/1.0'
                ]
            ]);

            $responJSON = file_get_contents($url, false, $konteks);

            if ($responJSON === false) {
                return null;
            }

            return json_decode($responJSON, true);

        } catch (\Exception $e) {
            return null;
        }
    }

    private function buatGarisLurusFallback($koordinatAwal, $koordinatTujuan)
    {
        $koordinatRute = [
            [$koordinatAwal['lat'], $koordinatAwal['lng']],
            [$koordinatTujuan['lat'], $koordinatTujuan['lng']]
        ];

        $jarak = $this->hitungJarakHaversine(
            $koordinatAwal['lat'], $koordinatAwal['lng'],
            $koordinatTujuan['lat'], $koordinatTujuan['lng']
        );

        return response()->json([
            'success' => true,
            'koordinat_rute' => $koordinatRute,
            'jarak' => round($jarak, 2),
            'durasi' => round($jarak / 40 * 60, 0), // estimasi dengan kecepatan 40 km/jam
            'fallback' => true
        ]);
    }
}
