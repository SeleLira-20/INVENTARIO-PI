from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import HTTPBasic, HTTPBasicCredentials
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Usuario as UsuarioDB
from app.data.schemas import UsuarioCreate, LoginPinSchema
import secrets

router   = APIRouter(prefix="/v1/usuarios", tags=["Usuarios"])
security = HTTPBasic()

def verificar_admin(credentials: HTTPBasicCredentials = Depends(security)):
    ok = secrets.compare_digest(credentials.username, "admin") and \
         secrets.compare_digest(credentials.password, "Admin123!")
    if not ok:
        raise HTTPException(status_code=401, detail="Credenciales incorrectas",
                            headers={"WWW-Authenticate": "Basic"})
    return credentials.username

@router.get("/")
async def leer_usuarios(db: Session = Depends(get_db)):
    usuarios = db.query(UsuarioDB).all()
    return {"status": "200", "total": len(usuarios), "usuarios": usuarios}

@router.get("/{id_usuario}")
async def leer_usuario(id_usuario: int, db: Session = Depends(get_db)):
    u = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not u:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    return {"status": "200", "usuario": u}

@router.post("/", status_code=201, dependencies=[Depends(verificar_admin)])
async def crear_usuario(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    if db.query(UsuarioDB).filter(UsuarioDB.email == usuario.email).first():
        return {"status": "400", "mensaje": "Ya existe un usuario con ese correo"}
    if usuario.id_empleado and db.query(UsuarioDB).filter(UsuarioDB.id_empleado == usuario.id_empleado).first():
        return {"status": "400", "mensaje": "Ya existe un usuario con ese ID de empleado"}
    nuevo = UsuarioDB(**usuario.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Usuario creado", "usuario": nuevo}

@router.put("/{id_usuario}", dependencies=[Depends(verificar_admin)])
async def actualizar_usuario(id_usuario: int, datos: UsuarioCreate, db: Session = Depends(get_db)):
    u = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not u:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    for key, value in datos.model_dump().items():
        if key == "pin" and (value is None or value == ""):
            continue
        setattr(u, key, value)
    db.commit()
    db.refresh(u)
    return {"mensaje": "Usuario actualizado", "usuario": u}

@router.delete("/{id_usuario}", dependencies=[Depends(verificar_admin)])
async def eliminar_usuario(id_usuario: int, db: Session = Depends(get_db)):
    u = db.query(UsuarioDB).filter(UsuarioDB.id_usuario == id_usuario).first()
    if not u:
        return {"status": "404", "mensaje": "Usuario no encontrado"}
    db.delete(u)
    db.commit()
    return {"mensaje": f"Usuario {id_usuario} eliminado"}

@router.post("/login/pin")
async def login_pin(datos: LoginPinSchema, db: Session = Depends(get_db)):
    """Endpoint público — login de la app móvil."""
    u = db.query(UsuarioDB).filter(
        UsuarioDB.id_empleado == datos.id_empleado.strip().upper(),
        UsuarioDB.pin == datos.pin.strip()
    ).first()
    if not u:
        return {"status": "401", "mensaje": "ID de empleado o PIN incorrectos"}
    permisos = (u.permisos or "").split(",") if u.permisos else []
    return {
        "status": "200", "mensaje": "Login exitoso",
        "usuario": {
            "id_usuario":  u.id_usuario,
            "nombre":      u.nombre,
            "email":       u.email,
            "rol":         u.rol,
            "id_empleado": u.id_empleado,
            "permisos":    permisos,
        }
    }