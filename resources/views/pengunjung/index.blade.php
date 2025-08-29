@extends('template-pengunjung')
@section('title', 'Pariwisata Humbahas')
@section('body')
    <section id="hero" class="hero section">
        <div class="hero-bg mb-5">
            <img src="https://grapadinews.co.id/wp-content/uploads/2019/11/Welcome_gate_to_Humbang_Hasundutan_Sumatra_Utara_v._Sbb.jpg" alt="">
        </div>
        <div class="container text-center">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <h1 data-aos="fade-up">Jelajahi Wisata</h1>
                <h1 data-aos="fade-up"><span>Humbang Hasundutan</span></h1>
                <p data-aos="fade-up" data-aos-delay="100">Temukan Destinasi Menarik dan Rute Tercepat untuk mencapainya<br></p>
                <div class="d-flex gap-5" data-aos="fade-up" data-aos-delay="200">
                    <a href="{{ route('pengunjung.wisata') }}" class="btn-get-started">Lihat Daftar Wisata</a>
                    <a href="{{ route('pengunjung.cari-rute') }}" class="btn-get-started">Cari Rute Terdekat</a>
                </div>
            </div>
        </div>

    </section>

    <section id="features" class="features section">

        <div class="container section-title" data-aos="fade-up">
            <h2>Destinasi Unggulan</h2>
        </div>

        <div class="container">
            <div class="row row-cols-1 row-cols-md-4 g-4">
                @forelse($wisataUnggulan as $item)
                <div class="col" data-aos="fade-left" data-aos-delay="100">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ asset($item->foto) }}" class="card-img-top" alt="..." style="width:100%;height:200px;object-fit:cover;">
                        <div class="card-body">
                            <a href="{{ route('pengunjung.wisata.detail', $item->id_wisata) }}">
                                <h5 class="card-title fw-bold">{{ $item->nama_wisata }}</h5>
                            </a>
                            <p class="card-text text-opacity-50">{{ $item->lokasi }}</p>
                        </div>
                    </div>
                </div>
                @empty
                    <p class="fst-italic text-center">destinasi unggulan belum di set</p>
                @endforelse
            </div>
        </div>

    </section>
@endsection
