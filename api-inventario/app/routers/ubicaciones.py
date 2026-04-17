from fastapi import APIRouter, Depends, HTTPException
from fastapi.security import HTTPBasic, HTTPBasicCredentials
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Ubicacion as UbicacionDB
from app.data.schemas import UbicacionCreate
import secrets

router   = APIRouter(prefix="/v1/ubicaciones", tags=["Ubicaciones"])
security = HTTPBasic()

def verificar_admin(credentials: HTTPBasicCredentials = Depends(security)):
    ok = secrets.compare_digest(credentials.username, "admin") and \
         secrets.compare_digest(credentials.password, "Admin123!")
    if not ok:
        raise HTTPException(status_code=401, detail="Credenciales incorrectas",
                            headers={"WWW-Authenticate": "Basic"})
    return credentials.username

@router.get("/")
async def listar_ubicaciones(db: Session = Depends(get_db)):
    ubicaciones = db.query(UbicacionDB).all()
    return {"status": "200", "total": len(ubicaciones), "ubicaciones": ubicaciones}

@router.get("/{id_ubicacion}")
async def obtener_ubicacion(id_ubicacion: int, db: Session = Depends(get_db)):
    u = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not u:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    return {"status": "200", "ubicacion": u}

@router.post("/", status_code=201, dependencies=[Depends(verificar_admin)])
async def crear_ubicacion(ubicacion: UbicacionCreate, db: Session = Depends(get_db)):
    nueva = UbicacionDB(**ubicacion.model_dump())
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return {"mensaje": "Ubicación creada", "ubicacion": nueva}

@router.put("/{id_ubicacion}", dependencies=[Depends(verificar_admin)])
async def actualizar_ubicacion(id_ubicacion: int, datos: UbicacionCreate, db: Session = Depends(get_db)):
    u = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not u:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    for key, value in datos.model_dump().items():
        setattr(u, key, value)
    db.commit()
    db.refresh(u)
    return {"mensaje": "Ubicación actualizada", "ubicacion": u}

@router.delete("/{id_ubicacion}", dependencies=[Depends(verificar_admin)])
async def eliminar_ubicacion(id_ubicacion: int, db: Session = Depends(get_db)):
    u = db.query(UbicacionDB).filter(UbicacionDB.id_ubicacion == id_ubicacion).first()
    if not u:
        return {"status": "404", "mensaje": "Ubicación no encontrada"}
    db.delete(u)
    db.commit()
    return {"mensaje": f"Ubicación {id_ubicacion} eliminada"}