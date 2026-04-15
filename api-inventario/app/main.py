from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.data.db import engine, Base
from app.data import models  # noqa: F401
from app.routers import productos, usuarios, movimientos, incidentes, ubicaciones, picking

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