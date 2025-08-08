@extends('template-admin')

@section('title', 'Edit Wisata')

@section('halaman', 'Edit Data Wisata')

@section('konten')
    <div class="row">
        <form action="{{ route('wisata.update', $wisata->id_wisata) }}" method="POST" enctype="multipart/form-data">
            <div class="card p-3 shadow-sm">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-7">
                        <!-- Form fields start -->
                        <div class="row mb-3">
                            <label for="nama_wisata" class="col-sm-3 col-form-label">Nama Wisata<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('nama_wisata') is-invalid @enderror" id="nama_wisata" name="nama_wisata" value="{{ old('nama_wisata', $wisata->nama_wisata) }}">
                                @error('nama_wisata')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="lokasi" class="col-sm-3 col-form-label">Alamat<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi', $wisata->lokasi) }}">
                                @error('lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="id_kategori" class="col-sm-3 col-form-label">Kategori<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select js-example-basic-multiple @error('id_kategori') is-invalid @enderror" name="id_kategori[]" multiple="multiple">
                                    <option hidden value="">pilih</option>
                                    @forelse($kategori as $item)
                                        <option value="{{ $item->id_kategori }}" {{ (collect(old('id_kategori', $wisata->kategori->pluck('id_kategori')->toArray()))->contains($item->id_kategori)) ? 'selected' : '' }}>
                                            {{ $item->nama_kategori }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Tidak ada kategori tersedia</option>
                                    @endforelse
                                </select>
                                @error('id_kategori')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="deskripsi" class="col-sm-3 col-form-label">Deskripsi<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $wisata->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="foto" class="col-sm-3 col-form-label">Foto<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                @if($wisata->foto)
                                    <img src="{{ asset($wisata->foto) }}" alt="Foto Wisata" width="150" class="mb-2"><br>
                                @endif
                                <input class="form-control @error('foto') is-invalid @enderror" type="file" id="foto" name="foto" accept="image/*">
                                @error('foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="harga_tiket" class="col-sm-3 col-form-label">Harga Tiket</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('harga_tiket') is-invalid @enderror" id="harga_tiket" name="harga_tiket" value="{{ old('harga_tiket', $wisata->harga_tiket) }}">
                                @error('harga_tiket')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="biaya_parkir" class="col-sm-3 col-form-label">Biaya Parkir</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('biaya_parkir') is-invalid @enderror" id="biaya_parkir" name="biaya_parkir" value="{{ old('biaya_parkir', $wisata->biaya_parkir) }}">
                                @error('biaya_parkir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="jam_operasional" class="col-sm-3 col-form-label">Jam Operasional</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('jam_operasional') is-invalid @enderror" id="jam_operasional" name="jam_operasional" value="{{ old('jam_operasional', $wisata->jam_operasional) }}">
                                @error('jam_operasional')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="fasilitas" class="col-sm-3 col-form-label">Fasilitas</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('fasilitas') is-invalid @enderror" id="fasilitas" name="fasilitas" rows="3">{{ old('fasilitas', $wisata->fasilitas) }}</textarea>
                                @error('fasilitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="peraturan" class="col-sm-3 col-form-label">Peraturan</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('peraturan') is-invalid @enderror" id="peraturan" name="peraturan" rows="3">{{ old('peraturan', $wisata->peraturan) }}</textarea>
                                @error('peraturan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <!-- Form fields end -->
                    </div>
                    <div class="col-lg-5">
                        <h4 class="text-center">Lokasi Tempat Wisata</h4>
                        <div class="form-floating mb-3">
                            <input type="number" class="form-control @error('latitude') is-invalid @enderror" id="latitude" placeholder="masukan latitude" name="latitude" value="{{ old('latitude', $wisata->latitude) }}" step="any" oninput="updateMap()">
                            <label for="latitude">Latitude</label>
                            @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-floating">
                            <input type="number" class="form-control @error('longitude') is-invalid @enderror" id="longitude" placeholder="masukan longitude" name="longitude" value="{{ old('longitude', $wisata->longitude) }}" step="any" oninput="updateMap()">
                            <label for="longitude">Longitude</label>
                            @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row mt-3 px-3 align-items-center">
                            <div id="map" style="height: 350px"></div>
                        </div>
                    </div>
                </div>
                <div class="mx-auto mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('wisata.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        let map;
        let marker;
        initMap({{ old('latitude', $wisata->latitude) }}, {{ old('longitude', $wisata->longitude) }}, "{{ old('nama_wisata', $wisata->nama_wisata) }}");

        function initMap(latitude, longitude, namaDestinasi) {
            map = L.map('map').setView([latitude, longitude], 12);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">Humbahas</a>'
            }).addTo(map);

            marker = L.marker([latitude, longitude]).addTo(map);
            marker.bindPopup(namaDestinasi).openPopup();
        }

        function updateMap() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lon = parseFloat(document.getElementById('longitude').value);
            const namaDestinasi = document.getElementById('nama_wisata').value;

            if (!isNaN(lat) && !isNaN(lon)) {
                map.setView([lat, lon], 12);
                marker.setLatLng([lat, lon]);
                marker.bindPopup(namaDestinasi).openPopup();
            }
        }
    </script>
@endpush
