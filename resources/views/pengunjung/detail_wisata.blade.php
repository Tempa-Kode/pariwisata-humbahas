@extends("template-pengunjung")
@section("title", "Pariwisata Humbahas")
@section("body")
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <div class="row">
                <div class="card p-3 shadow-sm">
                    <div class="row">
                        <div class="my-3">
                            <a href="{{ route("pengunjung.wisata") }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6">
                            @if ($wisata->foto)
                                <img src="{{ asset($wisata->foto) }}" alt="Foto Wisata" class="img-fluid rounded">
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Wisata:</label>
                                <div>{{ $wisata->nama_wisata }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat:</label>
                                <div>{{ $wisata->lokasi }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kategori:</label>
                                <div>
                                    @foreach ($wisata->kategori as $kat)
                                        <span class="badge bg-primary">{{ $kat->nama_kategori }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Harga Tiket:</label>
                                <div>{{ $wisata->harga_tiket ?? "-" }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jam Operasional:</label>
                                <div>{{ $wisata->jam_operasional ?? "-" }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Biaya Parkir:</label>
                                <div>{{ $wisata->biaya_parkir ?? "-" }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fasilitas:</label>
                                <div>{{ $wisata->fasilitas ?? "-" }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Transportasi:</label>
                                <div>{{ $wisata->transportasi ?? "-" }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi:</label>
                                <div>{!! $wisata->deskripsi !!}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Peraturan:</label>
                                <div>{{ $wisata->peraturan ?? "-" }}</div>
                            </div>
                            <button type="button" class="btn btn-success" id="btnKunjungiDestinasi"
                                data-wisata-id="{{ $wisata->id_wisata }}">
                                <i class="bi bi-geo-alt-fill"></i> Kunjungi Destinasi
                            </button>
                            <span class="text-muted ms-2" id="statusLokasi" style="display: none;">
                                <i class="bi bi-hourglass-split"></i> Mendapatkan lokasi...
                            </span>
                            <div class="row mt-3 px-3 align-items-center">
                                <div id="map" style="height: 350px"></div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Galeri Foto:</label>
                                <div class="row g-2">
                                    @foreach ($wisata->fotoWisata as $foto)
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#modalFotoWisata{{ $foto->id_foto_wisata }}">
                                                <img src="{{ asset($foto->url_foto) }}" alt="Foto Wisata"
                                                    class="img-thumbnail"
                                                    style="height: 100px; object-fit: cover; width: 100%;">
                                            </a>
                                        </div>
                                        <!-- Modal Foto Besar -->
                                        <div class="modal fade" id="modalFotoWisata{{ $foto->id_foto_wisata }}"
                                            tabindex="-1" aria-labelledby="modalLabel{{ $foto->id_foto_wisata }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalLabel{{ $foto->id_foto_wisata }}">
                                                            Foto Wisata</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset($foto->url_foto) }}" alt="Foto Wisata"
                                                            class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
        let map;
        let marker;

        // Global flag untuk mencegah multiple execution
        let isProcessingLocation = false;
        let locationWatchId = null;

        document.addEventListener('DOMContentLoaded', function() {
            initMap({{ $wisata->latitude }}, {{ $wisata->longitude }}, "{{ $wisata->nama_wisata }}");

            // Event handler untuk tombol Kunjungi Destinasi
            const btnKunjungi = document.getElementById('btnKunjungiDestinasi');
            if (btnKunjungi) {
                btnKunjungi.addEventListener('click', function() {
                    const wisataId = this.getAttribute('data-wisata-id');
                    cariRuteDariLokasiSekarang(wisataId);
                });
            }
        });

        function initMap(latitude, longitude, namaDestinasi) {
            map = L.map('map').setView([latitude, longitude], 12);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);
            marker = L.marker([latitude, longitude]).addTo(map);
            marker.bindPopup(namaDestinasi).openPopup();
        }

        function cariRuteDariLokasiSekarang(wisataId) {
            const statusLokasi = document.getElementById('statusLokasi');
            const btnKunjungi = document.getElementById('btnKunjungiDestinasi');

            // Cek apakah sudah ada proses yang berjalan
            if (isProcessingLocation) {
                console.log('Masih ada proses yang berjalan, mengabaikan klik...');
                return;
            }

            // Set flag menjadi true
            isProcessingLocation = true;

            // Tampilkan status loading
            if (statusLokasi) {
                statusLokasi.style.display = 'inline';
            }
            if (btnKunjungi) {
                btnKunjungi.disabled = true;
                btnKunjungi.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
            }

            // Cek apakah browser mendukung geolocation
            if (!navigator.geolocation) {
                alert(
                    'Browser Anda tidak mendukung geolocation. Anda akan diarahkan ke halaman cari rute untuk memilih lokasi manual.'
                );

                // Reset tombol dan flag sebelum redirect
                isProcessingLocation = false;
                if (statusLokasi) statusLokasi.style.display = 'none';
                if (btnKunjungi) {
                    btnKunjungi.disabled = false;
                    btnKunjungi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Kunjungi Destinasi';
                }

                window.location.href = "{{ route("pengunjung.cari-rute") }}?tujuan=" + wisataId;
                return;
            }

            // Cek apakah sedang menggunakan HTTPS atau localhost
            const isSecureContext = window.isSecureContext || location.protocol === 'https:' || location.hostname ===
                'localhost' || location.hostname === '127.0.0.1';

            if (!isSecureContext) {
                alert(
                    'Akses lokasi memerlukan koneksi HTTPS yang aman. Anda akan diarahkan ke halaman cari rute untuk memilih lokasi manual.'
                );

                // Reset tombol dan flag sebelum redirect
                isProcessingLocation = false;
                if (statusLokasi) statusLokasi.style.display = 'none';
                if (btnKunjungi) {
                    btnKunjungi.disabled = false;
                    btnKunjungi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Kunjungi Destinasi';
                }

                window.location.href = "{{ route("pengunjung.cari-rute") }}?tujuan=" + wisataId;
                return;
            }

            console.log('Meminta akses lokasi GPS...');

            // Ambil lokasi GPS saat ini
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Cek apakah masih dalam proses yang valid (belum di-cancel)
                    if (!isProcessingLocation) {
                        console.log('Proses sudah dibatalkan, mengabaikan success callback...');
                        return;
                    }

                    console.log('Lokasi berhasil didapat:', position.coords);
                    // Sukses mendapatkan lokasi
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Buat form dan submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route("pengunjung.proses-rute") }}";

                    // Tambahkan CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    // Tambahkan latitude
                    const latInput = document.createElement('input');
                    latInput.type = 'hidden';
                    latInput.name = 'latitude';
                    latInput.value = latitude;
                    form.appendChild(latInput);

                    // Tambahkan longitude
                    const lngInput = document.createElement('input');
                    lngInput.type = 'hidden';
                    lngInput.name = 'longitude';
                    lngInput.value = longitude;
                    form.appendChild(lngInput);

                    // Tambahkan lokasi awal (current)
                    const lokasiAwalInput = document.createElement('input');
                    lokasiAwalInput.type = 'hidden';
                    lokasiAwalInput.name = 'lokasi_awal';
                    lokasiAwalInput.value = 'current';
                    form.appendChild(lokasiAwalInput);

                    // Tambahkan lokasi tujuan
                    const lokasiTujuanInput = document.createElement('input');
                    lokasiTujuanInput.type = 'hidden';
                    lokasiTujuanInput.name = 'lokasi_tujuan';
                    lokasiTujuanInput.value = wisataId;
                    form.appendChild(lokasiTujuanInput);

                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                },
                function(error) {
                    // Cek apakah masih dalam proses yang valid
                    if (!isProcessingLocation) {
                        console.log('Proses sudah selesai/dibatalkan, mengabaikan error callback...');
                        return;
                    }

                    // Log error untuk debugging
                    console.error('Geolocation error:', error);
                    console.error('Error code:', error.code);
                    console.error('Error message:', error.message);

                    // Reset tombol DULU sebelum menampilkan alert
                    if (statusLokasi) {
                        statusLokasi.style.display = 'none';
                    }
                    if (btnKunjungi) {
                        btnKunjungi.disabled = false;
                        btnKunjungi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Kunjungi Destinasi';
                    }

                    // Gagal mendapatkan lokasi
                    let errorMessage = 'Tidak dapat mengakses lokasi Anda.\n\n';
                    let errorDetail = '';

                    switch (error.code) {
                        case 1: // PERMISSION_DENIED
                            errorMessage += '❌ Izin akses lokasi ditolak.\n\n';
                            errorDetail = 'Untuk menggunakan fitur ini:\n' +
                                '1. Klik ikon gembok/info di address bar\n' +
                                '2. Izinkan akses lokasi\n' +
                                '3. Refresh halaman dan coba lagi\n\n' +
                                'Atau pilih "OK" di dialog berikutnya untuk memilih lokasi secara manual.';
                            break;
                        case 2: // POSITION_UNAVAILABLE
                            errorMessage += '📍 Informasi lokasi tidak tersedia.\n\n';
                            errorDetail = 'Pastikan:\n' +
                                '- GPS perangkat sudah aktif\n' +
                                '- Anda berada di area dengan sinyal GPS yang baik\n' +
                                '- Browser memiliki izin akses lokasi\n\n' +
                                'Atau pilih "OK" di dialog berikutnya untuk memilih lokasi secara manual.';
                            break;
                        case 3: // TIMEOUT
                            errorMessage += '⏱️ Waktu permintaan lokasi habis.\n\n';
                            errorDetail = 'Proses mendapatkan lokasi memakan waktu terlalu lama.\n' +
                                'Silakan coba lagi atau pilih "OK" di dialog berikutnya untuk memilih lokasi secara manual.';
                            break;
                        default:
                            errorMessage += '⚠️ Terjadi kesalahan: ' + error.message + '\n\n';
                            errorDetail = 'Kemungkinan penyebab:\n' +
                                '- Koneksi tidak aman (perlu HTTPS)\n' +
                                '- Browser tidak mendukung geolocation\n' +
                                '- GPS perangkat bermasalah\n\n' +
                                'Pilih "OK" di dialog berikutnya untuk memilih lokasi secara manual.';
                    }

                    // Tampilkan pesan error
                    alert(errorMessage + errorDetail);

                    // Tanya user apakah ingin ke halaman cari rute manual
                    const userConfirm = confirm(
                        'Apakah Anda ingin membuka halaman cari rute untuk memilih lokasi secara manual?');

                    console.log('User confirm result:', userConfirm);

                    if (userConfirm) {
                        console.log('Redirecting to cari-rute page...');
                        // Reset flag sebelum redirect
                        isProcessingLocation = false;
                        window.location.href = "{{ route("pengunjung.cari-rute") }}?tujuan=" + wisataId;
                    } else {
                        console.log('User cancelled, staying on current page');
                        // User memilih Cancel - RESET FLAG dan batalkan proses
                        isProcessingLocation = false;
                        console.log('Flag isProcessingLocation direset menjadi false');
                        // Tombol sudah direset di atas
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 15000, // Perpanjang timeout jadi 15 detik
                    maximumAge: 0
                }
            );
        }
    </script>
@endpush
