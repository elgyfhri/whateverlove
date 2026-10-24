<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::withCount('orders')->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        $user->email_verified_at = $request->boolean('verified') ? now() : null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil ditambahkan.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'is_admin' => ['nullable', 'boolean'],
            'verified' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->is_admin = $request->boolean('is_admin');
        $user->email_verified_at = $request->boolean('verified') ? now() : null;

        $adminsCount = User::where('is_admin', true)->count();

        if (!$user->is_admin && $user->id === auth()->id() && $adminsCount <= 1) {
            return back()->withErrors(['is_admin' => 'Tidak bisa menghapus role admin dari diri sendiri.'])
                ->withInput();
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->name} berhasil diupdate.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['email' => 'Tidak bisa menghapus akun admin yang sedang dipakai.']);
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['email' => 'Tidak bisa menghapus admin terakhir.']);
        }

        $name = $user->name;

        DB::transaction(function () use ($user): void {
            $user->orders()->each(function ($order): void {
                $order->items()->delete();
                $order->delete();
            });

            $user->cart?->items()->delete();
            $user->cart?->delete();
            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', "User {$name} berhasil dihapus.");
    }
}