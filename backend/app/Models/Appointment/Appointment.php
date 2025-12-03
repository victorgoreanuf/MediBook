<?php

namespace App\Models\Appointment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User\User;

class Appointment extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'start_time',
        'end_time',
        'status',
        'price',
        'is_paid'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_paid' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
