from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from app.data.db import get_db
from app.data.models import Movimiento as MovimientoDB, Producto as ProductoDB
from app.data.schemas import MovimientoCreate

router = APIRouter(prefix="/v1/movimientos", tags=["Movimientos"])


@router.get("/")
async def leer_movimientos(db: Session = Depends(get_db)):
    movimientos = db.query(MovimientoDB).all()
    return {"status": "200", "total": len(movimientos), "movimientos": movimientos}


@router.post("/", status_code=201)
async def registrar_movimiento(movimiento: MovimientoCreate, db: Session = Depends(get_db)):
    # Verificar que el producto existe
    producto = db.query(ProductoDB).filter(ProductoDB.id_producto == movimiento.id_producto).first()
    if not producto:
        return {"status": "404", "mensaje": "Producto no encontrado"}

    # Validar stock suficiente para salidas
    if movimiento.tipo_movimiento == "SALIDA":
        if producto.stock_actual < movimiento.cantidad:
            return {"status": "400", "mensaje": "Stock insuficiente"}
        producto.stock_actual -= movimiento.cantidad
    elif movimiento.tipo_movimiento == "ENTRADA":
        producto.stock_actual += movimiento.cantidad
    else:
        return {"status": "400", "mensaje": "tipo_movimiento debe ser ENTRADA o SALIDA"}

    # Registrar el movimiento
    nuevo = MovimientoDB(**movimiento.model_dump())
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {"mensaje": "Movimiento registrado", "movimiento": nuevo, "stock_actual": producto.stock_actual}