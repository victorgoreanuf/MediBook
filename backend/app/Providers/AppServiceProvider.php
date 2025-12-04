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
        // 1. Intercept the Email Verification URL generation
        VerifyEmail::createUrlUsing(function ($notifiable) {

            // 2. Generate the temporary signed URL for the BACKEND route
            // We do this to get the valid signature and expiration timestamp
            $backendUrl = URL::temporarySignedRoute(
                'verification.verify', // This matches the route name in your api.php
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // 3. Extract the query parameters (expires & signature)
            $parsedUrl = parse_url($backendUrl);
            $queryString = $parsedUrl['query'] ?? '';

            // 4. Construct the FRONTEND URL (Vue)
            // We swap the host to localhost:5173 but keep the critical security params
            return 'http://localhost:5173/verify-email/' .
                $notifiable->getKey() . '/' .
                sha1($notifiable->getEmailForVerification()) .
                '?' . $queryString;
        });
    }
}
