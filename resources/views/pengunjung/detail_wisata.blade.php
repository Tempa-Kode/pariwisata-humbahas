@extends('template-pengunjung')
@section('title', 'Pariwisata Humbahas')
@section('body')
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <div class="row">
                <div class="card p-3 shadow-sm">
                    <div class="row">
                        <div class="my-3">
                            <a href="{{ route('pengunjung.wisata') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6">
                            @if($wisata->foto)
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
                                    @foreach($wisata->kategori as $kat)
                                        <span class="badge bg-primary">{{ $kat->nama_kategori }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Harga Tiket:</label>
                                <div>{{ $wisata->harga_tiket ?? '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jam Operasional:</label>
                                <div>{{ $wisata->jam_operasional ?? '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Biaya Parkir:</label>
                                <div>{{ $wisata->biaya_parkir ?? '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fasilitas:</label>
                                <div>{{ $wisata->fasilitas ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi:</label>
                                <div>{{ $wisata->deskripsi }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Peraturan:</label>
                                <div>{{ $wisata->peraturan ?? '-' }}</div>
                            </div>
                            <a href="{{ route('pengunjung.cari-rute', ['tujuan' => $wisata->id_wisata]) }}" class="btn btn-success">Kunjungi Destinasi</a>
                            <div class="row mt-3 px-3 align-items-center">
                                <div id="map" style="height: 350px"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script type="text/javascript">
        let map;
        let marker;
        document.addEventListener('DOMContentLoaded', function() {
            initMap({{ $wisata->latitude }}, {{ $wisata->longitude }}, "{{ $wisata->nama_wisata }}");
        });
        function initMap(latitude, longitude, namaDestinasi) {
            map = L.map('map').setView([latitude, longitude], 12);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);
            marker = L.marker([latitude, longitude]).addTo(map);
            marker.bindPopup(namaDestinasi).openPopup();
        }
    </script>
@endpush

