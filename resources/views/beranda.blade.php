@extends('template-admin')

@section('title', 'Beranda')

@section('konten')
    <div id="map" style="height: 500px;"></div>
{{--    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />--}}
{{--    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>--}}
{{--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
@endsection

@push('script')
    <script>
        var map = L.map('map').setView([2.288971175704209, 98.53564577695926], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© Kabupaten Humbang Hasundutan',
        }).addTo(map);

        $.ajax({
            url: '/api/wisata', // Make sure this route returns JSON data
            method: 'GET',
            success: function(data) {
                console.log(data)
                data.forEach(function(wisata) {
                    if(wisata.latitude && wisata.longitude) {
                        L.marker([wisata.latitude, wisata.longitude])
                            .addTo(map)
                            .bindPopup(wisata.nama_wisata);
                    }
                });
            }
        });
    </script>
@endpush
