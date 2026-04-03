from sqlalchemy import Column, Integer, String, Numeric, Text, TIMESTAMP, ForeignKey
from sqlalchemy.sql import func
from app.data.db import Base


class Producto(Base):
    __tablename__ = "productos"

    id_producto      = Column(Integer, primary_key=True, index=True, autoincrement=True)
    sku              = Column(String(50), unique=True, nullable=False)
    nombre           = Column(String(100), nullable=False)
    categoria        = Column(String(50), default="Otros")          # ← NUEVO
    stock_actual     = Column(Integer, nullable=False, default=0)
    stock_minimo     = Column(Integer, nullable=False, default=5)
    precio_unitario  = Column(Numeric(10, 2), nullable=False)
    estado           = Column(String(20), default="Activo")


class Usuario(Base):
    __tablename__ = "usuarios"

    id_usuario = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre     = Column(String(100), nullable=False)
    email      = Column(String(100), unique=True, nullable=False)
    rol        = Column(String(50), default="Operador")


class Movimiento(Base):
    __tablename__ = "movimientos"

    id_movimiento    = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_producto      = Column(Integer, ForeignKey("productos.id_producto"))
    tipo_movimiento  = Column(String(20), nullable=False)
    cantidad         = Column(Integer, nullable=False)
    fecha_movimiento = Column(TIMESTAMP, server_default=func.now())
    id_usuario       = Column(Integer, nullable=False)
    observaciones    = Column(Text)


class OrdenPicking(Base):
    __tablename__ = "ordenes_picking"

    id_orden             = Column(Integer, primary_key=True, index=True, autoincrement=True)
    numero_orden         = Column(String(30), unique=True, nullable=False)
    id_usuario_asignado  = Column(Integer, nullable=False)
    fecha_creacion       = Column(TIMESTAMP, server_default=func.now())
    estado               = Column(String(20), default="Pendiente")


class DetalleOrdenPicking(Base):
    __tablename__ = "detalle_orden_picking"

    id_detalle           = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_orden             = Column(Integer, ForeignKey("ordenes_picking.id_orden"))
    id_producto          = Column(Integer, ForeignKey("productos.id_producto"))
    cantidad_requerida   = Column(Integer, nullable=False)
    cantidad_recolectada = Column(Integer, default=0)
    estado               = Column(String(20), default="Pendiente")


class Incidente(Base):
    __tablename__ = "incidentes"

    id_incidente       = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_producto        = Column(Integer, ForeignKey("productos.id_producto"), nullable=True)
    tipo_problema      = Column(String(50))
    descripcion        = Column(Text)
    id_usuario_reporta = Column(Integer)
    fecha_reporte      = Column(TIMESTAMP, server_default=func.now())
    nivel_urgencia     = Column(String(20))
    estado             = Column(String(20), default="Reportado")