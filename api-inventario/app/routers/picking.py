from fastapi import APIRouter, Depends, HTTPException
from fastapi.security import HTTPBasic, HTTPBasicCredentials
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import OrdenPicking, DetalleOrdenPicking
import secrets, datetime

router   = APIRouter(prefix="/v1/picking", tags=["Picking"])
security = HTTPBasic()

def verificar_admin(credentials: HTTPBasicCredentials = Depends(security)):
    ok = secrets.compare_digest(credentials.username, "admin") and \
         secrets.compare_digest(credentials.password, "Admin123!")
    if not ok:
        raise HTTPException(status_code=401, detail="Credenciales incorrectas",
                            headers={"WWW-Authenticate": "Basic"})
    return credentials.username

@router.get("/")
async def listar_ordenes(db: Session = Depends(get_db)):
    ordenes = db.query(OrdenPicking).all()
    return {"status": "200", "total": len(ordenes), "ordenes": ordenes}

@router.get("/{id_orden}")
async def detalle_orden(id_orden: int, db: Session = Depends(get_db)):
    orden = db.query(OrdenPicking).filter(OrdenPicking.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    return {"status": "200", "orden": orden}

@router.post("/", status_code=201, dependencies=[Depends(verificar_admin)])
async def crear_orden(datos: dict, db: Session = Depends(get_db)):
    nueva = OrdenPicking(
        numero_orden       = f"PCK-{datetime.datetime.now().strftime('%Y-%m%d-%H%M')}",
        id_usuario_asignado= datos.get("id_usuario_asignado", 1),
        estado             = "Pendiente",
        fecha_creacion     = datetime.datetime.now(),
    )
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return {"mensaje": "Orden creada", "orden": nueva}

@router.put("/{id_orden}/estado")
async def actualizar_estado(id_orden: int, datos: dict, db: Session = Depends(get_db)):
    orden = db.query(OrdenPicking).filter(OrdenPicking.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    orden.estado = datos.get("estado", orden.estado)
    db.commit()
    db.refresh(orden)
    return {"mensaje": "Estado actualizado", "orden": orden}

@router.delete("/{id_orden}", dependencies=[Depends(verificar_admin)])
async def eliminar_orden(id_orden: int, db: Session = Depends(get_db)):
    orden = db.query(OrdenPicking).filter(OrdenPicking.id_orden == id_orden).first()
    if not orden:
        return {"status": "404", "mensaje": "Orden no encontrada"}
    db.delete(orden)
    db.commit()
    return {"mensaje": f"Orden {id_orden} eliminada"}