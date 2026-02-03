@extends('admin.layouts.main')

@section('title', 'Dashboard')

@section('content')
<h2 class="page-title">Dashboard Admin</h2>

<div class="grid">

    <div class="stat">
        <h4>Total Coffee Shop</h4>
        <p>{{ \App\Models\CoffeeShop::count() }}</p>
    </div>

    <div class="stat" style="background:#059669">
        <h4>Rata-rata Rating</h4>
        <p>{{ number_format(\App\Models\CoffeeShop::avg('rating'),1) ?? 0 }}</p>
    </div>

    <div class="stat" style="background:#7c3aed">
        <h4>Daerah Terdata</h4>
        <p>{{ \App\Models\CoffeeShop::distinct('daerah')->count('daerah') }}</p>
    </div>

</div>

<div class="card mt-4">
    <div class="card-title">Menu Cepat</div>

    <div class="grid">

        <a href="{{ route('coffee.index') }}" class="card shadow-sm">
            <h3>Kelola Coffee Shop</h3>
            <p>Lihat, tambah, edit, hapus data coffee shop</p>
        </a>

        <a href="{{ route('coffee.create') }}" class="card shadow-sm">
            <h3>Tambah Coffee Shop</h3>
            <p>Masukkan data tempat ngopi baru</p>
        </a>

        <div class="card disabled">
            <h3>Users</h3>
            <p>Segera hadir</p>
        </div>

    </div>
</div>

<div class="card mt-4">
    <div class="card-title">Tentang Sistem</div>
    <p>
        Panel admin ini digunakan untuk mengelola data rekomendasi coffee shop
        wilayah Sidoarjo dan Surabaya, lengkap dengan harga, rating, dan deskripsi tempat.
    </p>
</div>
@endsection
