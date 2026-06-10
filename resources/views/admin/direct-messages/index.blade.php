@extends('admin.layouts.main')

@section('title', 'Direct Messages')

@section('content')

<div class="page-title">
    <span>Data Direct Messages</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Pengirim</th>
                <th>Penerima</th>
                <th>Pesan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->sender->name ?? 'Anak Skena' }}</td>
                <td>{{ $item->receiver->name ?? 'Anak Skena' }}</td>
                <td>{{ Str::limit($item->message, 50) }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('direct-messages.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus DM ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada direct message.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
