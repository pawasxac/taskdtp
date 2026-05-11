@extends('admin.layouts.main')

@section('title', 'Dashboard')

@section('content')
<h2 class="page-title">Dashboard Admin</h2>

<div class="grid">

    <div class="stat">
        <h4>Total Coffee Shops</h4>
        <p>{{ \App\Models\CoffeeShop::count() }}</p>
    </div>

    <div class="stat" style="background:#059669">
        <h4>Total Komunitas</h4>
        <p>{{ \App\Models\Komunitas::count() }}</p>
    </div>

    <div class="stat" style="background:#7c3aed">
        <h4>Total Users</h4>
        <p>{{ \App\Models\User::count() }}</p>
    </div>

    <div class="stat" style="background:#dc2626">
        <h4>Total Reviews</h4>
        <p>{{ \App\Models\CoffeeShopReview::count() }}</p>
    </div>

    <div class="stat" style="background:#f59e0b">
        <h4>Total Gathering Requests</h4>
        <p>{{ \App\Models\GatheringRequest::count() }}</p>
    </div>

    <div class="stat" style="background:#3b82f6">
        <h4>Rata-rata Rating</h4>
        <p>{{ number_format(\App\Models\CoffeeShop::avg('rating'),1) ?? 0 }}</p>
    </div>

</div>

<div class="card mt-4">
    <div class="card-title">Menu Cepat</div>

    <div class="grid">

        <a href="{{ route('coffee.index') }}" class="card shadow-sm">
            <h3>Kelola Coffee Shop</h3>
            <p>Lihat, tambah, edit, hapus data coffee shop</p>
        </a>

        <a href="{{ route('kecamatan.index') }}" class="card shadow-sm">
            <h3>Kelola Kecamatan</h3>
            <p>Kelola data kecamatan di wilayah Sidoarjo</p>
        </a>

        <a href="{{ route('gathering-requests.index') }}" class="card shadow-sm">
            <h3>Gathering Requests</h3>
            <p>Kelola pengajuan gathering komunitas</p>
        </a>

        <a href="{{ route('community-posts.index') }}" class="card shadow-sm">
            <h3>Community Posts</h3>
            <p>Kelola postingan komunitas</p>
        </a>

    </div>
</div>

<div class="card mt-4">
    <div class="card-title">Tentang Sistem</div>
    <p>
        Panel admin ini digunakan untuk mengelola data rekomendasi coffee shop
        wilayah Sidoarjo dan Surabaya, lengkap dengan harga, rating, dan deskripsi tempat.
        Juga mengelola komunitas, gathering requests, dan postingan komunitas.
    </p>
</div>
@endsection
