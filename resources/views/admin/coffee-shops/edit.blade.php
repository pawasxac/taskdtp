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
            <select id="kecamatan" name="kecamatan" required>
                <option value="">Pilih Kecamatan</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->name }}" {{ old('kecamatan', $data->kecamatan) == $kecamatan->name ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="kecamatan_id">Kecamatan (Link)</label>
            <select id="kecamatan_id" name="kecamatan_id">
                <option value="">Pilih Kecamatan (Opsional)</option>
                @foreach($kecamatans as $kecamatan)
                    <option value="{{ $kecamatan->id }}" {{ old('kecamatan_id', $data->kecamatan_id) == $kecamatan->id ? 'selected' : '' }}>{{ $kecamatan->name }}</option>
                @endforeach
            </select>
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

        <div class="form-group">
            <label for="image_url">Gambar Coffee Shop (URL)</label>
            <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $data->image_url) }}" placeholder="https://example.com/coffee-shop.jpg">
            <span class="help-text">Paste URL gambar dari sumber eksternal seperti Unsplash, Pixabay, atau hosting gambar lainnya</span>
            <div id="image-preview-container" style="margin-top: 12px; display: none;">
                <img id="image-preview" src="" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #ccc;">
            </div>
        </div>

        <script>
            const imageUrlInput = document.getElementById('image_url');
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');

            imageUrlInput.addEventListener('input', function(e) {
                const url = e.target.value.trim();

                if (url === '') {
                    previewContainer.style.display = 'none';
                    return;
                }


                try {
                    new URL(url);
                    previewImage.src = url;
                    previewImage.onload = function() {
                        previewContainer.style.display = 'block';
                    };
                    previewImage.onerror = function() {
                        previewContainer.style.display = 'none';
                    };
                } catch (e) {
                    previewContainer.style.display = 'none';
                }
            });


            if (imageUrlInput.value) {
                imageUrlInput.dispatchEvent(new Event('input'));
            }
</script>

        <div class="form-actions">
            <a href="{{ route('coffee.index') }}" class="btn-cancel">Batal</a>
            <button type="submit" class="btn-save">Update</button>
        </div>
    </form>
</div>
@endsection
