<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    private string $apiBase;

    public function __construct()
    {
        $this->apiBase = env('API_URL', 'http://localhost:8000') . '/v1/productos';
    }

    private function curl(string $url, string $method = 'GET', array $body = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:Admin123!');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        if ($body !== null) {
            $json = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ]);
        }

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status, json_decode($response, true)];
    }

    public function index()
    {
        return view('inventario.index');
    }

    public function listar()
    {
        [$status, $data] = $this->curl($this->apiBase . '/');
        return response()->json($data, $status ?: 500);
    }

    public function crear(Request $request)
    {
        $validated = $request->validate([
            'sku'             => 'required|string|min:3|max:50',
            'nombre'          => 'required|string|min:2|max:100',
            'categoria'       => 'nullable|string|max:50',
            'stock_actual'    => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0.01',
        ]);

        [$status, $data] = $this->curl($this->apiBase . '/', 'POST', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function actualizar(Request $request, int $id)
    {
        $validated = $request->validate([
            'sku'             => 'required|string|min:3|max:50',
            'nombre'          => 'required|string|min:2|max:100',
            'categoria'       => 'nullable|string|max:50',
            'stock_actual'    => 'required|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'precio_unitario' => 'required|numeric|min:0.01',
        ]);

        [$status, $data] = $this->curl("{$this->apiBase}/{$id}", 'PUT', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function eliminar(int $id)
    {
        [$status, $data] = $this->curl("{$this->apiBase}/{$id}", 'DELETE');
        return response()->json($data, $status ?: 500);
    }

    // ── Registrar movimiento ───────────────────────────────────────────────
    public function registrarMovimiento(Request $request)
    {
        $validated = $request->validate([
            'id_producto'     => 'required|integer',
            'tipo_movimiento' => 'required|in:ENTRADA,SALIDA',
            'cantidad'        => 'required|integer|min:1',
            'id_usuario'      => 'required|integer',
            'observaciones'   => 'nullable|string',
        ]);

        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/movimientos/';
        [$status, $data] = $this->curl($apiUrl, 'POST', $validated);
        return response()->json($data, $status ?: 500);
    }

    // ── Listar movimientos ─────────────────────────────────────────────────
    public function movimientos()
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/movimientos/';
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }





    // ── Picking ────────────────────────────────────────────────────────────────
    public function listarPicking()
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/picking/';
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }

    public function detalleOrden(int $id)
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/picking/{$id}";
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }

    public function crearOrden(Request $request)
    {
        $validated = $request->validate([
            'numero_orden'        => 'required|string',
            'id_usuario_asignado' => 'required|integer',
            'estado'              => 'nullable|string',
        ]);
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/picking/';
        [$status, $data] = $this->curl($apiUrl, 'POST', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function actualizarEstadoOrden(Request $request, int $id)
    {
        $validated = $request->validate(['estado' => 'required|string']);
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/picking/{$id}/estado";
        [$status, $data] = $this->curl($apiUrl, 'PUT', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function eliminarOrden(int $id)
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/picking/{$id}";
        [$status, $data] = $this->curl($apiUrl, 'DELETE');
        return response()->json($data, $status ?: 500);
    }

    // ── Ubicaciones ────────────────────────────────────────────────────────────
    public function listarUbicaciones()
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/ubicaciones/';
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }

    public function crearUbicacion(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|min:2|max:100',
            'codigo'      => 'required|string|min:1|max:50',
            'descripcion' => 'nullable|string',
            'capacidad'   => 'nullable|integer|min:0',
            'ocupacion'   => 'nullable|integer|min:0',
            'nivel'       => 'nullable|integer|min:1|max:3',
            'id_padre'    => 'nullable|integer',
        ]);
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/ubicaciones/';
        [$status, $data] = $this->curl($apiUrl, 'POST', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function actualizarUbicacion(Request $request, int $id)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|min:2|max:100',
            'codigo'      => 'required|string|min:1|max:50',
            'descripcion' => 'nullable|string',
            'capacidad'   => 'nullable|integer|min:0',
            'ocupacion'   => 'nullable|integer|min:0',
            'nivel'       => 'nullable|integer|min:1|max:3',
            'id_padre'    => 'nullable|integer',
        ]);
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/ubicaciones/{$id}";
        [$status, $data] = $this->curl($apiUrl, 'PUT', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function eliminarUbicacion(int $id)
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/ubicaciones/{$id}";
        [$status, $data] = $this->curl($apiUrl, 'DELETE');
        return response()->json($data, $status ?: 500);
    }

    // ── Usuarios ───────────────────────────────────────────────────────────────
    public function listarUsuarios()
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/usuarios/';
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }

    public function crearUsuario(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|min:3|max:100',
            'email'       => 'required|email',
            'rol'         => 'nullable|string',
            'id_empleado' => 'nullable|string',
            'pin'         => 'nullable|string|size:4',
            'permisos'    => 'nullable|string',
        ]);
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/usuarios/';
        [$status, $data] = $this->curl($apiUrl, 'POST', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function actualizarUsuario(Request $request, int $id)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|min:3|max:100',
            'email'       => 'required|email',
            'rol'         => 'nullable|string',
            'id_empleado' => 'nullable|string',
            'pin'         => 'nullable|string|size:4',
            'permisos'    => 'nullable|string',
        ]);
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/usuarios/{$id}";
        [$status, $data] = $this->curl($apiUrl, 'PUT', $validated);
        return response()->json($data, $status ?: 500);
    }

    public function eliminarUsuario(int $id)
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/usuarios/{$id}";
        [$status, $data] = $this->curl($apiUrl, 'DELETE');
        return response()->json($data, $status ?: 500);
    }

    // ── Incidentes ─────────────────────────────────────────────────────────────
    public function incidentes()
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . '/v1/incidentes/';
        [$status, $data] = $this->curl($apiUrl);
        return response()->json($data, $status ?: 500);
    }


    // ── Resolver incidente ─────────────────────────────────────────────────────
    public function resolverIncidente(int $id)
    {
        $apiUrl = env('API_URL', 'http://localhost:8000') . "/v1/incidentes/{$id}/resolver";
        [$status, $data] = $this->curl($apiUrl, 'PUT');
        return response()->json($data, $status ?: 500);
    }

    // ── Alertas stock bajo ─────────────────────────────────────────────────
    public function alertasStockBajo()
    {
        [$status, $data] = $this->curl($this->apiBase . '/alertas/stock-bajo');
        return response()->json($data, $status ?: 500);
    }
}