from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Usuario as UsuarioDB
from app.data.schemas import UsuarioCreate

router = APIRouter(prefix="/v1/usuarios", tags=["Usuarios"])


@router.get("/")
async def leer_usuarios(db: Session = Depends(get_db)):
    usuarios = db.query(UsuarioDB).all()
    return {"status": "200", "total": len(usuarios), "usuarios": usuarios}


@router.post("/", status_code=201)
async def crear_usuario(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    nuevo = UsuarioDB(**usuario.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Usuario agregado", "usuario": nuevo}


@router.delete("/{id_usuario}")
async def eliminar_usuario(id_usuario: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not usuario:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    db.delete(usuario)
    db.commit()
    return {"mensaje": f"Usuario {id_usuario} eliminado"}