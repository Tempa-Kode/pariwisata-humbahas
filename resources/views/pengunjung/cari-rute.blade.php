@extends("template-pengunjung")
@section("title", "Pariwisata Humbahas")
@section("body")
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <form action="{{ route("pengunjung.proses-rute") }}" method="post" id="formCariRute">
                @csrf
                @method("POST")
                <div class="row">
                    <div class="card py-5 shadow-sm">
                        <div class="row d-flex justify-content-center align-items-center flex-column gy-4">
                            <h2 class="fw-bold text-center">Cari Rute Terpendek Ke Tempat Wisata</h2>
                            <input type="hidden" name="latitude" id="latitude" step="any">
                            <input type="hidden" name="longitude" id="longitude" step="any">
                            <input type="hidden" name="nama_lokasi_custom" id="namaLokasiCustom">
                            <input type="hidden" name="tipe_lokasi" id="tipeLokasiValue">

                            <div class="col-lg-6">
                                <!-- Pilihan Tipe Lokasi Awal -->
                                <div class="row mb-3">
                                    <label for="tipeLokasiAwal" class="col-sm-3 col-form-label">Tipe Lokasi</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" id="tipeLokasiAwal">
                                            <option value="predefined">Lokasi Tersedia (GPS/Daftar Wisata)</option>
                                            <option value="search">Cari Lokasi di Maps</option>
                                        </select>
                                        <small class="text-muted">Pilih cara menentukan lokasi awal</small>
                                    </div>
                                </div>

                                <!-- Form Lokasi Predefined -->
                                <div id="formLokasiPredefined">
                                    <div class="row mb-3">
                                        <label for="lokasiAwal" class="col-sm-3 col-form-label">Lokasi Awal</label>
                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <select class="form-select" name="lokasi_awal" id="lokasiAwal"
                                                    data-placeholder="Pilih lokasi awal">
                                                    <option value="" hidden="">Pilih Lokasi Awal</option>
                                                    <option value="current" id="currentLocationOption">Lokasi Saat Ini
                                                        (Mendapatkan lokasi...)</option>
                                                    <option value="dolok_sanggul" data-lat="2.252977" data-lng="98.748272">
                                                        Pusat
                                                        Dolok Sanggul</option>
                                                    @forelse($wisata as $item)
                                                        <option value="{{ $item->id_wisata }}"
                                                            data-lat="{{ $item->latitude }}"
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
                                            <small class="text-muted">Tip: Untuk akurasi terbaik, pastikan GPS aktif dan
                                                berada
                                                di area terbuka</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Pencarian Lokasi -->
                                <div id="formLokasiSearch" style="display: none;">
                                    <div class="row mb-3">
                                        <label for="searchLokasi" class="col-sm-3 col-form-label">Cari Lokasi</label>
                                        <div class="col-sm-9">
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" id="searchLokasi"
                                                    placeholder="Contoh: Pasar Dolok Sanggul, Hotel...">
                                                <button type="button" class="btn btn-primary" id="btnCariLokasi">
                                                    <i class="fas fa-search"></i> Cari
                                                </button>
                                            </div>
                                            <small class="text-muted">Ketik nama lokasi dan tekan Enter atau klik
                                                Cari</small>
                                        </div>
                                    </div>

                                    <!-- Hasil Pencarian -->
                                    <div class="row mb-3" id="hasilPencarianContainer" style="display: none;">
                                        <div class="col-sm-9 offset-sm-3">
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-list"></i> Hasil Pencarian</strong>
                                                </div>
                                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                                    <div id="listHasilPencarian"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Lokasi Terpilih -->
                                    <div class="row mb-3" id="lokasiTerpilihContainer" style="display: none;">
                                        <div class="col-sm-9 offset-sm-3">
                                            <div class="alert alert-success">
                                                <strong><i class="fas fa-map-marker-alt"></i> Lokasi Terpilih:</strong>
                                                <p class="mb-1 mt-2" id="namaLokasiTerpilih"></p>
                                                <small class="text-muted" id="koordinatLokasiTerpilih"></small>
                                                <button type="button" class="btn btn-sm btn-outline-danger float-end"
                                                    id="btnHapusLokasi">
                                                    <i class="fas fa-times"></i> Ganti
                                                </button>
                                            </div>
                                        </div>
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
                                    <button type="submit" class="btn btn-success w-50 mx-auto">Cari Rute
                                        Terpendek</button>
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
                                <div id="petaDestinasi"
                                    style="height: 500px; border: 2px solid #ddd; border-radius: 8px;">
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

            // Event handler untuk perubahan tipe lokasi
            $('#tipeLokasiAwal').change(function() {
                const tipe = $(this).val();

                if (tipe === 'search') {
                    // Tampilkan form pencarian, sembunyikan form predefined
                    $('#formLokasiPredefined').hide();
                    $('#formLokasiSearch').show();
                    $('#lokasiAwal').prop('required', false);
                    $('#tipeLokasiValue').val('search');
                } else {
                    // Tampilkan form predefined, sembunyikan form pencarian
                    $('#formLokasiPredefined').show();
                    $('#formLokasiSearch').hide();
                    $('#lokasiAwal').prop('required', true);
                    $('#tipeLokasiValue').val('predefined');

                    // Reset form pencarian
                    resetFormPencarian();
                }
            });

            // Event handler untuk pencarian lokasi
            $('#btnCariLokasi').click(function() {
                cariLokasiDiMaps();
            });

            // Enter key pada input pencarian
            $('#searchLokasi').keypress(function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    cariLokasiDiMaps();
                }
            });

            // Event handler untuk menghapus lokasi terpilih
            $('#btnHapusLokasi').click(function() {
                resetFormPencarian();
            });

            // Validasi form sebelum submit
            $('#formCariRute').submit(function(e) {
                const tipeLokasiAwal = $('#tipeLokasiAwal').val();
                const lokasiTujuan = $('#lokasi_tujuan').val();

                // Validasi lokasi tujuan
                if (!lokasiTujuan) {
                    e.preventDefault();
                    showAlert('Mohon pilih lokasi tujuan', 'warning');
                    $('#lokasi_tujuan').focus();
                    return false;
                }

                // Validasi berdasarkan tipe lokasi
                if (tipeLokasiAwal === 'predefined') {
                    const lokasiAwal = $('#lokasiAwal').val();
                    const latitude = $('#latitude').val();
                    const longitude = $('#longitude').val();

                    if (!lokasiAwal) {
                        e.preventDefault();
                        showAlert('Mohon pilih lokasi awal', 'warning');
                        $('#lokasiAwal').focus();
                        return false;
                    }

                    if (!latitude || !longitude) {
                        e.preventDefault();
                        showAlert('Koordinat lokasi awal tidak valid. Silakan pilih ulang.', 'warning');
                        $('#lokasiAwal').focus();
                        return false;
                    }
                } else if (tipeLokasiAwal === 'search') {
                    const latitude = $('#latitude').val();
                    const longitude = $('#longitude').val();
                    const namaLokasi = $('#namaLokasiCustom').val();

                    if (!latitude || !longitude || !namaLokasi) {
                        e.preventDefault();
                        showAlert('Mohon cari dan pilih lokasi awal terlebih dahulu', 'warning');
                        $('#searchLokasi').focus();
                        return false;
                    }
                }

                // Jika semua validasi lolos, tampilkan loading
                const btnSubmit = $(this).find('button[type="submit"]');
                btnSubmit.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Mencari Rute...');

                return true;
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

            // Fungsi untuk mencari lokasi di Maps menggunakan Nominatim API
            function cariLokasiDiMaps() {
                const query = $('#searchLokasi').val().trim();

                if (query === '') {
                    showAlert('Mohon masukkan nama lokasi yang ingin dicari', 'warning');
                    return;
                }

                // Tampilkan loading
                $('#btnCariLokasi').prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> Mencari...');
                $('#listHasilPencarian').html(
                    '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Mencari lokasi...</div>'
                    );
                $('#hasilPencarianContainer').show();

                // Prioritaskan pencarian di area Humbang Hasundutan
                const viewbox = '98.3,2.0,98.8,2.5'; // Koordinat bounding box Humbahas

                // Panggil Nominatim API
                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/search',
                    method: 'GET',
                    data: {
                        q: query + ', Humbang Hasundutan, Sumatera Utara, Indonesia',
                        format: 'json',
                        limit: 10,
                        viewbox: viewbox,
                        bounded: 0,
                        addressdetails: 1
                    },
                    success: function(data) {
                        $('#btnCariLokasi').prop('disabled', false).html(
                            '<i class="fas fa-search"></i> Cari');

                        if (data && data.length > 0) {
                            tampilkanHasilPencarian(data);
                        } else {
                            // Coba pencarian tanpa spesifik ke Humbahas
                            cariLokasiGlobal(query);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#btnCariLokasi').prop('disabled', false).html(
                            '<i class="fas fa-search"></i> Cari');
                        $('#listHasilPencarian').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                Terjadi kesalahan saat mencari lokasi. Silakan coba lagi.
                            </div>
                        `);
                        console.error('Error:', error);
                    }
                });
            }

            // Fungsi pencarian global jika tidak ditemukan di area Humbahas
            function cariLokasiGlobal(query) {
                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/search',
                    method: 'GET',
                    data: {
                        q: query + ', Sumatera Utara, Indonesia',
                        format: 'json',
                        limit: 10,
                        addressdetails: 1
                    },
                    success: function(data) {
                        if (data && data.length > 0) {
                            tampilkanHasilPencarian(data);
                        } else {
                            $('#listHasilPencarian').html(`
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle"></i>
                                    Lokasi tidak ditemukan. Coba kata kunci lain atau lebih spesifik.
                                    <br><small>Contoh: "Pasar Dolok Sanggul", "Hotel Aek Rangat"</small>
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#listHasilPencarian').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                Terjadi kesalahan. Silakan coba lagi.
                            </div>
                        `);
                        console.error('Error:', error);
                    }
                });
            }

            // Fungsi untuk menampilkan hasil pencarian
            function tampilkanHasilPencarian(results) {
                let html = '<div class="list-group">';

                results.forEach((result, index) => {
                    const displayName = result.display_name;
                    const lat = result.lat;
                    const lon = result.lon;
                    const address = result.address || {};

                    // Format alamat yang lebih ringkas
                    let shortAddress = '';
                    if (address.road) shortAddress += address.road + ', ';
                    if (address.village || address.town || address.city) {
                        shortAddress += (address.village || address.town || address.city);
                    }
                    if (!shortAddress) shortAddress = displayName;

                    html += `
                        <a href="javascript:void(0)"
                           class="list-group-item list-group-item-action hasil-lokasi-item"
                           data-lat="${lat}"
                           data-lon="${lon}"
                           data-name="${displayName}"
                           data-short="${shortAddress}">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    ${shortAddress}
                                </h6>
                            </div>
                            <small class="text-muted">${displayName}</small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-location-arrow"></i>
                                Koordinat: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}
                            </small>
                        </a>
                    `;
                });

                html += '</div>';
                $('#listHasilPencarian').html(html);

                // Event handler untuk memilih lokasi
                $('.hasil-lokasi-item').click(function() {
                    const lat = $(this).data('lat');
                    const lon = $(this).data('lon');
                    const name = $(this).data('name');
                    const shortName = $(this).data('short');

                    pilihLokasiDariPencarian(lat, lon, name, shortName);
                });
            }

            // Fungsi untuk memilih lokasi dari hasil pencarian
            function pilihLokasiDariPencarian(lat, lon, fullName, shortName) {
                // Set koordinat ke hidden input
                $('#latitude').val(lat);
                $('#longitude').val(lon);
                $('#namaLokasiCustom').val(shortName);

                // Tampilkan lokasi terpilih
                $('#namaLokasiTerpilih').text(shortName);
                $('#koordinatLokasiTerpilih').html(`
                    <i class="fas fa-location-arrow"></i>
                    Koordinat: ${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}
                    <br><small>${fullName}</small>
                `);

                // Sembunyikan hasil pencarian, tampilkan lokasi terpilih
                $('#hasilPencarianContainer').hide();
                $('#lokasiTerpilihContainer').show();

                // Berikan feedback
                showAlert('Lokasi berhasil dipilih: ' + shortName, 'success');

                // Update marker di peta jika ada
                if (petaDestinasi) {
                    // Hapus marker lama jika ada
                    if (markerLokasiSaya) {
                        petaDestinasi.removeLayer(markerLokasiSaya);
                    }

                    // Tambahkan marker baru
                    markerLokasiSaya = L.marker([lat, lon], {
                        icon: L.divIcon({
                            className: 'marker-lokasi-search',
                            html: '<div style="background-color: #ff6b6b; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 14px; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"><i class="fas fa-search-location"></i></div>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(petaDestinasi);

                    markerLokasiSaya.bindPopup(`<b>Lokasi Awal (Pencarian)</b><br>${shortName}`).openPopup();

                    // Zoom ke lokasi
                    petaDestinasi.setView([lat, lon], 14);
                }
            }

            // Fungsi untuk reset form pencarian
            function resetFormPencarian() {
                $('#searchLokasi').val('');
                $('#hasilPencarianContainer').hide();
                $('#lokasiTerpilihContainer').hide();
                $('#listHasilPencarian').html('');
                $('#latitude').val('');
                $('#longitude').val('');
                $('#namaLokasiCustom').val('');

                // Hapus marker pencarian jika ada
                if (markerLokasiSaya && petaDestinasi) {
                    petaDestinasi.removeLayer(markerLokasiSaya);
                    markerLokasiSaya = null;
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

        /* Styling untuk form pencarian lokasi */
        .hasil-lokasi-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .hasil-lokasi-item:hover {
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
            transform: translateX(3px);
        }

        .hasil-lokasi-item h6 {
            color: #212529;
            font-size: 14px;
        }

        .hasil-lokasi-item small {
            font-size: 12px;
        }

        #hasilPencarianContainer .card,
        #lokasiTerpilihContainer .alert {
            animation: fadeInDown 0.3s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Marker untuk lokasi pencarian */
        .marker-lokasi-search {
            border: none !important;
            background: transparent !important;
        }

        /* Styling untuk tipe lokasi dropdown */
        #tipeLokasiAwal {
            font-weight: 500;
        }

        #tipeLokasiAwal option {
            padding: 8px;
        }

        /* Loading state untuk button */
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Highlight untuk input focus */
        #searchLokasi:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Card body scroll styling */
        #hasilPencarianContainer .card-body::-webkit-scrollbar {
            width: 6px;
        }

        #hasilPencarianContainer .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #hasilPencarianContainer .card-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #hasilPencarianContainer .card-body::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush
