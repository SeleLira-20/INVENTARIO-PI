from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Incidente as IncidenteDB
from app.data.schemas import IncidenteCreate

router = APIRouter(prefix="/v1/incidentes", tags=["Incidentes"])


@router.get("/")
async def leer_incidentes(db: Session = Depends(get_db)):
    incidentes = db.query(IncidenteDB).all()
    return {"status": "200", "total": len(incidentes), "incidentes": incidentes}


@router.post("/", status_code=201)
async def reportar_incidente(incidente: IncidenteCreate, db: Session = Depends(get_db)):
    nuevo = IncidenteDB(**incidente.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Incidente reportado", "incidente": nuevo}


@router.put("/{id_incidente}/resolver")
async def resolver_incidente(id_incidente: int, db: Session = Depends(get_db)):
    incidente = db.query(IncidenteDB).filter(IncidenteDB.id_incidente == id_incidente).first()
    if not incidente:
        return {"status": "404", "mensaje": "Incidente no encontrado"}
    incidente.estado = "Resuelto"
    db.commit()
    db.refresh(incidente)
    return {"mensaje": "Incidente resuelto", "incidente": incidente}