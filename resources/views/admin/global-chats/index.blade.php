@extends('admin.layouts.main')

@section('title', 'Global Chats')

@section('content')

<div class="page-title">
    <span>Data Global Chats</span>
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
                <th>Pesan</th>
                <th>Balasan Ke</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->user->name ?? 'Anak Skena' }}</td>
                <td>{{ Str::limit($item->message, 50) }}</td>
                <td>
                    @if($item->replyTo)
                        <span class="badge badge-admin">Membalas pesan ID: {{ $item->replyTo->id }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('global-chats.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus chat ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="empty">Belum ada obrolan global.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
