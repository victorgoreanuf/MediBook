<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function ($notifiable) {

            $backendUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            $parsedUrl = parse_url($backendUrl);
            $queryString = $parsedUrl['query'] ?? '';

            return 'http://localhost:5173/verify-email/' .
                $notifiable->getKey() . '/' .
                sha1($notifiable->getEmailForVerification()) .
                '?' . $queryString;
        });
    }
}
