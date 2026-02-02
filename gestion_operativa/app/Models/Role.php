<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
                    ->withTimestamps();
    }

    public function hasPermission(string $permiso): bool
    {
        return $this->permissions()->where('nombre', $permiso)->exists();
    }

    public function getPermissionNames(): array
    {
        return $this->permissions->pluck('nombre')->toArray();
    }
}
