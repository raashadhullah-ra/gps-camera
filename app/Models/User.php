<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'username',
        'displayname',
        'email',
        'password',
        'dob',
        'city',
        'district',
        'state',
        'country',
        'address',
        'mobilenumber',
        'language',
        'timezone',
        'admin_id',
        'role',
        'department',
        'two_factor_enabled',
        'status',
        'active_sessions_count',
        'doj',
        'dor',
        'isblocked',
        'lastloginat',
        'passwordchangedat',
        'email_verified_at',
    ];

    /**
     * User Status Constants
     * active = 1, pending = 0, suspended/blocked = 2
     */
    public const STATUS_PENDING = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_SUSPENDED = 2;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'lastloginat' => 'datetime',
            'passwordchangedat' => 'datetime',
            'dob' => 'date',
            'doj' => 'date',
            'dor' => 'date',
            'status' => 'integer',
            'active_sessions_count' => 'integer',
            'isblocked' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the textual label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ((int) $this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_PENDING => 'Pending',
            default => 'Pending',
        };
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }
}
