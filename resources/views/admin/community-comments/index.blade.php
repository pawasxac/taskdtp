@extends('admin.layouts.main')

@section('title', 'Community Comments')

@section('content')
<h2 class="page-title">Kelola Komentar Komunitas</h2>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Post</th>
                <th>User</th>
                <th>Komentar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ Str::limit($item->post->content, 30) ?? '-' }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ Str::limit($item->comment, 50) }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <form action="{{ route('community-comments.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada data komentar</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

