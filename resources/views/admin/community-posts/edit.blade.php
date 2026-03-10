@extends('admin.layouts.main')

@section('title', 'Edit Postingan Komunitas')

@section('content')
<div class="page-title">Edit Postingan Komunitas</div>

<div class="form-wrapper">
    <form action="{{ route('community-posts.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="community_id">Komunitas</label>
            <select name="community_id" id="community_id" required>
                <option value="">Pilih Komunitas</option>
                @foreach($komunitas as $item)
                    <option value="{{ $item->id }}" {{ old('community_id', $data->community_id) == $item->id ? 'selected' : '' }}>{{ $item->nama_komunitas }}</option>
                @endforeach
            </select>
            @error('community_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" required>
                <option value="">Pilih User</option>
                @foreach($users as $item)
                    <option value="{{ $item->id }}" {{ old('user_id', $data->user_id) == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->username }})</option>
                @endforeach
            </select>
            @error('user_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="6" required>{{ old('content', $data->content) }}</textarea>
            @error('content')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Update</button>
            <a href="{{ route('community-posts.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

