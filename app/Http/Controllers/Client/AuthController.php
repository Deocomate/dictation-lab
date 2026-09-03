<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Client\ClientAuthService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly ClientAuthService $authService) {}

    public function showLogin(): View
    {
        return view('client.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['remember'] = $request->boolean('remember');

        if (! $this->authService->login($credentials)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.']);
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Đăng nhập thành công!');
    }

    public function showRegister(): View
    {
        return view('client.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $this->authService->register($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')
            ->with('success', 'Chào mừng bạn đến với Dictation Lab!');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('home')
            ->with('success', 'Bạn đã đăng xuất thành công.');
    }

    public function showForgotPassword(): View
    {
        return view('client.auth.forgot-password');
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(Request $request, string $token): View
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('success', 'Mật khẩu đã được đặt lại. Hãy đăng nhập!');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
