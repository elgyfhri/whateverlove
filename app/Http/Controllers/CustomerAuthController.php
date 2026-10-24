<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->has('back')) {
            session()->put('url.intended', $request->query('back'));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!auth()->user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if ($this->storePendingCartItem()) {
                return redirect()->route('cart.index')
                    ->with('success', 'Produk ditambahkan ke keranjang.');
            }

            return redirect()->intended(route('catalog'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }

    public function showVerifyNotice()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('catalog'));
        }

        return view('auth.verify');
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('catalog'));
        }

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }

    public function verifyEmailCode(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);
        $user = $request->user();

        if (!$user->email_verification_expires_at || now()->greaterThan($user->email_verification_expires_at) || !Hash::check($data['code'], $user->email_verification_code)) {
            throw ValidationException::withMessages(['code' => 'Kode salah atau sudah kedaluwarsa.']);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->forceFill([
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
        ])->save();

        return redirect()->intended(route('catalog'))->with('verified', true);
    }

    private function storePendingCartItem(): bool
    {
        $pending = session()->pull('cart.pending');

        if (!$pending || !isset($pending['product_id'])) {
            return false;
        }

        app(CartController::class)->addPending(auth()->user(), $pending);

        return true;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalog');
    }
}