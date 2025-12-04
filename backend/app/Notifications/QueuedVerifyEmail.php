<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <--- Critical
use Illuminate\Auth\Notifications\VerifyEmail;

class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue // <--- Implement this
{
    use Queueable;
}
