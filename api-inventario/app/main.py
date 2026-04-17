from fastapi import FastAPI, Depends, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBasic, HTTPBasicCredentials
from app.data.db import engine, Base
from app.data import models  # noqa: F401
from app.routers import productos, usuarios, movimientos, incidentes, ubicaciones, picking
import secrets

Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="Universal Inventory API",
    description="API de gestión de inventario con FastAPI + SQLAlchemy + PostgreSQL",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ── HTTP Basic Security ────────────────────────────────────────────────────
security = HTTPBasic()

# Credenciales del administrador
ADMIN_USER     = "admin"
ADMIN_PASSWORD = "Admin123!"

def verificar_admin(credentials: HTTPBasicCredentials = Depends(security)):
    """Verifica que las credenciales sean del administrador."""
    usuario_correcto    = secrets.compare_digest(credentials.username, ADMIN_USER)
    contrasena_correcta = secrets.compare_digest(credentials.password, ADMIN_PASSWORD)

    if not (usuario_correcto and contrasena_correcta):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Credenciales incorrectas",
            headers={"WWW-Authenticate": "Basic"},
        )
    return credentials.username

# ── Routers ────────────────────────────────────────────────────────────────
# Pasamos verificar_admin para proteger endpoints sensibles
app.include_router(productos.router)
app.include_router(usuarios.router)
app.include_router(movimientos.router)
app.include_router(incidentes.router)
app.include_router(ubicaciones.router)
app.include_router(picking.router)


@app.get("/")
async def root():
    return {
        "mensaje": "Universal Inventory API corriendo 🚀",
        "docs": "/docs",
        "endpoints": [
            "/v1/productos",
            "/v1/usuarios",
            "/v1/movimientos",
            "/v1/incidentes",
            "/v1/ubicaciones",
            "/v1/picking"
        ]
    }


# ── Endpoint protegido de ejemplo ─────────────────────────────────────────
@app.get("/admin/info", tags=["Admin"])
async def info_admin(usuario: str = Depends(verificar_admin)):
    """Endpoint protegido — solo el administrador puede acceder."""
    return {
        "mensaje": f"Bienvenido, {usuario}. Tienes acceso de administrador.",
        "sistema": "Universal Inventory",
        "version": "1.0.0"
    }