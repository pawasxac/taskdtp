@extends('admin.layouts.main')

@section('title', 'Edit Komunitas')

@section('content')

<div class="page-title">Edit Komunitas</div>

<div class="form-wrapper">
    <form action="{{ route('komunitas.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nama_komunitas">Nama Komunitas</label>
            <input type="text" name="nama_komunitas" id="nama_komunitas" class="form-control" value="{{ old('nama_komunitas', $data->nama_komunitas) }}" required>
            @error('nama_komunitas')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="domisili">Domisili</label>
            <input type="text" name="domisili" id="domisili" class="form-control" value="{{ old('domisili', $data->domisili) }}" placeholder="Contoh: Jakarta Selatan" required>
            @error('domisili')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="ketua">Nama Ketua</label>
            <input type="text" name="ketua" id="ketua" class="form-control" value="{{ old('ketua', $data->ketua) }}" required>
            @error('ketua')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi Komunitas</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required>{{ old('deskripsi', $data->deskripsi) }}</textarea>
            @error('deskripsi')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tanggal_dibentuk">Tanggal Dibentuk</label>
            <input type="date" name="tanggal_dibentuk" id="tanggal_dibentuk" class="form-control" value="{{ old('tanggal_dibentuk', $data->tanggal_dibentuk) }}" required>
            @error('tanggal_dibentuk')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="jumlah_anggota">Jumlah Anggota</label>
            <input type="number" name="jumlah_anggota" id="jumlah_anggota" class="form-control" value="{{ old('jumlah_anggota', $data->jumlah_anggota) }}" min="0" required>
            @error('jumlah_anggota')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="kontak">Kontak (WhatsApp/Email)</label>
            <input type="text" name="kontak" id="kontak" class="form-control" value="{{ old('kontak', $data->kontak) }}" placeholder="Contoh: 0812-3456-7890">
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="aktif" {{ old('status', $data->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status', $data->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            @error('status')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Simpan</button>
            <a href="{{ route('komunitas.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>

@endsection

