@extends('admin.layouts.main')

@section('title', 'Edit Kecamatan')

@section('content')
<div class="page-title">Edit Kecamatan</div>

<div class="form-wrapper">
    <form action="{{ route('kecamatan.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nama Kecamatan</label>
            <input type="text" name="name" id="name" value="{{ old('name', $data->name) }}" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Update</button>
            <a href="{{ route('kecamatan.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

