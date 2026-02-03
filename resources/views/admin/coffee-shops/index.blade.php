@extends('admin.layouts.main')

@section('title', 'Data Coffee Shop')

@section('content')
<div class="page-title">Data Coffee Shop</div>

<div class="table-wrapper">
    <a href="{{ route('coffee.create') }}" class="btn-edit">
        + Tambah Coffee Shop
    </a>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Daerah</th>
                <th>Kecamatan</th>
                <th>Harga</th>
                <th>Rating</th>
                <th>Alamat</th>
                <th>Jam Buka</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->daerah }}</td>
                <td>{{ $item->kecamatan }}</td>
                <td>Rp{{ number_format($item->harga_min) }} - Rp{{ number_format($item->harga_max) }}</td>
                <td>{{ $item->rating }}</td>
                <td>{{ $item->alamat }}</td>
                <td>{{ $item->jam_buka }} - {{ $item->jam_tutup }}</td>
                <td>
                    <a href="{{ route('coffee.edit', $item->id) }}" class="btn-edit">Edit</a>

                    <form action="{{ route('coffee.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $item->id }})">Hapus</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="empty">Belum ada data</td>
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