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
                                    <div class="d-flex gap-3">
                                        <!-- Toggle Jenis Rute -->
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggleJenisRute" checked>
                                            <label class="form-check-label" for="toggleJenisRute">
                                                <i class="fas fa-route me-1"></i>
                                                Dengan Transit
                                            </label>
                                        </div>

                                        <!-- Toggle Destinasi -->
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="toggleDestinasi" checked>
                                            <label class="form-check-label" for="toggleDestinasi">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                Semua Destinasi
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontrol Rute Alternatif -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="kontrol-rute-alternatif" id="kontrolRuteAlternatif">
                                            <button class="btn btn-rute-alternatif btn-outline-primary btn-sm active"
                                                id="btnRute1" data-rute="1">
                                                <i class="fas fa-route"></i> Rute 1 (Transit)
                                            </button>
                                            <button class="btn btn-rute-alternatif btn-outline-success btn-sm"
                                                id="btnRute2" data-rute="2">
                                                <i class="fas fa-route"></i> Rute 2 (Langsung)
                                            </button>
                                        </div>
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
                                                <small>Jalur Rute 1 (Transit)</small>
                                            </div>
                                            <div class="legend-item mb-2">
                                                <div class="legend-line me-2"
                                                    style="background-color: #28a745; height: 3px; width: 20px;"></div>
                                                <small>Jalur Rute 2 (Langsung)</small>
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
                                        <small class="text-muted" id="infoRuteAktif">
                                            <i class="fas fa-route text-primary"></i> Rute 1 (Transit) sedang aktif
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
        let ruteAktif = 1; // 1 = rute dengan transit, 2 = rute langsung
        let garisRute1 = null; // Garis untuk rute 1 (transit)
        let garisRute2 = null; // Garis untuk rute 2 (langsung)
        let infoRute1 = null; // Info jarak dan waktu untuk rute 1
        let infoRute2 = null; // Info jarak dan waktu untuk rute 2

        // ==========================================
        // INISIALISASI DOCUMENT READY
        // ==========================================
        $(document).ready(function() {
            inisialisasiPeta();

            // Event handler untuk toggle destinasi
            $('#toggleDestinasi').change(function() {
                toggleSemuaDestinasi($(this).is(':checked'));
            });

            // Event handler untuk toggle jenis rute
            $('#toggleJenisRute').change(function() {
                toggleJenisRute($(this).is(':checked'));
            });

            // Event handler untuk tombol rute alternatif
            $('#btnRute1').click(function() {
                pilihRuteAlternatif(1);
            });

            $('#btnRute2').click(function() {
                pilihRuteAlternatif(2);
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
        // FUNGSI KONTROL RUTE
        // ==========================================

        // Fungsi untuk toggle jenis rute (dengan transit atau langsung)
        function toggleJenisRute(denganTransit) {
            const label = $('label[for="toggleJenisRute"]');
            const kontrolRute = $('#kontrolRuteAlternatif');

            if (denganTransit) {
                label.html('<i class="fas fa-route me-1"></i>Dengan Transit');
                kontrolRute.show();
                // Aktifkan rute 1 (transit) secara default
                if (ruteAktif !== 1) {
                    pilihRuteAlternatif(1);
                }
            } else {
                label.html('<i class="fas fa-route me-1"></i>Rute Langsung');
                kontrolRute.hide();
                // Langsung aktifkan rute 2 (langsung)
                if (ruteAktif !== 2) {
                    pilihRuteAlternatif(2);
                }
            }
        }

        // Fungsi untuk memilih rute alternatif
        function pilihRuteAlternatif(nomorRute) {
            // Cek apakah data rute sudah tersedia
            if (nomorRute === 1 && !garisRute1) {
                console.log('Data rute 1 belum tersedia, menunggu...');
                $('#infoRuteAktif').html('<i class="fas fa-spinner fa-spin text-primary"></i> Memuat rute 1...');
                setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
                return;
            }

            if (nomorRute === 2 && !garisRute2) {
                console.log('Data rute 2 belum tersedia, menunggu...');
                $('#infoRuteAktif').html('<i class="fas fa-spinner fa-spin text-success"></i> Memuat rute 2...');
                setTimeout(() => pilihRuteAlternatif(nomorRute), 500);
                return;
            }

            ruteAktif = nomorRute;

            // Update tombol aktif
            $('#btnRute1').removeClass('active btn-primary').addClass('btn-outline-primary');
            $('#btnRute2').removeClass('active btn-success').addClass('btn-outline-success');

            if (nomorRute === 1) {
                $('#btnRute1').addClass('active btn-primary').removeClass('btn-outline-primary');
                $('#infoRuteAktif').html('<i class="fas fa-route text-primary"></i> Rute 1 (Transit) sedang aktif');
            } else {
                $('#btnRute2').addClass('active btn-success').removeClass('btn-outline-success');
                $('#infoRuteAktif').html('<i class="fas fa-route text-success"></i> Rute 2 (Langsung) sedang aktif');
            }

            // Tampilkan/sembunyikan garis rute
            tampilkanRuteAktif();

            // Update info rute di UI
            updateInfoRute();
        }

        // Fungsi untuk menampilkan rute yang aktif
        function tampilkanRuteAktif() {
            const peta = window.petaGlobal;

            if (!peta) return;

            // Sembunyikan semua garis rute
            if (garisRute1 && peta.hasLayer(garisRute1)) {
                peta.removeLayer(garisRute1);
            }
            if (garisRute2 && peta.hasLayer(garisRute2)) {
                peta.removeLayer(garisRute2);
            }

            // Tampilkan rute yang aktif
            if (ruteAktif === 1 && garisRute1) {
                if (garisRute1 instanceof L.LayerGroup) {
                    // Jika layer group, tambahkan semua layer
                    garisRute1.eachLayer(layer => {
                        if (!peta.hasLayer(layer)) {
                            layer.addTo(peta);
                        }
                    });
                } else {
                    // Jika single layer
                    garisRute1.addTo(peta);
                }
            } else if (ruteAktif === 2 && garisRute2) {
                garisRute2.addTo(peta);
            }
        }

        // Fungsi untuk update info rute di UI
        function updateInfoRute() {
            const infoRute = ruteAktif === 1 ? infoRute1 : infoRute2;

            if (infoRute) {
                // Update jarak dan waktu di info panel
                const jarakElement = $('.info-rute .row').eq(3).find('.col-7');
                const waktuElement = $('.info-rute .row').eq(4).find('.col-7');

                if (infoRute.jarak && infoRute.jarak !== 0) {
                    jarakElement.text(`${number_format(infoRute.jarak, 2)} km`);
                }

                if (infoRute.waktu && infoRute.waktu !== 'Menghitung...') {
                    waktuElement.text(infoRute.waktu);
                }

                console.log(`Info rute ${ruteAktif} diupdate:`, infoRute);
            }
        }

        // Fungsi helper untuk format number
        function number_format(number, decimals) {
            return parseFloat(number).toFixed(decimals);
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

                // Inisialisasi default rute aktif setelah semua data dimuat
                setTimeout(() => {
                    pilihRuteAlternatif(1); // Default ke rute 1 (transit)
                }, 1500);
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
            // ==========================================
            // RUTE 1: Dengan Transit (Rute Original)
            // ==========================================
            console.log('Membuat Rute 1 (Transit)...');

            // Garis dari lokasi awal ke wisata terdekat
            dapatkanDanGambarRuteJalan(peta, lokasiAwal, wisataAwal, '#28a745', 'Jalur ke titik awal', function(garis,
            info) {
                // Callback untuk menyimpan info rute 1
                if (!infoRute1) infoRute1 = {};
                if (info) {
                    infoRute1.jarak_awal = info.jarak || 0;
                    infoRute1.waktu_awal = info.durasi || 0;
                }
            });

            // Garis rute utama antar wisata untuk rute 1
            if (koordinatRute.length > 1) {
                gambarRuteAntarWisata(peta, koordinatRute, 1); // Parameter 1 untuk rute transit
            } else if (koordinatRute.length === 1) {
                dapatkanDanGambarRuteJalan(peta, wisataAwal, wisataTujuan, '#007bff', 'Jalur utama rute 1', function(garis,
                    info) {
                    garisRute1 = garis;
                    if (info) {
                        infoRute1 = {
                            jarak: info.jarak || 0,
                            waktu: info.durasi || 'Menghitung...'
                        };
                    }
                    // Sembunyikan rute 1 jika bukan rute aktif
                    if (ruteAktif !== 1) {
                        peta.removeLayer(garis);
                    }
                });
            }

            // ==========================================
            // RUTE 2: Langsung (Tanpa Transit)
            // ==========================================
            console.log('Membuat Rute 2 (Langsung)...');

            // Rute langsung dari lokasi awal ke tujuan akhir
            dapatkanDanGambarRuteJalan(peta, lokasiAwal, wisataTujuan, '#28a745', 'Jalur langsung rute 2', function(garis,
                info) {
                garisRute2 = garis;
                if (info) {
                    infoRute2 = {
                        jarak: info.jarak || 0,
                        waktu: info.durasi || 'Menghitung...'
                    };
                }
                // Sembunyikan rute 2 secara default, tampilkan hanya jika aktif
                if (ruteAktif !== 2) {
                    peta.removeLayer(garis);
                }
                console.log('Rute 2 (Langsung) selesai dibuat:', infoRute2);
            });

            // Sesuaikan viewport untuk menampilkan seluruh rute
            sesuaikanViewportPeta(peta, lokasiAwal, wisataAwal, wisataTujuan, koordinatRute);
        }

        function gambarRuteAntarWisata(peta, koordinatRute, nomorRute = 1) {
            console.log(`Membuat rute antar wisata untuk Rute ${nomorRute}...`);

            let totalJarak = 0;
            let totalWaktu = 0;
            let garisRute = [];

            // Gambar rute antar titik wisata secara berurutan
            for (let i = 0; i < koordinatRute.length - 1; i++) {
                const asal = koordinatRute[i];
                const tujuan = koordinatRute[i + 1];

                dapatkanDanGambarRuteJalan(peta, asal, tujuan, '#007bff', `Jalur ${i + 1} rute ${nomorRute}`, function(
                    garis, info) {
                    garisRute.push(garis);

                    if (info) {
                        totalJarak += parseFloat(info.jarak) || 0;
                        totalWaktu += parseFloat(info.durasi) || 0;
                    }

                    // Jika ini adalah garis terakhir, simpan info rute
                    if (i === koordinatRute.length - 2) {
                        if (nomorRute === 1) {
                            // Buat layer group untuk rute 1
                            garisRute1 = L.layerGroup(garisRute);
                            infoRute1 = {
                                jarak: totalJarak,
                                waktu: `${Math.round(totalWaktu)} menit`
                            };
                            console.log('Rute 1 (Transit) selesai dibuat:', infoRute1);
                        }
                    }
                });

                // Tambahkan marker untuk titik transit (kecuali titik awal dan akhir)
                if (i > 0 && nomorRute === 1) { // Hanya tampilkan transit untuk rute 1
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

        function dapatkanDanGambarRuteJalan(peta, koordinatAsal, koordinatTujuan, warna, keterangan, callback = null) {
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

                        // Panggil callback jika ada
                        if (callback) {
                            callback(garis, {
                                jarak: response.jarak,
                                durasi: response.durasi,
                                fallback: response.fallback
                            });
                        }
                    }
                },
                error: function() {
                    // Fallback: gambar garis lurus jika API gagal
                    console.warn('Gagal mendapatkan rute jalan, menggunakan garis lurus');
                    const ruteLangsung = [
                        [koordinatAsal.lat, koordinatAsal.lng],
                        [koordinatTujuan.lat, koordinatTujuan.lng]
                    ];
                    const garis = gambarGarisRute(peta, ruteLangsung, warna, keterangan + ' (garis lurus)');

                    // Panggil callback dengan info fallback
                    if (callback) {
                        callback(garis, {
                            jarak: null,
                            durasi: null,
                            fallback: true
                        });
                    }
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

        /* Rute Alternatif Button Styling */
        .kontrol-rute-alternatif {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        .btn-rute-alternatif {
            transition: all 0.3s ease;
            font-weight: 500;
            border-radius: 6px;
        }

        .btn-rute-alternatif:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-rute-alternatif.active {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .btn-rute-alternatif i {
            margin-right: 5px;
        }

        /* Info Rute Aktif Styling */
        #infoRuteAktif {
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 4px;
            background-color: rgba(0, 123, 255, 0.1);
            border: 1px solid rgba(0, 123, 255, 0.2);
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

            .kontrol-rute-alternatif {
                padding: 8px;
            }

            .btn-rute-alternatif {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>
@endpush
