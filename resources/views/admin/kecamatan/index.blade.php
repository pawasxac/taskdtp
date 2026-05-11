@extends('admin.layouts.main')

@section('title', 'Kecamatan')

@section('content')
<div class="page-title">Kelola Kecamatan</div>

<div class="table-wrapper">
    <a href="{{ route('kecamatan.create') }}" class="btn-edit">
        + Tambah Kecamatan
    </a>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kecamatan</th>
                <th>Total Coffee Shops</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->coffeeShops->count() }}</td>
                <td>
                    <a href="{{ route('kecamatan.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('kecamatan.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $item->id }})">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty">Belum ada data</td>
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

