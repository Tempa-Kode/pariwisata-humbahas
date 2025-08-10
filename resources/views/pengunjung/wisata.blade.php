@extends('template-pengunjung')
@section('title', 'Pariwisata Humbahas')
@section('body')
    <section id="services" class="services section light-background">
        <div class="container mb-4 mt-5" data-aos="fade-up">
            <h2 class="fw-bold">Tempat Wisata</h2>
            <p>Daftar Destinasi Wisata Kabupaten Humbang Hasundutan</p>
            <form action="" method="get">
                <div class="col-auto">
                    <div class="input-group">
                        <div class="input-group-text">🔍</div>
                        <input type="text" class="form-control" id="autoSizingInputGroup" name="wisata" value="{{ request()->wisata }}" placeholder="cari tempat wisata . . .">
                    </div>
                </div>
            </form>
        </div>

        <div class="container">
            <div class="row gy-3">
                @forelse($wisata as $item)
                <div class="col-12" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-item item-cyan position-relative">
                        <img src="{{ asset($item->foto) }}" alt="{{ $item->nama_wisata }}" class="img-fluid icon" style="max-width:80px;max-height:80px;">
                        <div>
                            <h3>{{ $item->nama_wisata }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($item->deskripsi, 300, '...') }}</p>
                            <a href="{{ route('pengunjung.wisata.detail', $item->id_wisata) }}" class="read-more stretched-link">Lihat Detail <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @empty
                    <p class="fst-italic text-muted">Data wisata kosong</p>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-around">
                {{ $wisata->links() }}
            </div>
        </div>

    </section>
@endsection
