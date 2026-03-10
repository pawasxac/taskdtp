@extends('admin.layouts.main')

@section('title', 'Tambah Review')

@section('content')
<div class="page-title">Tambah Review</div>

<div class="form-wrapper">
    <form action="{{ route('coffee-shop-reviews.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" required>
                <option value="">Pilih User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="coffee_shop_id">Coffee Shop</label>
            <select name="coffee_shop_id" id="coffee_shop_id" required>
                <option value="">Pilih Coffee Shop</option>
                @foreach($coffeeShops as $shop)
                    <option value="{{ $shop->id }}">{{ $shop->nama }}</option>
                @endforeach
            </select>
            @error('coffee_shop_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="rating">Rating</label>
            <select name="rating" id="rating" required>
                <option value="">Pilih Rating</option>
                <option value="1">1 - Buruk</option>
                <option value="2">2 - Kurang</option>
                <option value="3">3 - Cukup</option>
                <option value="4">4 - Baik</option>
                <option value="5">5 - Sangat Baik</option>
            </select>
            @error('rating')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="review">Review</label>
            <textarea name="review" id="review" rows="4" placeholder="Masukkan review..."></textarea>
            @error('review')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
            <a href="{{ route('coffee-shop-reviews.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

