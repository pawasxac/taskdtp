@extends('admin.layouts.main')

@section('title', 'Tambah Kecamatan')

@section('content')
<div class="page-title">Tambah Kecamatan</div>

<div class="form-wrapper">
    <form action="{{ route('kecamatan.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nama Kecamatan</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
            <a href="{{ route('kecamatan.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

