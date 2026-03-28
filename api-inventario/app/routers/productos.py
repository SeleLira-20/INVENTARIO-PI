from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Producto as ProductoDB
from app.data.schemas import ProductoCreate

router = APIRouter(prefix="/v1/productos", tags=["Productos"])


@router.get("/")
async def leer_productos(db: Session = Depends(get_db)):
    productos = db.query(ProductoDB).all()
    return {"status": "200", "total": len(productos), "productos": productos}


@router.get("/{id_producto}")
async def leer_producto(id_producto: int, db: Session = Depends(get_db)):
    producto = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not producto:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    return {"status": "200", "producto": producto}


@router.post("/", status_code=201)
async def crear_producto(producto: ProductoCreate, db: Session = Depends(get_db)):
    nuevo = ProductoDB(**producto.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Producto agregado", "producto": nuevo}


@router.put("/{id_producto}")
async def actualizar_producto(id_producto: int, datos: ProductoCreate, db: Session = Depends(get_db)):
    producto = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not producto:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    for key, value in datos.model_dump().items():
        setattr(producto, key, value)
    db.commit()
    db.refresh(producto)
    return {"mensaje": "Producto actualizado", "producto": producto}


@router.delete("/{id_producto}")
async def eliminar_producto(id_producto: int, db: Session = Depends(get_db)):
    producto = db.query(ProductoDB).filter(ProductoDB.id_producto == id_producto).first()
    if not producto:
        return {"status": "404", "mensaje": "Producto no encontrado"}
    db.delete(producto)
    db.commit()
    return {"mensaje": f"Producto {id_producto} eliminado"}


@router.get("/alertas/stock-bajo")
async def stock_bajo(db: Session = Depends(get_db)):
    productos = db.query(ProductoDB).filter(
        ProductoDB.stock_actual <= ProductoDB.stock_minimo
    ).all()
    return {"status": "200", "total": len(productos), "productos": productos}