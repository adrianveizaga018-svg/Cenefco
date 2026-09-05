<?php

namespace App\Infrastructure\Usuarios\Models;

use App\Infrastructure\Permisos\Models\Permiso;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
        'restringido_a_vendedor',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'restringido_a_vendedor' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'usuarios_roles', 'rol_id', 'usuario_id')
            ->withPivot('asignado_at', 'asignado_por');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'roles_permisos', 'rol_id', 'permiso_id');
    }

    public function tienePermiso(string $permiso): bool
    {
        $codigos = $this->permisos->pluck('codigo')->all();

        return in_array('*', $codigos, true) || in_array($permiso, $codigos, true);
    }
}
