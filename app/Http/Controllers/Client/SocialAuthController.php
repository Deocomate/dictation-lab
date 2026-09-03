<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    private const SUPPORTED_PROVIDERS = ['google', 'facebook'];

    public function redirect(string $provider): RedirectResponse
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')
                ->withErrors(['email' => 'Không thể xác thực với '.ucfirst($provider).'. Vui lòng thử lại.']);
        }

        $email = $socialUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản '.ucfirst($provider).' chưa chia sẻ email. Vui lòng dùng email/password hoặc cấp quyền email.']);
        }

        $providerId = (string) $socialUser->getId();
        if ($providerId === '') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Không thể lấy định danh tài khoản '.ucfirst($provider).'.']);
        }

        $displayName = $socialUser->getName() ?: Str::before($email, '@');

        $user = User::where('email', $email)->first();

        if ($user !== null && $user->role !== 'user') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản này thuộc khu vực quản trị và không thể đăng nhập tại trang học viên.']);
        }

        if ($user === null) {
            $user = User::create([
                'name' => $displayName,
                'email' => $email,
                'password' => null,
                'role' => 'user',
                'provider' => $provider,
                'provider_id' => $providerId,
                'subscription_tier' => 'free',
                'status' => 'active',
            ]);
        } else {
            if ($user->provider === null || $user->provider === $provider) {
                $user->update([
                    'name' => $user->name ?: $displayName,
                    'provider' => $provider,
                    'provider_id' => $providerId,
                ]);
            } elseif ($user->name === null || $user->name === '') {
                $user->update([
                    'name' => $displayName,
                ]);
            }
        }

        if ($user->status === 'locked') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đang bị khóa. Vui lòng liên hệ hỗ trợ.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')
            ->with('success', 'Đăng nhập bằng '.ucfirst($provider).' thành công.');
    }
}
