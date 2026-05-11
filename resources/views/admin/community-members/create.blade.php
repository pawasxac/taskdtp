@extends('admin.layouts.main')

@section('title', 'Tambah Anggota Komunitas')

@section('content')
<div class="page-title">Tambah Anggota Komunitas</div>

<div class="form-wrapper">
    <form action="{{ route('community-members.store') }}" method="POST">
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
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" required>
                <option value="">Pilih User</option>
                @foreach($users as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->username }})</option>
                @endforeach
            </select>
            @error('user_id')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="member">Member</option>
                <option value="leader">Leader</option>
            </select>
            @error('role')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">Simpan</button>
            <a href="{{ route('community-members.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection

