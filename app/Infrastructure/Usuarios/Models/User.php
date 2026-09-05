<?php

namespace App\Infrastructure\Usuarios\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'google_id',
        'avatar_url',
        'password_hash',
        'tipo',
        'ci',
        'telefono',
        'activo',
        'email_verificado',
        'codigo_verificacion',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'email_verificado' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'usuarios_roles',
            'usuario_id',
            'rol_id'
        )->withPivot('asignado_at', 'asignado_por')->with('permisos');
    }

    public function tienePermiso(string $permiso): bool
    {
        foreach ($this->roles as $rol) {
            if ($rol->tienePermiso($permiso)) {
                return true;
            }
        }

        return false;
    }

    public function esAdmin(): bool
    {
        return $this->tienePermiso('*');
    }
}
