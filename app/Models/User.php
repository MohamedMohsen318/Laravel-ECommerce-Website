<?php

namespace App\Models;

use App\Models\Relations\DiscountRelations;
use App\Models\Relations\ProductFeedbackRelations;
use App\Models\Traits\HasCartTrait;
use App\Models\Traits\HasDiscountUsage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,
        HasRoles,
        Notifiable,
        HasCartTrait,
        HasDiscountUsage,
        DiscountRelations,
        ProductFeedbackRelations;

    protected string $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
}
