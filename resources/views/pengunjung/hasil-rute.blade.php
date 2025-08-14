@extends("template-pengunjung")
@section("title", "Hasil Rute Terpendek - Pariwisata Humbahas")
@section("body")
    <section id="hasil-rute" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <div class="row">
                <div class="card py-5 shadow-sm">
                    <div class="container">
                        <h2 class="fw-bold text-center mb-4">Hasil Rute Terpendek</h2>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-rute">
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Lokasi Awal</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7" id="namaLokasiAwal">Lokasi Anda Saat Ini</div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Lokasi Tujuan</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">{{ $wisataTujuan->nama_wisata }}</div>
                                    </div>
                                    {{-- <div class="row mb-3">
                                        <div class="col-4"><strong>Titik Transit Terdekat</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">{{ $hasilRute["wisata_awal"]->nama_wisata }}
                                            ({{ number_format($hasilRute["jarak_ke_wisata_awal"], 2) }} km dari Anda)</div>
                                    </div> --}}
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Jarak</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">{{ number_format($hasilRute["jarak_total"], 2) }} km </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Waktu Tempuh</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">{{ $hasilRute["waktu_tempuh"] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Peta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="peta-container">
                                    <div id="peta" style="height: 500px; border: 2px solid #ddd; border-radius: 8px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Kembali -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route("pengunjung.wisata") }}" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Kembali Ke Daftar Wisata
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push("script")
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script type="text/javascript">
        $(document).ready(function() {
            inisialisasiPeta();
        });

        function inisialisasiPeta() {
            // Tampilkan loading indicator
            $('#peta').html(
                '<div class="d-flex justify-content-center align-items-center h-100"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat peta...</span></div><span class="ms-2">Memuat peta dan rute...</span></div>'
            );

            // Data dari server
            const lokasiAwal = {
                lat: {{ $lokasiAwal["latitude"] }},
                lng: {{ $lokasiAwal["longitude"] }}
            };

            const wisataAwal = {
                lat: {{ $hasilRute["wisata_awal"]->latitude }},
                lng: {{ $hasilRute["wisata_awal"]->longitude }},
                nama: "{{ $hasilRute["wisata_awal"]->nama_wisata }}"
            };

            const wisataTujuan = {
                lat: {{ $wisataTujuan->latitude }},
                lng: {{ $wisataTujuan->longitude }},
                nama: "{{ $wisataTujuan->nama_wisata }}"
            };

            const jalurWisata = @json($hasilRute["jalur"]);

            // Hapus loading dan inisialisasi peta
            setTimeout(function() {
                $('#peta').empty();

                // Inisialisasi peta
                const peta = L.map('peta').setView([wisataAwal.lat, wisataAwal.lng], 10);

                // Tambahkan tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(peta);

                // Marker untuk lokasi awal (posisi pengguna)
                const markerLokasiAwal = L.marker([lokasiAwal.lat, lokasiAwal.lng], {
                    icon: L.divIcon({
                        className: 'marker-lokasi-awal',
                        html: '<div style="background-color: #28a745; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill"></i></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                }).addTo(peta);
                markerLokasiAwal.bindPopup('<b>Lokasi Anda Saat Ini</b>').openPopup();

                // Marker untuk wisata awal terdekat
                const markerWisataAwal = L.marker([wisataAwal.lat, wisataAwal.lng], {
                    icon: L.divIcon({
                        className: 'marker-wisata-awal',
                        html: '<div style="background-color: #007bff; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-flag-fill"></i></div>',
                        iconSize: [25, 25],
                        iconAnchor: [12, 12]
                    })
                }).addTo(peta);
                markerWisataAwal.bindPopup('<b>Titik Awal: ' + wisataAwal.nama +
                    '</b><br>Jarak dari lokasi Anda: {{ number_format($hasilRute["jarak_ke_wisata_awal"], 2) }} km'
                );

                // Marker untuk wisata tujuan
                const markerWisataTujuan = L.marker([wisataTujuan.lat, wisataTujuan.lng], {
                    icon: L.divIcon({
                        className: 'marker-wisata-tujuan',
                        html: '<div style="background-color: #dc3545; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-geo-alt-fill"></i></div>',
                        iconSize: [25, 25],
                        iconAnchor: [12, 12]
                    })
                }).addTo(peta);
                markerWisataTujuan.bindPopup('<b>Tujuan: ' + wisataTujuan.nama + '</b>');

                // Dapatkan koordinat rute dari server
                dapatkanKoordinatRute(jalurWisata, peta, lokasiAwal, wisataAwal, wisataTujuan);
            }, 500);
        }

        function dapatkanKoordinatRute(jalurWisata, peta, lokasiAwal, wisataAwal, wisataTujuan) {
            $.ajax({
                url: '{{ route("api.rute-data") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    jalur: jalurWisata
                },
                success: function(koordinatRute) {
                    gambarRuteDiPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute);
                },
                error: function() {
                    console.error('Gagal mendapatkan data koordinat rute');
                    // Gambar rute langsung jika gagal
                    const ruteLangsung = [
                        [wisataAwal.lat, wisataAwal.lng],
                        [wisataTujuan.lat, wisataTujuan.lng]
                    ];
                    gambarGarisRute(peta, ruteLangsung, '#007bff');
                }
            });
        }

        function gambarRuteDiPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute) {
            // Garis dari lokasi awal ke wisata terdekat dengan routing jalan sebenarnya
            dapatkanDanGambarRuteJalan(peta, lokasiAwal, wisataAwal, '#28a745', 'Jalur ke titik awal');

            // Garis rute utama antar wisata
            if (koordinatRute.length > 1) {
                gambarRuteAntarWisata(peta, koordinatRute);
            } else if (koordinatRute.length === 1) {
                // Jika hanya ada satu wisata tujuan, gambar rute langsung dari wisata awal
                dapatkanDanGambarRuteJalan(peta, wisataAwal, wisataTujuan, '#007bff', 'Jalur utama');
            }

            // Sesuaikan viewport untuk menampilkan seluruh rute
            sesuaikanViewportPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute);
        }

        function gambarRuteAntarWisata(peta, koordinatRute) {
            // Gambar rute antar titik wisata secara berurutan
            for (let i = 0; i < koordinatRute.length - 1; i++) {
                const asal = koordinatRute[i];
                const tujuan = koordinatRute[i + 1];

                dapatkanDanGambarRuteJalan(peta, asal, tujuan, '#007bff', `Jalur ${i + 1}`);

                // Tambahkan marker untuk titik transit (kecuali titik awal dan akhir)
                if (i > 0) {
                    L.marker([asal.lat, asal.lng], {
                        icon: L.divIcon({
                            className: 'marker-transit',
                            html: '<div style="background-color: #ffc107; color: black; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 1px 2px rgba(0,0,0,0.3);"><i class="bi bi-circle-fill"></i></div>',
                            iconSize: [15, 15],
                            iconAnchor: [7, 7]
                        })
                    }).addTo(peta).bindPopup('<b>Transit: ' + asal.nama + '</b>');
                }
            }
        }

        function dapatkanDanGambarRuteJalan(peta, koordinatAsal, koordinatTujuan, warna, keterangan) {
            // Panggil API untuk mendapatkan rute jalan sebenarnya
            $.ajax({
                url: '{{ route("api.rute-jalan") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    koordinat_awal: koordinatAsal,
                    koordinat_tujuan: koordinatTujuan
                },
                success: function(response) {
                    if (response.success && response.koordinat_rute) {
                        // Gambar garis mengikuti jalan sebenarnya
                        const garis = L.polyline(response.koordinat_rute, {
                            color: warna,
                            weight: 4,
                            opacity: 0.8,
                            smoothFactor: 1
                        }).addTo(peta);

                        // Tambahkan tooltip dengan informasi jarak dan durasi
                        let tooltipText = keterangan;
                        if (response.jarak) {
                            tooltipText += `<br>Jarak: ${response.jarak} km`;
                        }
                        if (response.durasi) {
                            tooltipText += `<br>Waktu: ${response.durasi} menit`;
                        }
                        if (response.fallback) {
                            tooltipText += '<br><small>(Rute perkiraan)</small>';
                        }

                        garis.bindTooltip(tooltipText, {
                            permanent: false,
                            sticky: true
                        });
                    }
                },
                error: function() {
                    // Fallback: gambar garis lurus jika API gagal
                    console.warn('Gagal mendapatkan rute jalan, menggunakan garis lurus');
                    const ruteLangsung = [
                        [koordinatAsal.lat, koordinatAsal.lng],
                        [koordinatTujuan.lat, koordinatTujuan.lng]
                    ];
                    gambarGarisRute(peta, ruteLangsung, warna, keterangan + ' (garis lurus)');
                }
            });
        }

        function sesuaikanViewportPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute) {
            const semuaTitik = [
                [lokasiAwal.lat, lokasiAwal.lng],
                [wisataAwal.lat, wisataAwal.lng],
                [wisataTujuan.lat, wisataTujuan.lng]
            ];

            koordinatRute.forEach(koordinat => {
                semuaTitik.push([koordinat.lat, koordinat.lng]);
            });

            const grup = new L.featureGroup(semuaTitik.map(titik => L.marker(titik)));
            peta.fitBounds(grup.getBounds().pad(0.1));
        }

        function gambarGarisRute(peta, koordinat, warna, tooltip) {
            const garis = L.polyline(koordinat, {
                color: warna,
                weight: 4,
                opacity: 0.8,
                smoothFactor: 1
            }).addTo(peta);

            if (tooltip) {
                garis.bindTooltip(tooltip);
            }

            return garis;
        }
    </script>

    <style>
        .info-rute {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .leaflet-container {
            font-family: inherit;
        }

        .marker-lokasi-awal,
        .marker-wisata-awal,
        .marker-wisata-tujuan,
        .marker-transit {
            border: none !important;
            background: transparent !important;
        }

        .peta-container {
            position: relative;
        }

        .legend-peta {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            border: 1px solid white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .legend-line {
            width: 20px;
            height: 3px;
            margin-right: 8px;
            border-radius: 1px;
        }

        .peta-loading {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            color: #6c757d;
        }

        /* Responsive design untuk mobile */
        @media (max-width: 768px) {
            .info-rute .row {
                font-size: 14px;
            }

            .legend-peta .row {
                flex-direction: column;
            }

            #peta {
                height: 300px !important;
            }
        }

        /* Custom tooltip styling */
        .leaflet-tooltip {
            background-color: rgba(0, 0, 0, 0.8) !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            padding: 6px 10px !important;
            font-size: 12px !important;
        }

        .leaflet-tooltip-left:before {
            border-left-color: rgba(0, 0, 0, 0.8) !important;
        }

        .leaflet-tooltip-right:before {
            border-right-color: rgba(0, 0, 0, 0.8) !important;
        }
    </style>
@endpush
