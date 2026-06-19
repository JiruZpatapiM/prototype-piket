@extends('layouts.app')

@section('content')
<div class="container mt-8 pb-8">
    <div class="flex justify-between items-center" style="margin-bottom: 2rem;">
        <h2 class="text-accent font-bold" style="font-size: 1.5rem; margin: 0;">Manajemen Pengguna</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="font-weight: bold; padding: 0.5rem 1rem; border-radius: 6px;">+ Tambah User</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.1); border-color: var(--success); color: var(--success); padding: 1rem; border-radius: 8px; border-left: 4px solid var(--success);">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.1); border-color: var(--danger); color: var(--danger); padding: 1rem; border-radius: 8px; border-left: 4px solid var(--danger);">
            {{ session('error') }}
        </div>
    @endif

    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-wrapper" style="border: none;">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>NAMA</th>
                        <th>USERNAME</th>
                        <th>ROLE</th>
                        <th>LOKASI</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td style="font-weight: bold; color: var(--text-primary);">{{ $user->name }}</td>
                        <td>{{ $user->username }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span style="background: rgba(14, 165, 233, 0.2); color: var(--accent-primary); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">ADMIN</span>
                            @else
                                <span style="background: rgba(255, 255, 255, 0.1); color: var(--text-secondary); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem;">OFFICER</span>
                            @endif
                        </td>
                        <td>{{ $user->lokasi_fix ?: '-' }}</td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Edit</a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; color: var(--danger); border-color: var(--danger);">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 2rem;">Belum ada user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
