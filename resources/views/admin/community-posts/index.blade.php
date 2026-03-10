@extends('admin.layouts.main')

@section('title', 'Community Posts')

@section('content')
<div class="page-title">Kelola Postingan Komunitas</div>

<div class="table-wrapper">
    <a href="{{ route('community-posts.create') }}" class="btn-edit">
        + Tambah Postingan
    </a>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Komunitas</th>
                <th>User</th>
                <th>Content</th>
                <th>Komentar</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->komunitas->nama_komunitas ?? '-' }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ Str::limit($item->content, 50) }}</td>
                <td>{{ $item->comments->count() }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('community-posts.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('community-posts.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $item->id }})">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="empty">Belum ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection

