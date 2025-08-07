@extends('template-admin')

@section('title', 'Kategori Wisata')

@section('halaman', 'Data Kategori Wisata')

@section('konten')
    <div class="row">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card p-3 shadow-sm">
            <div class="ms-2 mb-3">
                <a href="{{ route('kategori-wisata.tambah') }}" class="btn btn-sm btn-primary px-4 float-end">Tambah Data</a>
            </div>
            <div class="table-responsive">
                <table id="datatables" class="table table-striped">
                    <thead>
                        <tr>
                            <td>No</td>
                            <td>Nama Kategori</td>
                            <td>Aksi</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($kategori as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_kategori }}</td>
                            <td>
                                <a href="{{ route('kategori-wisata.edit', $item->id_kategori) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('kategori-wisata.hapus', $item->id_kategori) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
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
