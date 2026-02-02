<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empleado_id',
        'perfil',
        'estado',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación con el empleado
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Relación: un usuario tiene muchos roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withTimestamps();
    }

    /**
     * Obtener todos los permisos del usuario a través de sus roles
     */
    public function getPermissions()
    {
        $permissions = collect();
        
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        return $permissions->unique('id');
    }

    /**
     * Obtener nombres de permisos del usuario
     */
    public function getPermissionNames(): array
    {
        return $this->getPermissions()->pluck('nombre')->toArray();
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permiso): bool
    {
        // Admin siempre tiene todos los permisos
        if ($this->hasRole('admin')) {
            return true;
        }

        // Verificar permisos a través de sus roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permiso)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $rol): bool
    {
        return $this->roles()->where('nombre', $rol)->exists();
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('nombre', $roles)->exists();
    }

    /**
     * Obtener las cuadrillas del usuario a través del empleado
     */
    public function cuadrillas()
    {
        return $this->empleado ? $this->empleado->cuadrillasActivas() : collect();
    }
}
