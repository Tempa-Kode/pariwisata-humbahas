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
                                        <div class="col-7" id="namaLokasiAwal">
                                            {{ $namaLokasiAwal }}
                                            <br><small class="text-muted">({{ number_format($lokasiAwal["latitude"], 6) }},
                                                {{ number_format($lokasiAwal["longitude"], 6) }})</small>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-4"><strong>Lokasi Tujuan</strong></div>
                                        <div class="col-1">:</div>
                                        <div class="col-7">
                                            {{ $wisataTujuan->nama_wisata }}
                                            <br><small class="text-muted">({{ number_format($wisataTujuan->latitude, 6) }},
                                                {{ number_format($wisataTujuan->longitude, 6) }})</small>
                                        </div>
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
                                <!-- Toggle Controls -->
                                <div class="d-flex justify-content-between align-items-center mb-3 toggle-header">
                                    <h5 class="mb-0">Peta Rute</h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleDestinasi" checked>
                                        <label class="form-check-label" for="toggleDestinasi">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Tampilkan Semua Destinasi
                                        </label>
                                    </div>
                                </div>

                                <div class="peta-container">
                                    <div id="peta" style="height: 500px; border: 2px solid #ddd; border-radius: 8px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Legend Peta -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="legend-peta p-3 bg-light rounded">
                                    <h6 class="fw-bold mb-3">Keterangan Peta:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #28a745;"></div>
                                                <small>{{ $namaLokasiAwal }}</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #007bff;"></div>
                                                <small>Titik Awal Rute</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #dc3545;"></div>
                                                <small>Tujuan Akhir</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #ffc107;"></div>
                                                <small>Destinasi Unggulan Lainnya</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-marker me-2" style="background-color: #6c757d;"></div>
                                                <small>Destinasi Wisata Lainnya</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-line me-2"
                                                    style="background-color: #007bff; height: 3px; width: 20px;"></div>
                                                <small>Jalur Rute Terpendek</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Klik marker destinasi lain untuk membuat rute baru.
                                            Gunakan toggle di atas untuk menampilkan/menyembunyikan destinasi lain.
                                        </small>
                                        <br>
                                        <small class="text-muted" id="counterDestinasi">
                                            (Memuat data destinasi...)
                                        </small>
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
        // ==========================================
        // VARIABEL GLOBAL
        // ==========================================
        let markersDestinasiLain = [];

        // ==========================================
        // INISIALISASI DOCUMENT READY
        // ==========================================
        $(document).ready(function() {
            inisialisasiPeta();

            // Event handler untuk toggle destinasi
            $('#toggleDestinasi').change(function() {
                toggleSemuaDestinasi($(this).is(':checked'));
            });
        });

        // ==========================================
        // FUNGSI UTILITY
        // ==========================================

        // Fungsi untuk update counter destinasi
        function updateCounterDestinasi() {
            const totalDestinasi = markersDestinasiLain.length;
            let visibleDestinasi = 0;

            markersDestinasiLain.forEach(marker => {
                if (window.petaGlobal && window.petaGlobal.hasLayer(marker)) {
                    visibleDestinasi++;
                }
            });

            if (totalDestinasi === 0) {
                $('#counterDestinasi').html('<i class="fas fa-info-circle text-muted"></i> Memuat data destinasi...');
            } else if (visibleDestinasi === 0) {
                $('#counterDestinasi').html(
                    `<i class="fas fa-map-marker-alt text-muted"></i> ${totalDestinasi} destinasi tersedia (semua disembunyikan)`
                    );
            } else {
                $('#counterDestinasi').html(
                    `<i class="fas fa-map-marker-alt text-danger"></i> ${visibleDestinasi} dari ${totalDestinasi} destinasi ditampilkan`
                    );
            }
        }

        // ==========================================
        // FUNGSI INISIALISASI PETA
        // ==========================================
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
                id: {{ $hasilRute["wisata_awal"]->id_wisata }},
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

                // Simpan referensi peta secara global
                window.petaGlobal = peta;

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
                markerLokasiAwal.bindPopup('<b>{{ $namaLokasiAwal }}</b>').openPopup();

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

                // Muat dan tampilkan semua destinasi wisata lainnya
                muatSemuaDestinasi(peta, wisataAwal, wisataTujuan);
            }, 500);
        }

        // ==========================================
        // FUNGSI KOORDINAT DAN RUTE
        // ==========================================
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

        // ==========================================
        // FUNGSI DESTINASI WISATA
        // ==========================================

        // Fungsi untuk memuat dan menampilkan semua destinasi wisata
        function muatSemuaDestinasi(peta, wisataAwal, wisataTujuan) {
            $.ajax({
                url: '/api/wisata',
                method: 'GET',
                success: function(dataWisata) {
                    console.log('Data semua wisata loaded:', dataWisata);
                    tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading semua wisata data:', error);
                }
            });
        }

        // Fungsi untuk menampilkan semua destinasi di peta
        function tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan) {
            // Hapus markers lama jika ada
            hapusSemuaMarkersDestinasi(peta);

            dataWisata.forEach(function(wisata) {
                // Skip jika ini adalah wisata awal atau tujuan (sudah ditampilkan)
                if (wisata.id_wisata == wisataAwal.id || wisata.id_wisata == wisataTujuan.id_wisata) {
                    return;
                }

                if (wisata.latitude && wisata.longitude) {
                    // Tentukan warna marker berdasarkan destinasi unggulan
                    const warnaMarker = wisata.destinasi_unggulan == 1 ? '#ffc107' : '#6c757d';
                    const iconMarker = wisata.destinasi_unggulan == 1 ? 'star' : 'map-marker-alt';
                    const ukuranMarker = wisata.destinasi_unggulan == 1 ? 20 : 18;

                    const marker = L.marker([wisata.latitude, wisata.longitude], {
                        icon: L.divIcon({
                            className: 'marker-destinasi-lain',
                            html: `<div style="background-color: ${warnaMarker}; color: white; border-radius: 50%; width: ${ukuranMarker}px; height: ${ukuranMarker}px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); opacity: 0.8;"><i class="fas fa-${iconMarker}"></i></div>`,
                            iconSize: [ukuranMarker, ukuranMarker],
                            iconAnchor: [ukuranMarker / 2, ukuranMarker / 2]
                        })
                    }).addTo(peta);

                    // Popup dengan informasi wisata
                    const popupContent = `
                        <div class="wisata-popup-hasil" style="min-width: 180px;">
                            <h6 class="fw-bold mb-2" style="font-size: 14px;">${wisata.nama_wisata}</h6>
                            ${wisata.destinasi_unggulan == 1 ? '<span class="badge bg-warning text-dark mb-2" style="font-size: 10px;">Destinasi Unggulan</span><br>' : ''}
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-map-marker-alt"></i> ${wisata.lokasi || 'Lokasi tidak tersedia'}</small><br>
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-clock"></i> ${wisata.jam_operasional || 'Jam operasional tidak tersedia'}</small><br>
                            <small class="text-muted" style="font-size: 11px;"><i class="fas fa-ticket-alt"></i> ${wisata.harga_tiket || 'Harga tiket tidak tersedia'}</small>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary" style="font-size: 10px; padding: 3px 8px;" onclick="buatRuteKe('${wisata.id_wisata}', '${wisata.nama_wisata}')">
                                    <i class="fas fa-route"></i> Rute ke Sini
                                </button>
                            </div>
                        </div>
                    `;

                    marker.bindPopup(popupContent, {
                        maxWidth: 250,
                        className: 'custom-popup-hasil'
                    });

                    // Simpan marker ke array global
                    markersDestinasiLain.push(marker);
                }
            });

            // Update counter di legend jika ada markers
            if (markersDestinasiLain.length > 0) {
                updateCounterDestinasi();
            }
        }

        // Fungsi untuk menghapus semua markers destinasi
        function hapusSemuaMarkersDestinasi(peta) {
            markersDestinasiLain.forEach(marker => {
                peta.removeLayer(marker);
            });
            markersDestinasiLain = [];
        }

        // Fungsi untuk toggle tampilan semua destinasi
        function toggleSemuaDestinasi(tampilkan) {
            const peta = window.petaGlobal; // Menggunakan referensi peta global
            const toggleLabel = $('label[for="toggleDestinasi"]');

            if (tampilkan) {
                // Muat ulang data destinasi jika belum ada
                if (markersDestinasiLain.length === 0) {
                    // Tampilkan loading pada label
                    toggleLabel.html('<i class="fas fa-spinner fa-spin me-1"></i>Memuat Destinasi...');

                    $.ajax({
                        url: '/api/wisata',
                        method: 'GET',
                        success: function(dataWisata) {
                            const wisataAwal = {
                                id: {{ $hasilRute["wisata_awal"]->id_wisata }}
                            };
                            const wisataTujuan = {
                                id_wisata: {{ $wisataTujuan->id_wisata }}
                            };
                            tampilkanSemuaDestinasi(peta, dataWisata, wisataAwal, wisataTujuan);

                            // Update label setelah selesai
                            toggleLabel.html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan Destinasi Lain');
                            updateCounterDestinasi();
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading wisata data:', error);
                            toggleLabel.html('<i class="fas fa-exclamation-triangle me-1"></i>Gagal Memuat');

                            // Reset toggle jika error
                            setTimeout(() => {
                                $('#toggleDestinasi').prop('checked', false);
                                toggleLabel.html(
                                    '<i class="fas fa-map-marker-alt me-1"></i>Tampilkan Semua Destinasi'
                                    );
                                updateCounterDestinasi();
                            }, 2000);
                        }
                    });
                } else {
                    // Tampilkan markers yang sudah ada
                    markersDestinasiLain.forEach(marker => {
                        marker.addTo(peta);
                    });

                    // Update label toggle
                    toggleLabel.html('<i class="fas fa-eye-slash me-1"></i>Sembunyikan Destinasi Lain');
                    updateCounterDestinasi();
                }
            } else {
                // Sembunyikan semua markers destinasi
                markersDestinasiLain.forEach(marker => {
                    peta.removeLayer(marker);
                });

                // Update label toggle
                toggleLabel.html('<i class="fas fa-map-marker-alt me-1"></i>Tampilkan Semua Destinasi');
                updateCounterDestinasi();
            }
        }

        // ==========================================
        // FUNGSI GAMBAR RUTE DI PETA
        // ==========================================
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

        // ==========================================
        // FUNGSI GLOBAL DAN CALLBACK
        // ==========================================

        // Fungsi global untuk membuat rute ke destinasi lain
        function buatRuteKe(idWisata, namaWisata) {
            // Konfirmasi pengguna
            if (confirm(`Apakah Anda ingin membuat rute baru ke ${namaWisata}?`)) {
                // Redirect ke halaman cari rute
                const baseUrl = '{{ route("pengunjung.cari-rute") }}';
                window.location.href = `${baseUrl}?tujuan=${idWisata}`;
            }
        }
    </script>

    <style>
        /* Info Rute Section */
        .info-rute {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        /* Leaflet Container */
        .leaflet-container {
            font-family: inherit;
        }

        /* Marker Styling */
        .marker-lokasi-awal,
        .marker-wisata-awal,
        .marker-wisata-tujuan,
        .marker-transit,
        .marker-destinasi-lain {
            border: none !important;
            background: transparent !important;
        }

        .marker-destinasi-lain:hover {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }

        .marker-destinasi-lain {
            animation: markerFadeIn 0.5s ease-in-out;
        }

        @keyframes markerFadeIn {
            from {
                opacity: 0;
                transform: scale(0.5);
            }

            to {
                opacity: 0.8;
                transform: scale(1);
            }
        }

        /* Peta Container */
        .peta-container {
            position: relative;
        }

        .peta-loading {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            color: #6c757d;
        }

        /* Legend Styling */
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

        /* Toggle Switch Styling */
        .toggle-header {
            transition: all 0.3s ease;
        }

        .toggle-header:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 8px;
        }

        .form-check.form-switch {
            padding-left: 2.5em;
            min-height: 1.5rem;
        }

        .form-check.form-switch .form-check-input {
            width: 2em;
            margin-left: -2.5em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255,255,255,1%29'/%3e%3c/svg%3e");
            background-position: left center;
            border-radius: 2em;
            transition: background-position .15s ease-in-out;
        }

        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }

        .form-check-input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }

        .form-check-label {
            font-weight: 500;
            color: #495057;
            cursor: pointer;
            user-select: none;
        }

        .form-check-label:hover {
            color: #28a745;
            transition: color 0.2s ease;
        }

        /* Leaflet Tooltip Styling */
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

        /* Popup Styling */
        .custom-popup-hasil .leaflet-popup-content-wrapper {
            border-radius: 6px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }

        .custom-popup-hasil .leaflet-popup-content {
            margin: 10px;
        }

        .wisata-popup-hasil .btn {
            transition: all 0.2s ease;
        }

        .wisata-popup-hasil .btn:hover {
            transform: scale(1.05);
        }

        /* Responsive Design */
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
    </style>
@endpush
