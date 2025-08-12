@extends('template-pengunjung')
@section('title', 'Pariwisata Humbahas')
@section('body')
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <form action="" method="post">
                @csrf
                @method('POST')
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
                                        <input type="email" class="form-control" id="lokasiAwal">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="lokasi_tujuan" class="col-sm-3 col-form-label">Lokasi Tujuan</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" name="lokasi_tujuan" id="lokasi_tujuan" data-placeholder="Pilih lokasi tujuan">
                                            <option value="" hidden="">Pilih Lokasi Tujuan</option>
                                            @forelse($wisata as $item)
                                                <option value="{{ $item->id }}" data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}">
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

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            dapatkanLokasiPengguna();

            function dapatkanLokasiPengguna() {
                if (navigator.geolocation) {
                    // Menampilkan indikator loading
                    $('#lokasiAwal').val('Mendapatkan lokasi...');

                    navigator.geolocation.getCurrentPosition(
                        // Callback sukses
                        function(posisi) {
                            const lintang = posisi.coords.latitude;
                            const bujur = posisi.coords.longitude;

                            // Mengatur input tersembunyi
                            $('#latitude').val(lintang);
                            $('#longitude').val(bujur);

                            dapatkanNamaLokasi(lintang, bujur);
                        },
                        // Callback kesalahan
                        function(kesalahan) {
                            tanganiErrorLokasi(kesalahan);
                        },
                        // Opsi - akurasi tinggi, tidak ada cache, batas waktu 5 detik
                        {
                            enableHighAccuracy: true,
                            maximumAge: 0,
                            timeout: 5000
                        }
                    );
                } else {
                    $('#lokasiAwal').val('Geolocation tidak didukung oleh browser ini');
                }
            }

            // Menangani kesalahan lokasi
            function tanganiErrorLokasi(kesalahan) {
                switch(kesalahan.code) {
                    case kesalahan.PERMISSION_DENIED:
                        $('#lokasiAwal').val('Akses lokasi ditolak. Mohon izinkan akses lokasi.');
                        // Menambahkan tombol untuk meminta izin kembali
                        $('<button type="button" class="btn btn-sm btn-primary mt-2">Izinkan Akses Lokasi</button>')
                            .insertAfter('#lokasiAwal')
                            .click(function() {
                                $(this).remove();
                                dapatkanLokasiPengguna();
                            });
                        break;
                    case kesalahan.POSITION_UNAVAILABLE:
                        $('#lokasiAwal').val('Informasi lokasi tidak tersedia');
                        break;
                    case kesalahan.TIMEOUT:
                        $('#lokasiAwal').val('Waktu permintaan lokasi habis');
                        break;
                    case kesalahan.UNKNOWN_ERROR:
                        $('#lokasiAwal').val('Terjadi kesalahan saat mendapatkan lokasi');
                        break;
                }
            }

            // Mendapatkan nama lokasi menggunakan reverse geocoding
            function dapatkanNamaLokasi(lintang, bujur) {
                $.ajax({
                    url: `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lintang}&lon=${bujur}&zoom=18&addressdetails=1`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.display_name) {
                            $('#lokasiAwal').val(data.display_name);
                        } else {
                            $('#lokasiAwal').val(`Lokasi saat ini (${lintang}, ${bujur})`);
                        }
                    },
                    error: function() {
                        $('#lokasiAwal').val(`Lokasi saat ini (${lintang}, ${bujur})`);
                    }
                });
            }
        });
    </script>
@endpush
