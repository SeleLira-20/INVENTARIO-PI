from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Usuario as UsuarioDB
from app.data.schemas import UsuarioCreate, LoginPinSchema

router = APIRouter(prefix="/v1/usuarios", tags=["Usuarios"])


@router.get("/")
async def leer_usuarios(db: Session = Depends(get_db)):
    usuarios = db.query(UsuarioDB).all()
    return {"status": "200", "total": len(usuarios), "usuarios": usuarios}


@router.get("/{id_usuario}")
async def leer_usuario(id_usuario: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not usuario:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    return {"status": "200", "usuario": usuario}


@router.post("/", status_code=201)
async def crear_usuario(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    # Verificar email único
    if db.query(UsuarioDB).filter(UsuarioDB.email == usuario.email).first():
        return {"status": "400", "mensaje": "Ya existe un usuario con ese correo"}
    # Verificar id_empleado único
    if usuario.id_empleado and db.query(UsuarioDB).filter(UsuarioDB.id_empleado == usuario.id_empleado).first():
        return {"status": "400", "mensaje": "Ya existe un usuario con ese ID de empleado"}
    nuevo = UsuarioDB(**usuario.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Usuario creado", "usuario": nuevo}


@router.put("/{id_usuario}")
async def actualizar_usuario(id_usuario: int, datos: UsuarioCreate, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not usuario:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    for key, value in datos.model_dump().items():
        setattr(usuario, key, value)
    db.commit()
    db.refresh(usuario)
    return {"mensaje": "Usuario actualizado", "usuario": usuario}


@router.delete("/{id_usuario}")
async def eliminar_usuario(id_usuario: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not usuario:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    db.delete(usuario)
    db.commit()
    return {"mensaje": f"Usuario {id_usuario} eliminado"}


@router.post("/login/pin")
async def login_pin(datos: LoginPinSchema, db: Session = Depends(get_db)):
    """Endpoint para que la app móvil valide el PIN del operador."""
    usuario = db.query(UsuarioDB).filter(
        UsuarioDB.id_empleado == datos.id_empleado.strip().upper(),
        UsuarioDB.pin == datos.pin.strip()
    ).first()
    if not usuario:
        return {"status": "401", "mensaje": "ID de empleado o PIN incorrectos"}
    permisos = (usuario.permisos or "inventario,escanear,reportes,picking").split(",")
    return {
        "status": "200",
        "mensaje": "Login exitoso",
        "usuario": {
            "id_usuario":  usuario.id_usuario,
            "nombre":      usuario.nombre,
            "email":       usuario.email,
            "rol":         usuario.rol,
            "id_empleado": usuario.id_empleado,
            "permisos":    permisos,
        }
    }