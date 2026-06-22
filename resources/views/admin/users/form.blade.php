@extends('layouts.app')

@section('content')
<div class="container mt-8 pb-8" style="max-width: 600px;">
    <div class="flex items-center gap-4" style="margin-bottom: 2rem;">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem;">&larr; Kembali</a>
        <h2 class="text-accent font-bold" style="font-size: 1.5rem; margin: 0;">{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h2>
    </div>

    <div class="card">
        <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: bold;">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                @error('name')<div class="text-sm mt-1" style="color: var(--danger);">{{ $message }}</div>@enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: bold;">Username / NPP</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" required>
                @error('username')<div class="text-sm mt-1" style="color: var(--danger);">{{ $message }}</div>@enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: bold;">Role</label>
                <select name="role" class="form-control" required style="background-color: var(--bg-tertiary);">
                    <option value="officer" {{ old('role', $user->role ?? '') == 'officer' ? 'selected' : '' }}>Officer (Piket)</option>
                    <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<div class="text-sm mt-1" style="color: var(--danger);">{{ $message }}</div>@enderror
            </div>

            <div class="form-group mb-4">
                <label class="form-label" style="font-weight: bold;">Lokasi Fix (Opsional)</label>
                @php
                    $cabangs = [
                        'TPK Makassar', 'Makassar New Port', 'TPK Kupang', 'TPK Ambon', 'TPK Bitung', 
                        'TPK Kendari', 'TPK Pantoloan', 'TPK Tarakan', 'TPK Jayapura', 'TPK Sorong', 
                        'TPK Biak', 'TPK Manokwari', 'TPK Merauke', 'Cabang Balikpapan', 'Cabang Samarinda', 
                        'Cabang Tolitoli', 'Gorontalo', 'Cabang Parepare', 'Cabang Ternate', 'Cabang Nunukan', 
                        'Cabang Tanjung Redeb', 'Cabang Fakfak'
                    ];
                    $currentLokasi = old('lokasi_fix', $user->lokasi_fix ?? '');
                @endphp
                <select name="lokasi_fix" class="form-control" style="background-color: var(--bg-tertiary);">
                    <option value="">-- Bebas Pilih Lokasi (Semua Cabang) --</option>
                    @foreach($cabangs as $cab)
                        <option value="{{ $cab }}" {{ $currentLokasi == $cab ? 'selected' : '' }}>{{ $cab }}</option>
                    @endforeach
                </select>
                <div class="text-xs text-secondary mt-1">Biarkan kosong jika user bebas memilih lokasi.</div>
                @error('lokasi_fix')<div class="text-sm mt-1" style="color: var(--danger);">{{ $message }}</div>@enderror
            </div>

            <div class="form-group mb-6">
                <label class="form-label" style="font-weight: bold;">Password {{ isset($user) ? '(Opsional)' : '' }}</label>
                <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
                @if(isset($user))
                    <div class="text-xs text-secondary mt-1">Isi hanya jika ingin mengganti password.</div>
                @endif
                @error('password')<div class="text-sm mt-1" style="color: var(--danger);">{{ $message }}</div>@enderror
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary" style="font-weight: bold; padding: 0.8rem 2rem;">SIMPAN</button>
            </div>
        </form>
    </div>
</div>
@endsection
