<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Las URIs que deben ser excluidas de la verificación CSRF.
     * Excluimos las rutas proxy hacia la API FastAPI porque
     * el CSRF no aplica para llamadas internas servidor-servidor.
     */
    protected $except = [
        'inventario/api/*',
    ];
}