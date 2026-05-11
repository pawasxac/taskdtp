@extends('admin.layouts.main')

@section('title', 'Tambah Gathering Request')

@section('content')
<div class="page-title">Tambah Gathering Request</div>

<div class="form-wrapper">
    <form action="{{ route('gathering-requests.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="community_id">Komunitas</label>
            <select name="community_id" id="community_id" required>
                <option value="">Pilih Komunitas</option>
                @foreach($komunitas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_komunitas }}</option>
                @endforeach
            </select>
            @error('community_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="coffee_shop_id">Coffee Shop</label>
            <select name="coffee_shop_id" id="coffee_shop_id" required>
                <option value="">Pilih Coffee Shop</option>
                @foreach($coffeeShops as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
            </select>
            @error('coffee_shop_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required value="{{ old('title') }}">
            @error('title')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4">{{ old('description') }}</textarea>
            @error('description')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="event_date">Tanggal Event</label>
            <input type="date" name="event_date" id="event_date" required value="{{ old('event_date') }}">
            @error('event_date')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
            <a href="{{ route('gathering-requests.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

