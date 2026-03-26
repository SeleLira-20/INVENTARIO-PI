from pydantic import BaseModel

class Material(BaseModel):
    nombre: str
    sku: str
    ubicacion: str
    cantidad: int
    stockMinimo: int
    stockMaximo: int
    estado: str
    ultimaActualizacion: str
    categoria: str
    proveedor: str