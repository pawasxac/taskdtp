@extends('admin.layouts.main')

@section('title', 'Detail Komunitas')

@section('content')

<div class="page-title">
    <span>Detail Komunitas</span>
    <a href="{{ route('komunitas.index') }}" class="btn-cancel">Kembali</a>
</div>

<div class="card">
    <div class="detail-grid">
        <div class="detail-item">
            <label>Nama Komunitas</label>
            <p>{{ $data->nama_komunitas }}</p>
        </div>
        
        <div class="detail-item">
            <label>Domisili</label>
            <p>{{ $data->domisili }}</p>
        </div>
        
        <div class="detail-item">
            <label>Ketua</label>
            <p>{{ $data->ketua }}</p>
        </div>
        
        <div class="detail-item">
            <label>Tanggal Dibentuk</label>
            <p>{{ \Carbon\Carbon::parse($data->tanggal_dibentuk)->format('d M Y') }}</p>
        </div>
        
        <div class="detail-item">
            <label>Jumlah Anggota</label>
            <p>{{ $data->jumlah_anggota }} orang</p>
        </div>
        
        <div class="detail-item">
            <label>Kontak</label>
            <p>{{ $data->kontak ?? '-' }}</p>
        </div>
        
        <div class="detail-item">
            <label>Status</label>
            <span class="badge {{ $data->status === 'aktif' ? 'badge-admin' : 'badge-user' }}">
                {{ ucfirst($data->status) }}
            </span>
        </div>
        
        <div class="detail-item full-width">
            <label>Deskripsi</label>
            <p>{{ $data->deskripsi }}</p>
        </div>
    </div>
    
    <div class="form-actions" style="margin-top: 20px;">
        <a href="{{ route('komunitas.edit', $data->id) }}" class="btn-edit">Edit</a>
    </div>
</div>

@endsection

