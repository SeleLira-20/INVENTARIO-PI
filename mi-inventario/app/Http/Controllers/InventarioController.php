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

    // ── Alertas stock bajo ─────────────────────────────────────────────────
    public function alertasStockBajo()
    {
        [$status, $data] = $this->curl($this->apiBase . '/alertas/stock-bajo');
        return response()->json($data, $status ?: 500);
    }
}