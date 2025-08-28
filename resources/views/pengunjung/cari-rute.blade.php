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
                            <input type="number" name="latitude" id="latitude" step="any" hidden>
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
                                                <option value="dolok_sanggul" data-lat="2.2915" data-lng="98.9565">Pusat
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
    </section>
@endsection

@push("script")
    <script type="text/javascript">
        $(document).ready(function() {
            let currentLat = null;
            let currentLng = null;

            // Dapatkan lokasi pengguna saat halaman dimuat
            dapatkanLokasiPengguna();

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
        });
    </script>
@endpush
