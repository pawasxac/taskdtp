@extends('admin.layouts.main')

@section('title', 'Edit Review')

@section('content')
<div class="page-title">Edit Review</div>

<div class="form-wrapper">
    <form action="{{ route('coffee-shop-reviews.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label>User</label>
            <input type="text" value="{{ $data->user->name ?? '-' }}" disabled>
        </div>
        
        <div class="form-group">
            <label>Coffee Shop</label>
            <input type="text" value="{{ $data->coffeeShop->nama ?? '-' }}" disabled>
        </div>
        
        <div class="form-group">
            <label for="rating">Rating</label>
            <select name="rating" id="rating" required>
                <option value="1" {{ old('rating', $data->rating) == 1 ? 'selected' : '' }}>1 - Buruk</option>
                <option value="2" {{ old('rating', $data->rating) == 2 ? 'selected' : '' }}>2 - Kurang</option>
                <option value="3" {{ old('rating', $data->rating) == 3 ? 'selected' : '' }}>3 - Cukup</option>
                <option value="4" {{ old('rating', $data->rating) == 4 ? 'selected' : '' }}>4 - Baik</option>
                <option value="5" {{ old('rating', $data->rating) == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
            </select>
            @error('rating')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="review">Review</label>
            <textarea name="review" id="review" rows="4">{{ old('review', $data->review) }}</textarea>
            @error('review')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Update</button>
            <a href="{{ route('coffee-shop-reviews.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

