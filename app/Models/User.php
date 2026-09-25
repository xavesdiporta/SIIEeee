<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
// Use this for Stripe
use Laravel\Fortify\TwoFactorAuthenticatable;
//use Laravel\Paddle\Billable; // Use this for Paddle
//use LemonSqueezy\Laravel\Billable; // Use this for LemonSqueezy

use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasPermissions;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cargo',
        'cne_number',
        'cp_card_number',
        'seccao',
        'chefe',
        'chefes',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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
            'password' => 'hashed',
            'chefe' => 'boolean',
            'chefes' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            // Sincroniza 'chefe' e 'chefes' caso a coluna na base de dados use uma ou outra convenção
            try {
                if (isset($user->attributes['chefe']) && !isset($user->attributes['chefes'])) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefes') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefe')) {
                        $user->attributes['chefes'] = (bool) $user->attributes['chefe'];
                        unset($user->attributes['chefe']);
                    }
                } elseif (isset($user->attributes['chefes']) && !isset($user->attributes['chefe'])) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefe') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefes')) {
                        $user->attributes['chefe'] = (bool) $user->attributes['chefes'];
                        unset($user->attributes['chefes']);
                    }
                }
            } catch (\Throwable $e) {
                // Em caso de impossibilidade de aceder ao schema, não bloqueia o save
            }
        });
    }

    public function getChefeAttribute(): bool
    {
        return (bool) ($this->attributes['chefe'] ?? $this->attributes['chefes'] ?? false);
    }

    /**
     * Scope para obter apenas membros (exclui chefes/dirigentes das tabelas das secções).
     */
    public function scopeMembros($query)
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefe')) {
                return $query->where(function ($q) {
                    $q->where('chefe', false)->orWhereNull('chefe');
                });
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'chefes')) {
                return $query->where(function ($q) {
                    $q->where('chefes', false)->orWhereNull('chefes');
                });
            }
        } catch (\Throwable $e) {
            return $query->where(function ($q) {
                $q->where('chefe', false)->orWhereNull('chefe');
            });
        }

        return $query;
    }

    // Relations
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Check if user can access panel,
        //        return str_ends_with($this->email, '@yourdomain.com') && $this->hasVerifiedEmail();

        return $this->is_admin || $this->hasRole('admin');
    }

    public function trialIsUsed()
    {
        return $this->trial_is_used;
    }

    public function getSeccaoNomeAttribute(): string
    {
        return match ($this->seccao) {
            'lobitos'      => 'Lobitos',
            'exploradores' => 'Exploradores',
            'pioneiros'    => 'Pioneiros',
            'cla'          => 'Clã',
            default        => 'Clã',
        };
    }
}
