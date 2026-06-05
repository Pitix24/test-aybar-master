<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->input('email'))->first();

            if (! $user) {
                return null;
            }

            if (! $user->activo) {
                throw ValidationException::withMessages([
                    'email' => 'Las cuentas desactivadas no pueden iniciar sesión ni solicitar recuperación de contraseña.',
                ]);
            }

            return Hash::check($request->input('password'), $user->password) ? $user : null;
        });

        // Redirección personalizada después de login
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return match ($request->user()->rol) {
                        'admin' => redirect()->route('erp.home'),
                        'cliente' => redirect()->route('cliente.home'),
                        default => redirect('/'),
                    };
                }
            };
        });

        // Redirección después de verificar email según rol
        $this->app->singleton(VerifyEmailResponse::class, function () {
            return new class implements VerifyEmailResponse {
                public function toResponse($request)
                {
                    $user = $request->user();

                    if ($user->rol === 'admin') {
                        return redirect()->route('erp.home');
                    }

                    if ($user->rol === 'cliente') {
                        return redirect()->route('cliente.home');
                    }

                    return '/';
                }
            };
        });

        $this->app->singleton(FailedPasswordResetLinkRequestResponse::class, function () {
            return new class implements FailedPasswordResetLinkRequestResponse {
                public function toResponse($request)
                {
                    $email = $request->input('email');
                    $user = User::where('email', $email)->first();

                    $message = $user && ! $user->activo
                        ? 'Las cuentas desactivadas no pueden iniciar sesión ni solicitar recuperación de contraseña.'
                        : trans('passwords.user');

                    return back()
                        ->withInput($request->only('email'))
                        ->withErrors(['email' => $message]);
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn() => view('livewire.auth.login'));
        Fortify::verifyEmailView(fn() => view('livewire.auth.verify-email'));
        Fortify::twoFactorChallengeView(fn() => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn() => view('livewire.auth.confirm-password'));
        Fortify::registerView(fn() => view('livewire.auth.register'));
        Fortify::resetPasswordView(fn() => view('livewire.auth.reset-password'));
        Fortify::requestPasswordResetLinkView(fn() => view('livewire.auth.forgot-password'));
    }

    /**
     * Configure rate limiting.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
