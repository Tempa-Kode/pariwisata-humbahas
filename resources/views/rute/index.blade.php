@extends('template-admin')

@section('title', 'Rute Wisata')

@section('halaman', 'Data Rute Wisata')

@section('konten')
    <div class="row">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card p-3 shadow-sm">
            <div class="ms-2 mb-3">
                <a href="{{ route('rute.tambah') }}" class="btn btn-sm btn-primary px-4 float-end">Tambah Data</a>
            </div>
            <div class="table-responsive">
                <table id="datatables" class="table table-striped">
                    <thead>
                        <tr>
                            <td>No</td>
                            <td>Lokasi Asal</td>
                            <td>Lokasi Tujuan</td>
                            <td>Jarak</td>
                            <td>Waktu</td>
                            <td>Aksi</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($rute as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->lokasiAsal->nama_wisata }}</td>
                            <td>{{ $item->lokasiTujuan->nama_wisata }}</td>
                            <td>{{ $item->jarak }} Km</td>
                            <td>{{ $item->waktu_tempuh }} Jam</td>
                            <td>
                                <a href="{{ route('rute.edit', $item->id_rute) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('rute.hapus', $item->id_rute) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus rute ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
