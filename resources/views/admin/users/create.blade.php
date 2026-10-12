@extends('layouts.admin')

@section('title', 'Add User')

@section('content')
<div class="max-w-2xl">
    <div class="mb-10">
        <p class="font-label-sm text-xs uppercase tracking-widest text-gray-400 mb-2">Account</p>
        <h1 class="font-headline-lg text-3xl font-extrabold uppercase tracking-tight">Add User</h1>
        <p class="mt-2 text-sm text-gray-500">Buat akun pelanggan atau admin baru.</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-8">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-8">
            @csrf

            <div>
                <label for="name" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Name <span class="text-secondary">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-transparent border-b-2 @error('name') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Email <span class="text-secondary">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-transparent border-b-2 @error('email') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block font-label-sm text-xs uppercase tracking-widest text-gray-700 mb-2">Password <span class="text-secondary">*</span></label>
                <input id="password" type="password" name="password" required placeholder="Minimal 8 karakter"
                    class="w-full bg-transparent border-b-2 @error('password') border-red-300 @else border-gray-300 @enderror focus:border-black outline-none py-2.5 text-sm transition-colors">
                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black">
                    <span class="font-label-sm text-xs uppercase tracking-widest text-gray-700">Role Admin</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="verified" value="1" {{ old('verified') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 text-black focus:ring-black">
                    <span class="font-label-sm text-xs uppercase tracking-widest text-gray-700">Email Sudah Terverifikasi</span>
                </label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="px-6 py-3 bg-black text-white font-bold uppercase text-xs tracking-widest rounded-full hover:opacity-80 transition">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-[#f8f8f8] text-gray-700 font-bold uppercase text-xs tracking-widest rounded-full hover:bg-gray-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection