from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Ubicacion as UbicacionDB
from app.data.schemas import UbicacionCreate

router = APIRouter(prefix="/v1/ubicaciones", tags=["Ubicaciones"])


@router.get("/")
async def leer_ubicaciones(db: Session = Depends(get_db)):
    ubicaciones = db.query(UbicacionDB).all()
    return {"status": "200", "total": len(ubicaciones), "ubicaciones": ubicaciones}


@router.get("/{id_ubicacion}")
async def leer_ubicacion(id_ubicacion: int, db: Session = Depends(get_db)):
    ubicacion = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not ubicacion:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    return {"status": "200", "ubicacion": ubicacion}


@router.post("/", status_code=201)
async def crear_ubicacion(ubicacion: UbicacionCreate, db: Session = Depends(get_db)):
    nueva = UbicacionDB(**ubicacion.model_dump())
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return {"mensaje": "Ubicación creada", "ubicacion": nueva}


@router.put("/{id_ubicacion}")
async def actualizar_ubicacion(id_ubicacion: int, datos: UbicacionCreate, db: Session = Depends(get_db)):
    ubicacion = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not ubicacion:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    for key, value in datos.model_dump().items():
        setattr(ubicacion, key, value)
    db.commit()
    db.refresh(ubicacion)
    return {"mensaje": "Ubicación actualizada", "ubicacion": ubicacion}


@router.delete("/{id_ubicacion}")
async def eliminar_ubicacion(id_ubicacion: int, db: Session = Depends(get_db)):
    ubicacion = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not ubicacion:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    db.delete(ubicacion)
    db.commit()
    return {"mensaje": f"Ubicación {id_ubicacion} eliminada"}