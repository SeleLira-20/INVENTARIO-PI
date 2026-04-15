from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy import func
from app.data.db import get_db
from app.data.models import OrdenPicking as OrdenDB, DetalleOrdenPicking as DetalleDB
from pydantic import BaseModel, Field
from typing import Optional

router = APIRouter(prefix="/v1/picking", tags=["Picking"])


# ── Schemas inline ──────────────────────────────────────────────────────────
class OrdenCreate(BaseModel):
    numero_orden        : str = Field(..., example="PCK-2026-001")
    id_usuario_asignado : int = Field(..., example=1)
    estado              : Optional[str] = Field(default="Pendiente")


class EstadoUpdate(BaseModel):
    estado: str = Field(..., example="En Proceso")


# ── Endpoints ───────────────────────────────────────────────────────────────
@router.get("/")
async def leer_ordenes(db: Session = Depends(get_db)):
    ordenes = db.query(OrdenDB).order_by(OrdenDB.fecha_creacion.desc()).all()
    return {"status": "200", "total": len(ordenes), "ordenes": ordenes}


@router.get("/{id_orden}")
async def leer_orden(id_orden: int, db: Session = Depends(get_db)):
    orden = db.query(OrdenDB).filter(OrdenDB.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    detalles = db.query(DetalleDB).filter(DetalleDB.id_orden == id_orden).all()
    return {"status": "200", "orden": orden, "detalles": detalles}


@router.post("/", status_code=201)
async def crear_orden(orden: OrdenCreate, db: Session = Depends(get_db)):
    # Verificar que el número de orden no exista
    existe = db.query(OrdenDB).filter(OrdenDB.numero_orden == orden.numero_orden).first()
    if existe:
        return {"status": "400", "mensaje": "El número de orden ya existe"}
    nueva = OrdenDB(**orden.model_dump())
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return {"mensaje": "Orden creada", "orden": nueva}


@router.put("/{id_orden}/estado")
async def actualizar_estado(id_orden: int, datos: EstadoUpdate, db: Session = Depends(get_db)):
    orden = db.query(OrdenDB).filter(OrdenDB.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    estados_validos = ["Pendiente", "En Proceso", "Completada", "Cancelada"]
    if datos.estado not in estados_validos:
        return {"status": "400", "mensaje": f"Estado inválido. Usa: {estados_validos}"}
    orden.estado = datos.estado
    db.commit()
    db.refresh(orden)
    return {"mensaje": f"Orden actualizada a {datos.estado}", "orden": orden}


@router.delete("/{id_orden}")
async def eliminar_orden(id_orden: int, db: Session = Depends(get_db)):
    orden = db.query(OrdenDB).filter(OrdenDB.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    db.delete(orden)
    db.commit()
    return {"mensaje": f"Orden {id_orden} eliminada"}