@extends('admin.layouts.main')

@section('title', 'Edit Anggota Komunitas')

@section('content')
<div class="page-title">Edit Anggota Komunitas</div>

<div class="form-wrapper">
    <form action="{{ route('community-members.update', $data->id) }}" method="POST">
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
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="member" {{ old('role', $data->role) == 'member' ? 'selected' : '' }}>Member</option>
                <option value="leader" {{ old('role', $data->role) == 'leader' ? 'selected' : '' }}>Leader</option>
            </select>
            @error('role')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Update</button>
            <a href="{{ route('community-members.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

