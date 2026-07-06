<?php

namespace App\Models;

use App\Notifications\ResetPasswordCustom;
use App\Notifications\VerificarEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes, HasRoles {
        hasPermissionTo as spatieHasPermissionTo;
        getAllPermissions as spatieGetAllPermissions;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'password_changed_at',
        'profile_photo_path',
        'rol',
        'politica_uno',
        'politica_dos',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    public function perfilCliente()
    {
        return $this->hasOne(Cliente::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class)
            ->withPivot('is_principal')
            ->withTimestamps();
    }

    /**
     * Get the address associated with the user.
     */
    public function direccion()
    {
        return $this->hasOne(Direccion::class);
    }

    public function ticketsParticipantes()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_participantes')
            ->withPivot('activo')
            ->withTimestamps();
    }

    public function tiposSolicitud()
    {
        return $this->belongsToMany(TipoSolicitud::class, 'tipo_solicitud_user')
            ->withPivot('is_principal')
            ->withTimestamps();
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->spatieHasPermissionTo($permission, $guardName)) {
            return true;
        }
        return $this->hasInheritedPermissionTo($permission, $guardName);
    }

    public function getAllPermissions(): Collection
    {
        return $this->spatieGetAllPermissions()
            ->merge($this->getInheritedPermissions())
            ->unique('id')
            ->values();
    }

    public function getInheritedPermissions(?string $guardName = null): Collection
    {
        $guardName = $guardName ?? $this->getDefaultGuardName();
        $inheritedPermissions = collect();
        $assignedRoles = $this->roles;
        foreach ($assignedRoles as $assignedRole) {
            $roleIds = collect([$assignedRole->id])->merge($assignedRole->cadenaSuperior()->pluck('id'));
            $inheritedPermissions = $inheritedPermissions->merge(
                Permission::query()
                    ->where('guard_name', $guardName)
                    ->whereHas('roles', function ($q) use ($roleIds) {
                        $q->whereIn('roles.id', $roleIds);
                    })
                    ->get()
            );
        }
        return $inheritedPermissions->unique('id')->values();
    }

    public function hasInheritedPermissionTo($permission, ?string $guardName = null): bool
    {
        $guardName = $guardName ?? $this->getDefaultGuardName();
        try {
            $permissionModel = $this->getPermissionClass()::findByName($permission, $guardName);
        } catch (\Exception $e) {
            if (is_numeric($permission)) {
                try {
                    $permissionModel = $this->getPermissionClass()::findById($permission, $guardName);
                } catch (\Exception $e) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return $this->getInheritedPermissions($guardName)->contains('id', $permissionModel->id);
    }

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
            'activo' => 'boolean',
            'politica_uno' => 'boolean',
            'politica_dos' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificarEmail());
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordCustom($token));
    }

    public function necesitaActualizarDatosPersonales(): bool
    {
        return empty(optional($this->perfilCliente)->telefono_principal);
    }

    public function necesitaActualizarDireccion(): bool
    {
        return !$this->direccion()->exists();
    }
}
