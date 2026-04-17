# app/middleware/auth.py
from fastapi import Request, HTTPException
from fastapi.responses import JSONResponse
from starlette.middleware.base import BaseHTTPMiddleware
import os

# Token secreto — debe coincidir con el del .env de Laravel
API_SECRET_TOKEN = os.getenv("API_SECRET_TOKEN", "universal_inventory_secret_2026")

# Rutas públicas que no requieren token
RUTAS_PUBLICAS = [
    "/",
    "/docs",
    "/openapi.json",
    "/redoc",
    "/v1/usuarios/login/pin",  # Login de la app móvil es público
]

class AuthMiddleware(BaseHTTPMiddleware):
    async def dispatch(self, request: Request, call_next):
        # Permitir rutas públicas
        if request.url.path in RUTAS_PUBLICAS:
            return await call_next(request)

        # Verificar token en header
        token = request.headers.get("X-API-Token")

        if not token or token != API_SECRET_TOKEN:
            return JSONResponse(
                status_code=401,
                content={"status": "401", "mensaje": "Token de acceso inválido o no proporcionado"}
            )

        return await call_next(request)