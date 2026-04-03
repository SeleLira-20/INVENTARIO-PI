from pydantic import BaseModel, Field
from typing import Optional


# ── PRODUCTOS ──────────────────────────────────────────────
class ProductoCreate(BaseModel):
    sku             : str   = Field(..., min_length=3, max_length=50,  example="LPT-001")
    nombre          : str   = Field(..., min_length=2, max_length=100, example="Laptop Dell")
    categoria       : str   = Field(default="Otros", example="Electrónicos")   # ← NUEVO
    stock_actual    : int   = Field(..., ge=0,  example=50)
    stock_minimo    : int   = Field(..., ge=0,  example=10)
    precio_unitario : float = Field(..., gt=0,  example=1299.99)


# ── USUARIOS ───────────────────────────────────────────────
class UsuarioCreate(BaseModel):
    nombre : str           = Field(..., min_length=3, max_length=100, example="Juan Martínez")
    email  : str           = Field(..., example="juan@empresa.com")
    rol    : Optional[str] = Field(default="Operador", example="Operador")


# ── MOVIMIENTOS ────────────────────────────────────────────
class MovimientoCreate(BaseModel):
    id_producto     : int           = Field(..., example=1)
    tipo_movimiento : str           = Field(..., example="ENTRADA")
    cantidad        : int           = Field(..., gt=0, example=10)
    id_usuario      : int           = Field(..., example=1)
    observaciones   : Optional[str] = Field(default=None, example="Compra inicial")


# ── INCIDENTES ─────────────────────────────────────────────
class IncidenteCreate(BaseModel):
    id_producto        : Optional[int] = Field(default=None, example=1)
    tipo_problema      : str           = Field(..., example="Producto Dañado")
    descripcion        : str           = Field(..., example="Pantalla rota")
    id_usuario_reporta : int           = Field(..., example=2)
    nivel_urgencia     : str           = Field(..., example="ALTA")