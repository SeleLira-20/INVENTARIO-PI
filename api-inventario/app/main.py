from fastapi import FastAPI
from app.data.db import engine, Base

# Importar todos los modelos para que SQLAlchemy los registre antes de crear las tablas
from app.data import models  # noqa: F401

# Importar routers
from app.routers import productos, usuarios, movimientos, incidentes

# ── Crear todas las tablas automáticamente al iniciar ──
Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="Universal Inventory API",
    description="API de gestión de inventario con FastAPI + SQLAlchemy + PostgreSQL",
    version="1.0.0"
)

# ── Registrar routers ──
app.include_router(productos.router)
app.include_router(usuarios.router)
app.include_router(movimientos.router)
app.include_router(incidentes.router)


@app.get("/")
async def root():
    return {
        "mensaje": "Universal Inventory API corriendo 🚀",
        "docs": "/docs",
        "endpoints": [
            "/v1/productos",
            "/v1/usuarios",
            "/v1/movimientos",
            "/v1/incidentes"
        ]
    }