<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/fh-4.0.3/datatables.min.css" rel="stylesheet" integrity="sha384-agp5dJUxwq6B3cZflEbOexGvolzKsopeIwQVoz2SjNog1k29nNJyFLAaRl5CqvZf" crossorigin="anonymous">

    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/fh-4.0.3/datatables.min.js" integrity="sha384-L+8cWDuJxLcD+Scp0vrgdZJQ7HjyU8EetzY2LOs2bvheLfsr+XRnxiigYxbjed6s" crossorigin="anonymous"></script>

    <!-- Main CSS File -->
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
</head>

<body class="starter-page-page">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="/" class="logo d-flex align-items-center me-auto">
            <img src="{{ asset('assets/css/main.css') }}" alt="">
            <h1 class="sitename">Pariwisata Humbang Hasundutan</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ route('dashboard') }}" class="{{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('kategori-wisata.index') }}" class="{{ Route::currentRouteName() == 'kategori-wisata.index' ? 'active' : '' }}">Kategori Wisata</a></li>
                <li><a href="index.html#features">Data Wisata</a></li>
                <li><a href="index.html#services">Data Rute</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="btn-getstarted" href="index.html#about">Logout</a>

    </div>
</header>

<main class="main">

    @if(Route::currentRouteName() != 'dashboard')
        <div class="page-title" data-aos="fade">
            <div class="container d-lg-flex justify-content-start align-items-center">
                <h1 class="mb-2 mb-lg-0">@yield('halaman')</h1>
            </div>
        </div>
    @endif

    <section id="starter-section" class="starter-section section">

        @if(Route::currentRouteName() == 'dashboard')
            <div class="container section-title" data-aos="fade-up">
                <h2 class="text-capitalize">Halo, {{ Auth::user()->username }}</h2>
                <p>Selamat datang di panel pengelolaan wisata Humbang Hasundutan</p>
            </div>
        @endif

        <div class="container" data-aos="fade-up">
            @yield('konten')
        </div>

    </section>

</main>


<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
<script src="{{ asset('assets/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Main JS File -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<script type="text/javascript">
    $(document).ready( function () {
        $('#datatables').DataTable();
    });
</script>

</body>

</html>
