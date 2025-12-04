<?php

namespace App\Models\User;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Appointment\Appointment;
use Illuminate\Support\Carbon; // Ensure Carbon is imported for timestamps
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property bool $is_doctor // Casted to boolean
 * @property string|null $doctor_public_id
 * @property string|null $specialization
 * @property string|null $bio
 * @property array|null $available_hours // Casted to array
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static UserFactory factory(...$parameters)
 */
class User extends Authenticatable
{
    // Add HasApiTokens to the list of traits
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_doctor',
        'doctor_public_id',
        'specialization',
        'bio',
        'available_hours',
    ];

    protected $casts = [
        'is_doctor' => 'boolean',
        // Note: 'array' cast implies the property is an array in PHP code
        'available_hours' => 'array',
        'password' => 'hashed',
    ];

    /**
     * Explicitly link to the correct Factory
     */
    protected static function newFactory(): Factory
    {
        return UserFactory::new();
    }

    /**
     * Bootstrap the model and auto-generate UUID for doctors.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            // If it's a doctor and doesn't have a public ID yet, generate one
            if ($user->is_doctor && empty($user->doctor_public_id)) {
                $user->doctor_public_id = (string) Str::uuid();
            }
        });
    }

    // --- Relationships ---

    /**
     * Appointments where this User is the Doctor.
     */
    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    /**
     * Appointments where this User is the Patient.
     */
    public function patientAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
