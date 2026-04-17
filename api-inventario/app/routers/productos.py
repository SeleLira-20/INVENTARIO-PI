from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Producto as ProductoDB
from app.data.schemas import ProductoCreate
from fastapi.security import HTTPBasic, HTTPBasicCredentials
import secrets

router   = APIRouter(prefix="/v1/productos", tags=["Productos"])
security = HTTPBasic()

ADMIN_USER     = "admin"
ADMIN_PASSWORD = "Admin123!"

def verificar_admin(credentials: HTTPBasicCredentials = Depends(security)):
    ok_user = secrets.compare_digest(credentials.username, ADMIN_USER)
    ok_pass = secrets.compare_digest(credentials.password, ADMIN_PASSWORD)
    if not (ok_user and ok_pass):
        raise HTTPException(status_code=401, detail="Credenciales incorrectas",
                            headers={"WWW-Authenticate": "Basic"})
    return credentials.username


@router.get("/")
async def listar_productos(db: Session = Depends(get_db)):
    productos = db.query(ProductoDB).all()
    return {"status": "200", "total": len(productos), "productos": productos}


@router.get("/{id_producto}")
async def obtener_producto(id_producto: int, db: Session = Depends(get_db)):
    p = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not p:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    return {"status": "200", "producto": p}


@router.post("/", status_code=201)
async def crear_producto(producto: ProductoCreate, db: Session = Depends(get_db)):
    if db.query(ProductoDB).filter(ProductoDB.sku == producto.sku).first():
        return {"status": "400", "mensaje": "Ya existe un producto con ese SKU"}
    nuevo = ProductoDB(**producto.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Producto creado", "producto": nuevo}


@router.put("/{id_producto}", dependencies=[Depends(verificar_admin)])
async def actualizar_producto(id_producto: int, datos: ProductoCreate, db: Session = Depends(get_db)):
    p = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not p:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    for key, value in datos.model_dump().items():
        setattr(p, key, value)
    db.commit()
    db.refresh(p)
    return {"mensaje": "Producto actualizado", "producto": p}


@router.delete("/{id_producto}", dependencies=[Depends(verificar_admin)])
async def eliminar_producto(id_producto: int, db: Session = Depends(get_db)):
    p = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not p:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    db.delete(p)
    db.commit()
    return {"mensaje": f"Producto {id_producto} eliminado"}