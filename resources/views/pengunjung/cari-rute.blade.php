@extends("template-pengunjung")
@section("title", "Pariwisata Humbahas")
@section("body")
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <form action="{{ route("pengunjung.proses-rute") }}" method="post">
                @csrf
                @method("POST")
                <div class="row">
                    <div class="card py-5 shadow-sm">
                        <div class="row d-flex justify-content-center align-items-center flex-column gy-4">
                            <h2 class="fw-bold text-center">Cari Rute Terpendek Ke Tempat Wisata</h2>
                            <input type /* Card hover effect */ .card:hover { transform: translateY(-2px); box-shadow: 0 8px
                                16px rgba(0,0,0,0.1) !important; transition: all 0.3s ease; } /* Card highlight animation */
                                .card.border-primary { border: 2px solid #007bff !important; box-shadow: 0 0 15px rgba(0,
                                123, 255, 0.3) !important; transition: all 0.5s ease; }er" name="latitude" id="latitude"
                                step="any" hidden>
                            <input type="number" name="longitude" id="longitude" step="any" hidden>
                            <div class="col-lg-6">
                                <div class="row mb-3">
                                    <label for="lokasiAwal" class="col-sm-3 col-form-label">Lokasi Awal</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <select class="form-select" name="lokasi_awal" id="lokasiAwal"
                                                data-placeholder="Pilih lokasi awal">
                                                <option value="" hidden="">Pilih Lokasi Awal</option>
                                                <option value="current" id="currentLocationOption">Lokasi Saat Ini
                                                    (Mendapatkan lokasi...)</option>
                                                <option value="dolok_sanggul" data-lat="2.252977" data-lng="98.748272">Pusat
                                                    Dolok Sanggul</option>
                                                @forelse($wisata as $item)
                                                    <option value="{{ $item->id_wisata }}" data-lat="{{ $item->latitude }}"
                                                        data-lng="{{ $item->longitude }}">
                                                        {{ $item->nama_wisata }}
                                                    </option>
                                                @empty
                                                    <option value="">Tidak ada data wisata</option>
                                                @endforelse
                                            </select>
                                            <button type="button" class="btn btn-outline-primary" id="refreshLocation"
                                                title="Refresh lokasi GPS">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Tip: Untuk akurasi terbaik, pastikan GPS aktif dan berada
                                            di area terbuka</small>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="lokasi_tujuan" class="col-sm-3 col-form-label">Lokasi Tujuan</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="lokasi_tujuan" id="lokasi_tujuan"
                                            data-placeholder="Pilih lokasi tujuan">
                                            <option value="" hidden="">Pilih Lokasi Tujuan</option>
                                            @forelse($wisata as $item)
                                                <option value="{{ $item->id_wisata }}" data-lat="{{ $item->latitude }}"
                                                    data-lng="{{ $item->longitude }}" class="wisata-option">
                                                    {{ $item->nama_wisata }}
                                                </option>
                                            @empty
                                                <option value="">Tidak ada data wisata</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <button type="submit" class="btn btn-success w-50 mx-auto">Cari Rute Terpendek</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Section Peta Destinasi Wisata -->
        <div class="container mt-5" data-aos="fade-up">
            <div class="row">
                <div class="card py-4 shadow-sm">
                    <div class="container">
                        <h3 class="fw-bold text-center mb-4">Peta Destinasi Wisata Humbahas</h3>
                        <p class="text-center text-muted mb-4">Klik pada marker untuk melihat detail destinasi wisata</p>

                        <!-- Filter Kategori -->
                        <div class="row mb-3">
                            <div class="col-md-6 mx-auto">
                                <select class="form-select" id="filterKategori">
                                    <option value="">Semua Kategori</option>
                                    <!-- Kategori akan diisi via JavaScript -->
                                </select>
                            </div>
                        </div>

                        <!-- Peta -->
                        <div class="row">
                            <div class="col-12">
                                <div id="petaDestinasi" style="height: 500px; border: 2px solid #ddd; border-radius: 8px;">
                                    <div class="d-flex justify-content-center align-items-center h-100">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Memuat peta...</span>
                                        </div>
                                        <span class="ms-2">Memuat peta destinasi wisata...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push("script")
    <script type="text/javascript">
        $(document).ready(function() {
            let currentLat = null;
            let currentLng = null;
            let petaDestinasi = null;
            let markersDestinasi = [];
            let markerLokasiSaya = null;

            // Dapatkan lokasi pengguna saat halaman dimuat
            dapatkanLokasiPengguna();

            // Inisialisasi peta destinasi
            inisialisasiPetaDestinasi();

            // Auto-select destinasi dari parameter URL
            autoSelectDestinasiFromURL();

            // Event handler untuk filter kategori
            $('#filterKategori').change(function() {
                filterDestinasiByKategori($(this).val());
            });

            // Event handler untuk tombol refresh lokasi
            $('#refreshLocation').click(function() {
                $(this).find('i').addClass('fa-spin');
                $('#currentLocationOption').text('Lokasi Saat Ini (Mencari lokasi...)');
                dapatkanLokasiPengguna();

                // Hentikan animasi spin setelah 15 detik
                setTimeout(() => {
                    $(this).find('i').removeClass('fa-spin');
                }, 15000);
            });

            // Event handler untuk perubahan dropdown lokasi awal
            $('#lokasiAwal').change(function() {
                const selectedOption = $(this).find('option:selected');
                const value = selectedOption.val();

                if (value === 'current') {
                    // Gunakan koordinat lokasi saat ini
                    if (currentLat && currentLng) {
                        $('#latitude').val(currentLat);
                        $('#longitude').val(currentLng);
                    }
                } else if (value === 'dolok_sanggul') {
                    // Gunakan koordinat Pusat Dolok Sanggul
                    $('#latitude').val(selectedOption.data('lat'));
                    $('#longitude').val(selectedOption.data('lng'));
                } else if (value) {
                    // Gunakan koordinat wisata yang dipilih
                    $('#latitude').val(selectedOption.data('lat'));
                    $('#longitude').val(selectedOption.data('lng'));
                } else {
                    // Reset koordinat jika tidak ada yang dipilih
                    $('#latitude').val('');
                    $('#longitude').val('');
                }

                // Update pilihan lokasi tujuan
                updatePilihanTujuan();
            });

            // Fungsi untuk mengupdate pilihan lokasi tujuan
            function updatePilihanTujuan() {
                const lokasiAwalValue = $('#lokasiAwal').val();

                // Reset semua opsi wisata di lokasi tujuan
                $('#lokasi_tujuan .wisata-option').prop('disabled', false);

                // Disable opsi yang sama dengan lokasi awal (jika memilih wisata)
                if (lokasiAwalValue && lokasiAwalValue !== 'current' && lokasiAwalValue !== 'dolok_sanggul') {
                    $(`#lokasi_tujuan option[value="${lokasiAwalValue}"]`).prop('disabled', true);

                    // Jika lokasi tujuan yang dipilih sama dengan lokasi awal, reset pilihan
                    if ($('#lokasi_tujuan').val() === lokasiAwalValue) {
                        $('#lokasi_tujuan').val('');
                    }
                }
            }

            function dapatkanLokasiPengguna() {
                if (navigator.geolocation) {
                    // Tampilkan loading dengan akurasi info
                    $('#currentLocationOption').text('Lokasi Saat Ini (Mencari lokasi yang akurat...)');

                    navigator.geolocation.getCurrentPosition(
                        // Callback sukses
                        function(posisi) {
                            const lintang = posisi.coords.latitude;
                            const bujur = posisi.coords.longitude;
                            const akurasi = posisi.coords.accuracy;

                            console.log('Koordinat ditemukan:', {
                                latitude: lintang,
                                longitude: bujur,
                                accuracy: akurasi + ' meter'
                            });

                            // Simpan koordinat lokasi saat ini
                            currentLat = lintang;
                            currentLng = bujur;

                            // Update teks opsi lokasi saat ini
                            dapatkanNamaLokasi(lintang, bujur, akurasi);

                            // Set sebagai pilihan default jika belum ada yang dipilih
                            if ($('#lokasiAwal').val() === '') {
                                $('#lokasiAwal').val('current');
                                $('#latitude').val(lintang);
                                $('#longitude').val(bujur);
                            }

                            // Update marker lokasi saya di peta
                            tambahkanMarkerLokasiSaya();
                        },
                        // Callback kesalahan
                        function(kesalahan) {
                            tanganiErrorLokasi(kesalahan);
                        },
                        // Opsi untuk akurasi maksimal
                        {
                            enableHighAccuracy: true, // Gunakan GPS jika tersedia
                            maximumAge: 0, // Jangan gunakan cache lokasi
                            timeout: 15000 // Perpanjang timeout untuk akurasi lebih baik
                        }
                    );

                    // Coba watchPosition untuk mendapatkan lokasi yang lebih akurat
                    if (navigator.geolocation.watchPosition) {
                        let watchId = navigator.geolocation.watchPosition(
                            function(posisi) {
                                const lintang = posisi.coords.latitude;
                                const bujur = posisi.coords.longitude;
                                const akurasi = posisi.coords.accuracy;

                                // Jika mendapat akurasi yang lebih baik (< 50 meter), update koordinat
                                if (akurasi < 50) {
                                    console.log('Lokasi yang lebih akurat ditemukan:', {
                                        latitude: lintang,
                                        longitude: bujur,
                                        accuracy: akurasi + ' meter'
                                    });

                                    currentLat = lintang;
                                    currentLng = bujur;

                                    // Update koordinat jika lokasi saat ini yang dipilih
                                    if ($('#lokasiAwal').val() === 'current') {
                                        $('#latitude').val(lintang);
                                        $('#longitude').val(bujur);
                                    }

                                    dapatkanNamaLokasi(lintang, bujur, akurasi);

                                    // Update marker lokasi saya di peta
                                    tambahkanMarkerLokasiSaya();

                                    // Hentikan watch setelah mendapat akurasi yang baik
                                    navigator.geolocation.clearWatch(watchId);
                                }
                            },
                            function(error) {
                                console.log('Watch position error:', error);
                            }, {
                                enableHighAccuracy: true,
                                maximumAge: 0,
                                timeout: 10000
                            }
                        );

                        // Hentikan watch setelah 30 detik
                        setTimeout(function() {
                            navigator.geolocation.clearWatch(watchId);
                        }, 30000);
                    }
                } else {
                    $('#currentLocationOption').text('Geolocation tidak didukung oleh browser ini');
                }
            }

            // Menangani kesalahan lokasi
            function tanganiErrorLokasi(kesalahan) {
                let errorMessage = '';
                switch (kesalahan.code) {
                    case kesalahan.PERMISSION_DENIED:
                        errorMessage = 'Akses lokasi ditolak';
                        break;
                    case kesalahan.POSITION_UNAVAILABLE:
                        errorMessage = 'Informasi lokasi tidak tersedia';
                        break;
                    case kesalahan.TIMEOUT:
                        errorMessage = 'Waktu permintaan lokasi habis';
                        break;
                    case kesalahan.UNKNOWN_ERROR:
                        errorMessage = 'Terjadi kesalahan saat mendapatkan lokasi';
                        break;
                }
                $('#currentLocationOption').text(`Lokasi Saat Ini (${errorMessage})`);
            }

            // Mendapatkan nama lokasi menggunakan reverse geocoding
            function dapatkanNamaLokasi(lintang, bujur, akurasi) {
                $.ajax({
                    url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lintang}&lon=${bujur}&zoom=18&addressdetails=1`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.display_name) {
                            // Ambil bagian yang relevan dari nama alamat
                            const address = data.address || {};
                            const shortName = address.village || address.town || address.city || address
                                .county || 'Lokasi Saat Ini';

                            // Tampilkan dengan info akurasi jika tersedia
                            let locationText = `Lokasi Saat Ini (${shortName})`;
                            if (akurasi) {
                                locationText += ` - Akurasi: ${Math.round(akurasi)}m`;
                            }
                            $('#currentLocationOption').text(locationText);
                        } else {
                            let locationText =
                                `Lokasi Saat Ini (${lintang.toFixed(6)}, ${bujur.toFixed(6)})`;
                            if (akurasi) {
                                locationText += ` - Akurasi: ${Math.round(akurasi)}m`;
                            }
                            $('#currentLocationOption').text(locationText);
                        }
                    },
                    error: function() {
                        let locationText =
                            `Lokasi Saat Ini (${lintang.toFixed(6)}, ${bujur.toFixed(6)})`;
                        if (akurasi) {
                            locationText += ` - Akurasi: ${Math.round(akurasi)}m`;
                        }
                        $('#currentLocationOption').text(locationText);
                    }
                });
            }

            // Fungsi untuk inisialisasi peta destinasi
            function inisialisasiPetaDestinasi() {
                setTimeout(function() {
                    // Hapus loading indicator
                    $('#petaDestinasi').empty();

                    // Inisialisasi peta dengan koordinat Humbahas
                    petaDestinasi = L.map('petaDestinasi').setView([2.288971175704209, 98.53564577695926],
                        10);

                    // Tambahkan tile layer
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors | Kabupaten Humbang Hasundutan'
                    }).addTo(petaDestinasi);

                    // Muat data destinasi wisata
                    muatDataDestinasi();
                }, 1000);
            }

            // Fungsi untuk memuat data destinasi wisata
            function muatDataDestinasi() {
                $.ajax({
                    url: '/api/wisata',
                    method: 'GET',
                    success: function(data) {
                        console.log('Data wisata loaded:', data);

                        // Simpan data untuk filtering
                        window.dataWisata = data;

                        // Tampilkan semua destinasi
                        tampilkanDestinasi(data);

                        // Isi filter kategori
                        isiFilterKategori(data);

                        // Tambahkan marker lokasi pengguna jika tersedia
                        tambahkanMarkerLokasiSaya();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading wisata data:', error);
                        $('#petaDestinasi').html(
                            '<div class="d-flex justify-content-center align-items-center h-100 text-danger">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>' +
                            'Gagal memuat data destinasi wisata' +
                            '</div>'
                        );
                    }
                });
            }

            // Fungsi untuk menampilkan destinasi di peta
            function tampilkanDestinasi(dataWisata) {
                // Hapus marker lama
                markersDestinasi.forEach(marker => petaDestinasi.removeLayer(marker));
                markersDestinasi = [];

                dataWisata.forEach(function(wisata) {
                    if (wisata.latitude && wisata.longitude) {
                        // Tentukan warna marker berdasarkan destinasi unggulan
                        const warnaMarker = wisata.destinasi_unggulan == 1 ? '#dc3545' : '#007bff';
                        const iconMarker = wisata.destinasi_unggulan == 1 ? 'star' : 'map-marker-alt';

                        const marker = L.marker([wisata.latitude, wisata.longitude], {
                            icon: L.divIcon({
                                className: 'marker-destinasi',
                                html: `<div style="background-color: ${warnaMarker}; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-${iconMarker}"></i></div>`,
                                iconSize: [25, 25],
                                iconAnchor: [12, 12]
                            })
                        }).addTo(petaDestinasi);

                        // Popup dengan informasi wisata
                        const popupContent = `
                            <div class="wisata-popup" style="min-width: 200px;">
                                <h6 class="fw-bold mb-2">${wisata.nama_wisata}</h6>
                                ${wisata.destinasi_unggulan == 1 ? '<span class="badge bg-danger mb-2">Destinasi Unggulan</span><br>' : ''}
                                <small class="text-muted"><i class="fas fa-map-marker-alt"></i> ${wisata.lokasi || 'Lokasi tidak tersedia'}</small><br>
                                <small class="text-muted"><i class="fas fa-clock"></i> ${wisata.jam_operasional || 'Jam operasional tidak tersedia'}</small><br>
                                <small class="text-muted"><i class="fas fa-ticket-alt"></i> ${wisata.harga_tiket || 'Harga tiket tidak tersedia'}</small>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary" onclick="pilihSebagaiTujuan('${wisata.id_wisata}', '${wisata.nama_wisata}')">
                                        <i class="fas fa-route"></i> Jadikan Tujuan
                                    </button>
                                    <button class="btn btn-sm btn-success ms-1" onclick="pilihSebagaiAsal('${wisata.id_wisata}', '${wisata.nama_wisata}')">
                                        <i class="fas fa-flag"></i> Jadikan Asal
                                    </button>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent, {
                            maxWidth: 300,
                            className: 'custom-popup'
                        });

                        // Simpan marker untuk filtering
                        marker.wisataData = wisata;
                        markersDestinasi.push(marker);
                    }
                });
            }

            // Fungsi untuk mengisi filter kategori
            function isiFilterKategori(dataWisata) {
                const kategoriSet = new Set();

                dataWisata.forEach(wisata => {
                    if (wisata.kategori && Array.isArray(wisata.kategori)) {
                        wisata.kategori.forEach(kat => {
                            kategoriSet.add(kat.nama_kategori);
                        });
                    }
                });

                const filterSelect = $('#filterKategori');
                kategoriSet.forEach(kategori => {
                    filterSelect.append(`<option value="${kategori}">${kategori}</option>`);
                });
            }

            // Fungsi untuk filter destinasi berdasarkan kategori
            function filterDestinasiByKategori(kategoriTerpilih) {
                markersDestinasi.forEach(marker => {
                    const wisata = marker.wisataData;
                    let tampilkan = true;

                    if (kategoriTerpilih && kategoriTerpilih !== '') {
                        tampilkan = false;
                        if (wisata.kategori && Array.isArray(wisata.kategori)) {
                            wisata.kategori.forEach(kat => {
                                if (kat.nama_kategori === kategoriTerpilih) {
                                    tampilkan = true;
                                }
                            });
                        }
                    }

                    if (tampilkan) {
                        petaDestinasi.addLayer(marker);
                    } else {
                        petaDestinasi.removeLayer(marker);
                    }
                });
            }

            // Fungsi untuk menambahkan marker lokasi pengguna
            function tambahkanMarkerLokasiSaya() {
                if (currentLat && currentLng) {
                    // Hapus marker lama jika ada
                    if (markerLokasiSaya) {
                        petaDestinasi.removeLayer(markerLokasiSaya);
                    }

                    markerLokasiSaya = L.marker([currentLat, currentLng], {
                        icon: L.divIcon({
                            className: 'marker-lokasi-saya',
                            html: '<div style="background-color: #28a745; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"><i class="fas fa-user"></i></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(petaDestinasi);

                    markerLokasiSaya.bindPopup('<b>Lokasi Anda Saat Ini</b>');
                }
            }
        });

        // Fungsi global untuk memilih destinasi dari peta
        function pilihSebagaiTujuan(idWisata, namaWisata) {
            $('#lokasi_tujuan').val(idWisata);

            // Trigger change event untuk update koordinat
            $('#lokasi_tujuan').trigger('change');

            // Scroll ke form
            $('html, body').animate({
                scrollTop: $('#lokasiAwal').offset().top - 100
            }, 500);

            // Berikan feedback
            showAlert(`${namaWisata} dipilih sebagai lokasi tujuan`, 'success');
        }

        function pilihSebagaiAsal(idWisata, namaWisata) {
            $('#lokasiAwal').val(idWisata);

            // Trigger change event untuk update koordinat
            $('#lokasiAwal').trigger('change');

            // Scroll ke form
            $('html, body').animate({
                scrollTop: $('#lokasiAwal').offset().top - 100
            }, 500);

            // Berikan feedback
            showAlert(`${namaWisata} dipilih sebagai lokasi awal`, 'success');
        }

        // Fungsi untuk auto-select destinasi dari parameter URL
        function autoSelectDestinasiFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const tujuanId = urlParams.get('tujuan');

            if (tujuanId) {
                // Set lokasi tujuan
                $('#lokasi_tujuan').val(tujuanId);

                // Trigger change event untuk update koordinat
                $('#lokasi_tujuan').trigger('change');

                // Scroll ke form dan highlight
                $('html, body').animate({
                    scrollTop: $('#lokasiAwal').offset().top - 100
                }, 1000);

                // Highlight form dengan animasi
                $('.card').addClass('border-primary');
                setTimeout(() => {
                    $('.card').removeClass('border-primary');
                }, 3000);

                // Berikan notifikasi
                const selectedName = $('#lokasi_tujuan option:selected').text();
                if (selectedName && selectedName !== 'Pilih Lokasi Tujuan') {
                    showAlert(`${selectedName} telah dipilih sebagai tujuan`, 'info');
                }

                // Hapus parameter dari URL
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        }

        // Fungsi untuk menampilkan alert
        function showAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed"
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            $('body').append(alertHtml);

            // Auto dismiss setelah 3 detik
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 3000);
        }
    </script>
