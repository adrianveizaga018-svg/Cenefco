<?php

namespace App\Http\Swagger;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResponse',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'nombre', type: 'string', example: 'Juan Pérez'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@cenefco.gob.bo'),
        new OA\Property(property: 'tipo', type: 'string', example: 'admin'),
        new OA\Property(property: 'activo', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'LoginResponse',
    properties: [
        new OA\Property(property: 'token', type: 'string', example: '1|abc123...'),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserResponse'),
    ]
)]
class AuthSwagger
{
    #[OA\Post(
        path: '/auth/login',
        tags: ['Auth'],
        summary: 'Iniciar sesión',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@cenefco.gob.bo'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login exitoso', content: new OA\JsonContent(ref: '#/components/schemas/LoginResponse')),
            new OA\Response(response: 401, description: 'Credenciales inválidas'),
            new OA\Response(response: 429, description: 'Demasiados intentos'),
        ]
    )]
    public function login() {}

    #[OA\Post(
        path: '/auth/logout',
        tags: ['Auth'],
        summary: 'Cerrar sesión',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Sesión cerrada'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function logout() {}

    #[OA\Post(
        path: '/auth/logout-all',
        tags: ['Auth'],
        summary: 'Cerrar todas las sesiones activas',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Todas las sesiones cerradas'),
        ]
    )]
    public function logoutAll() {}

    #[OA\Get(
        path: '/auth/me',
        tags: ['Auth'],
        summary: 'Obtener el usuario autenticado',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Datos del usuario', content: new OA\JsonContent(ref: '#/components/schemas/UserResponse')),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function me() {}

    #[OA\Put(
        path: '/auth/perfil',
        tags: ['Auth'],
        summary: 'Actualizar perfil del usuario autenticado',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', example: 'Juan Pérez'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'current_password', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'password_confirmation', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Perfil actualizado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function updatePerfil() {}

    #[OA\Post(
        path: '/auth/forgot-password',
        tags: ['Auth'],
        summary: 'Solicitar enlace de recuperación de contraseña',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Enlace enviado'),
            new OA\Response(response: 404, description: 'Email no encontrado'),
        ]
    )]
    public function forgotPassword() {}

    #[OA\Post(
        path: '/auth/reset-password',
        tags: ['Auth'],
        summary: 'Restablecer contraseña con token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'token'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'password_confirmation', type: 'string'),
                    new OA\Property(property: 'token', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Contraseña restablecida'),
            new OA\Response(response: 422, description: 'Token inválido o expirado'),
        ]
    )]
    public function resetPassword() {}
}
