<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $lokasiAwal = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ];

        $wisataTujuan = Wisata::findOrFail($request->lokasi_tujuan);

        // Cari rute terpendek menggunakan algoritma Dijkstra
        $hasilRute = $this->algoritmaRuteTerpendek($lokasiAwal, $wisataTujuan);

        return view('pengunjung.hasil-rute', compact('hasilRute', 'lokasiAwal', 'wisataTujuan'));
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

        // Implementasi algoritma Dijkstra
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

        // Jika tidak ada jalur langsung, gunakan jalur alternatif dengan Haversine
        if (empty($jalurTerpendek) || $jarak[$wisataTujuan->id_wisata] === PHP_INT_MAX) {
            $jalurTerpendek = $this->buatJalurAlternatif($wisataAwal, $wisataTujuan, $semuaWisata);
            $jarak[$wisataTujuan->id_wisata] = $this->hitungJarakHaversine(
                $wisataAwal->latitude, $wisataAwal->longitude,
                $wisataTujuan->latitude, $wisataTujuan->longitude
            );
        }

        return [
            'jalur' => $jalurTerpendek,
            'jarak_total' => $jarak[$wisataTujuan->id_wisata],
            'waktu_tempuh' => $this->estimasiWaktuTempuh($jarak[$wisataTujuan->id_wisata]),
            'wisata_awal' => $wisataAwal,
            'jarak_ke_wisata_awal' => $this->hitungJarakHaversine(
                $lokasiAwal['latitude'], $lokasiAwal['longitude'],
                $wisataAwal->latitude, $wisataAwal->longitude
            )
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
