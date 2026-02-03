@extends('admin.layouts.main')

@section('title', 'Edit Coffee Shop')

@section('content')
<div class="page-title">Edit Coffee Shop</div>

<div class="form-wrapper">
    <form action="{{ route('coffee.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama">Nama Coffee Shop *</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama', $data->nama) }}" required>
        </div>

        <div class="form-group">
            <label for="daerah">Daerah *</label>
            <input type="text" id="daerah" name="daerah" value="{{ old('daerah', $data->daerah) }}" required>
        </div>

        <div class="form-group">
            <label for="kecamatan">Kecamatan *</label>
            <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $data->kecamatan) }}" required>
        </div>

        <div class="form-group">
            <label for="alamat">Alamat *</label>
            <textarea id="alamat" name="alamat" rows="3" required>{{ old('alamat', $data->alamat) }}</textarea>
        </div>

        <!-- ✅ DESKRIPSI -->
        <div class="form-group">
            <label for="deskripsi">Deskripsi *</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi', $data->deskripsi) }}</textarea>
        </div>

        <div class="form-group">
            <label for="jam_buka">Jam Buka *</label>
            <input type="text" id="jam_buka" name="jam_buka"
                   value="{{ old('jam_buka', $data->jam_buka) }}"
                   placeholder="Contoh: 08:00" required>
        </div>

        <!-- ✅ JAM TUTUP -->
        <div class="form-group">
            <label for="jam_tutup">Jam Tutup *</label>
            <input type="text" id="jam_tutup" name="jam_tutup"
                   value="{{ old('jam_tutup', $data->jam_tutup) }}"
                   placeholder="Contoh: 22:00" required>
        </div>

        <div class="form-group">
            <label for="harga_min">Harga Minimal (Rp) *</label>
            <input type="number" id="harga_min" name="harga_min"
                   value="{{ old('harga_min', $data->harga_min) }}"
                   min="0" step="1" required>
        </div>

        <div class="form-group">
            <label for="harga_max">Harga Maksimal (Rp) *</label>
            <input type="number" id="harga_max" name="harga_max"
                   value="{{ old('harga_max', $data->harga_max) }}"
                   min="0" step="1" required>
        </div>

        <div class="form-group">
            <label for="rating">Rating (1-5) *</label>
            <input type="number" id="rating" name="rating"
                   value="{{ old('rating', $data->rating) }}"
                   min="1" max="5" step="0.1" required>
        </div>

        <div class="form-actions">
            <a href="{{ route('coffee.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">Update</button>
        </div>
    </form>
</div>
@endsection
