@extends('admin.layouts.main')

@section('title', 'Gathering Requests')

@section('content')
<div class="page-title">Kelola Gathering Requests</div>

<div class="table-wrapper">
    <a href="{{ route('gathering-requests.create') }}" class="btn-edit">
        + Tambah Gathering Request
    </a>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Komunitas</th>
                <th>Coffee Shop</th>
                <th>Title</th>
                <th>Tanggal Event</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->komunitas->nama_komunitas ?? '-' }}</td>
                <td>{{ $item->coffeeShop->nama ?? '-' }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ \Carbon\Carbon::parse($item->event_date)->format('d/m/Y') }}</td>
                <td>
                    @if($item->status == 'pending')
                        <span class="badge" style="background:#f59e0b;color:#fff;padding:2px 8px;border-radius:4px;">Pending</span>
                    @elseif($item->status == 'approved')
                        <span class="badge" style="background:#10b981;color:#fff;padding:2px 8px;border-radius:4px;">Approved</span>
                    @else
                        <span class="badge" style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:4px;">Rejected</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('gathering-requests.edit', $item->id) }}" class="btn-edit">Edit</a>
                    <form action="{{ route('gathering-requests.destroy', $item->id) }}" method="POST" id="delete-form-{{ $item->id }}" style="display:inline;">
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

