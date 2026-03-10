@extends('admin.layouts.main')

@section('title', 'Coffee Shop Reviews')

@section('content')
<div class="page-title">
    <span>Kelola Reviews</span>
    <a href="{{ route('coffee-shop-reviews.create') }}" class="btn-add">+ Tambah Review</a>
</div>

<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Coffee Shop</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ $item->coffeeShop->nama ?? '-' }}</td>
                <td>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $item->rating)
                            ★
                        @else
                            ☆
                        @endif
                    @endfor
                    ({{ $item->rating }})
                </td>
                <td>{{ Str::limit($item->review, 30) ?? '-' }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('coffee-shop-reviews.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('coffee-shop-reviews.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
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

