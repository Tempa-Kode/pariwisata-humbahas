@extends('template-admin')

@section('title', 'Wisata')

@section('halaman', 'Data Wisata')

@section('konten')
    <div class="row">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card p-3 shadow-sm">
            <div class="ms-2 mb-3">
                <a href="{{ route('wisata.tambah') }}" class="btn btn-sm btn-primary px-4 float-end">Tambah Data</a>
            </div>
            <div class="table-responsive">
                <table id="datatables" class="table table-striped">
                    <thead>
                        <tr>
                            <td>No</td>
                            <td>Nama Wisata</td>
                            <td>Alamat</td>
                            <td>Kategori</td>
                            <td>Aksi</td>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($wisata as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_wisata }}</td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                @foreach ($item->kategori ?? [] as $kategori)
                                    {{ $kategori->nama_kategori }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('wisata.edit', $item->id_wisata) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('wisata.hapus', $item->id_wisata) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus wisata ini?')">Hapus</button>
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
