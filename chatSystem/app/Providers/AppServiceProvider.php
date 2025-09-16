<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        \App\Models\User::observe(\App\Observers\UserObserver::class);

        // Configuração para Sanctum trabalhar corretamente com SPA
        \Laravel\Sanctum\Sanctum::authenticateAccessTokensUsing(
            static function (\Laravel\Sanctum\PersonalAccessToken $accessToken, bool $is_valid) {
                if ($is_valid) {
                    return true;
                }

                // Verifique se o token expirou e renove-o se necessário
                if (config('sanctum.expiration')) {
                    return ! $accessToken->created_at->lte(
                        now()->subMinutes(config('sanctum.expiration'))
                    );
                }

                return true; // Se não há expiração configurada, considere válido
            }
        );
    }
}
