@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Account</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Users</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $users->count() }} accounts</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="self-start md:self-auto inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
        <span class="material-symbols-outlined text-sm">person_add</span> Add User
    </a>
</div>

@if($users->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
        <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">group</span>
        <h2 class="font-headline-lg text-xl font-bold text-gray-900 uppercase tracking-tight">No users yet</h2>
        <p class="mt-2 text-gray-500 text-sm">Accounts will appear here after registration.</p>
    </div>
@else
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#f8f8f8] text-left font-label-sm text-[10px] uppercase tracking-widest text-gray-500">
                        <th class="px-6 py-4 font-bold">User</th>
                        <th class="px-6 py-4 font-bold">Email</th>
                        <th class="px-6 py-4 font-bold">Role</th>
                        <th class="px-6 py-4 font-bold">Verification</th>
                        <th class="px-6 py-4 font-bold">Orders</th>
                        <th class="px-6 py-4 font-bold">Joined</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-[#fafafa] transition">
                            <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-9 h-9 rounded-full bg-[#111111] text-white flex items-center justify-center font-label-sm text-xs font-bold flex-shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</div><div><p class="font-bold text-xs uppercase tracking-wider text-gray-900">{{ $user->name }}</p><p class="text-xs text-gray-400 font-label-sm">ID {{ $user->id }}</p></div></div></td>
                            <td class="px-6 py-4 font-label-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">@if($user->is_admin)<span class="font-label-sm text-[10px] uppercase tracking-widest bg-white text-black border border-black px-3 py-1 rounded-full">Admin</span>@else<span class="font-label-sm text-[10px] uppercase tracking-widest bg-[#f8f8f8] text-gray-600 px-3 py-1 rounded-full">Customer</span>@endif @if($user->id === auth()->id())<span class="ml-1 font-label-sm text-[10px] uppercase tracking-widest bg-black text-white px-3 py-1 rounded-full">You</span>@endif</td>
                            <td class="px-6 py-4">@if($user->hasVerifiedEmail())<span class="font-label-sm text-[10px] uppercase tracking-widest bg-green-50 text-green-600 px-3 py-1 rounded-full">Verified</span>@else<span class="font-label-sm text-[10px] uppercase tracking-widest bg-amber-100 text-amber-700 px-3 py-1 rounded-full">Unverified</span>@endif</td>
                            <td class="px-6 py-4 font-label-sm text-gray-600">{{ (int) $user->orders_count }}</td>
                            <td class="px-6 py-4 font-label-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4"><div class="flex justify-end gap-2"><a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-[#f8f8f8] hover:bg-gray-200 text-gray-700 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">Edit</a>@if($user->id !== auth()->id())<form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="delete-user-form" data-user-name="{{ $user->name }}">@csrf @method('DELETE')<button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-label-sm text-[10px] uppercase tracking-widest font-bold transition">Delete</button></form>@endif</div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(session('success'))
<script>
Swal.fire({
    title: 'Berhasil',
    text: @json(session('success')),
    icon: 'success',
    confirmButtonColor: '#111111',
    confirmButtonText: 'OK',
});
</script>
@endif

<script>
document.querySelectorAll('.delete-user-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus user?',
            text: 'Data pembelian dan keranjang user ini ikut dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>
@endsection