@endpush

@push("style")
    <style>
        /* Styling untuk peta destinasi */
        #petaDestinasi {
            font-family: inherit;
        }

        .marker-destinasi,
        .marker-lokasi-saya {
            border: none !important;
            background: transparent !important;
        }

        .legend-peta {
            background-color: #f8f9fa;
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

        /* Custom popup styling */
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .custom-popup .leaflet-popup-content {
            margin: 12px;
        }

        .wisata-popup .btn {
            font-size: 11px;
            padding: 4px 8px;
        }

        /* Loading spinner */
        .spinner-border {
            width: 2rem;
            height: 2rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            #petaDestinasi {
                height: 350px !important;
            }

            .legend-peta .row {
                flex-direction: column;
            }

            .wisata-popup {
                min-width: 150px !important;
            }

            .wisata-popup .btn {
                font-size: 10px;
                padding: 3px 6px;
                margin: 1px;
            }
        }

        /* Filter styling */
        #filterKategori {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        #filterKategori:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Card hover effect */
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease;
        }

        /* Section spacing */
        .container[data-aos="fade-up"]+.container[data-aos="fade-up"] {
            margin-top: 3rem !important;
        }

        /* Alert styling */
        .alert.position-fixed {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Button hover effects */
        .btn-sm:hover {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }
    </style>
@endpush
