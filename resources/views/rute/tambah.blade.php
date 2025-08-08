@extends('template-admin')

@section('title', 'Tambah Rute Perjalanan')

@section('halaman', 'Tambah Rute Perjalanan')

@section('konten')
    <div class="row">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card p-3 shadow-sm">
            <form action="{{ route('rute.simpan') }}" method="POST">
                @csrf
                @method('POST')
                <div class="mb-3">
                    <label for="lokasi_asal" class="form-label">Lokasi Asal</label>
                    <select class="form-control js-example-basic-single @error('lokasi_asal') is-invalid @enderror" name="lokasi_asal">
                        <option value="" hidden>Pilih Lokasi Asal</option>
                        @forelse($wisata as $item)
                            <option value="{{ $item->id_wisata }}" {{ old('lokasi_asal') == $item->id_wisata ? 'selected' : '' }}>{{ $item->nama_wisata }}</option>
                        @empty
                            <option value="">Tidak Ada Data Wisata</option>
                        @endforelse
                    </select>
                    @error('lokasi_asal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="lokasi_tujuan" class="form-label">Lokasi Tujuan</label>
                    <select class="form-control js-example-basic-single @error('lokasi_tujuan') is-invalid @enderror" name="lokasi_tujuan">
                        <option value="" hidden>Pilih Lokasi Tujuan</option>
                        @forelse($wisata as $item)
                            <option value="{{ $item->id_wisata }}" {{ old('lokasi_tujuan') == $item->id_wisata ? 'selected' : '' }}>{{ $item->nama_wisata }}</option>
                        @empty
                            <option value="">Tidak Ada Data Wisata</option>
                        @endforelse
                    </select>
                    @error('lokasi_tujuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="jarak" class="form-label">Jarak</label>
                    <input type="text" class="form-control @error('jarak') is-invalid @enderror" id="jarak" name="jarak" value="{{ old('jarak') }}" required>
                    @error('jarak')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="waktu_tempuh" class="form-label">Waktu</label>
                    <input type="text" class="form-control @error('waktu_tempuh') is-invalid @enderror" id="waktu_tempuh" name="waktu_tempuh" value="{{ old('waktu_tempuh') }}" required>
                    @error('waktu_tempuh')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('rute.index') }}" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endpush
