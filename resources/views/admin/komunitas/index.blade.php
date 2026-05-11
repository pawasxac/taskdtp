@extends('admin.layouts.main')

@section('title', 'Data Komunitas')

@section('content')

<div class="page-title">
    <span>Data Komunitas</span>
    <a href="{{ route('komunitas.create') }}" class="btn-add">Tambah Komunitas</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Komunitas</th>
                <th>Domisili</th>
                <th>Ketua</th>
                <th>Jumlah Anggota</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_komunitas }}</td>
                <td>{{ $item->domisili }}</td>
                <td>{{ $item->ketua }}</td>
                <td>{{ $item->jumlah_anggota }} orang</td>
                <td>
                    <span class="badge {{ $item->status === 'aktif' ? 'badge-admin' : 'badge-user' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('komunitas.show', $item->id) }}" class="btn-view">Lihat</a>
                    <a href="{{ route('komunitas.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('komunitas.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="empty">Belum ada data komunitas</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection

