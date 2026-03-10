@extends('admin.layouts.main')

@section('title', 'Community Members')

@section('content')
<div class="page-title">Kelola Anggota Komunitas</div>

<div class="table-wrapper">
    <a href="{{ route('community-members.create') }}" class="btn-edit">
        + Tambah Anggota
    </a>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Komunitas</th>
                <th>User</th>
                <th>Role</th>
                <th>Tanggal Join</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->komunitas->nama_komunitas ?? '-' }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>
                    @if($item->role == 'leader')
                        <span class="badge" style="background:#f59e0b;color:#fff;padding:2px 8px;border-radius:4px;">Leader</span>
                    @else
                        <span class="badge" style="background:#3b82f6;color:#fff;padding:2px 8px;border-radius:4px;">Member</span>
                    @endif
                </td>
                <td>{{ $item->joined_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('community-members.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('community-members.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $item->id }})">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty">Belum ada data</td>
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
