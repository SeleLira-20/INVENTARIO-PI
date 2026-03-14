from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI(
    title="CRUD de gestión de inventario"
)

materiales = [
    {
        "id": 1,
        "nombre": "Laptop HP EliteBook 840 G8",
        "sku": "LPT-HP-001",
        "ubicacion": "A-12-03",
        "cantidad": 25,
        "stockMinimo": 10,
        "stockMaximo": 50,
        "estado": "stock_normal",
        "ultimaActualizacion": "2026-03-06",
        "categoria": "Electrónica",
        "proveedor": "HP México"
    },
    {
        "id": 2,
        "nombre": "Monitor Dell UltraSharp 24\"",
        "sku": "MON-DL-002",
        "ubicacion": "B-05-01",
        "cantidad": 8,
        "stockMinimo": 15,
        "stockMaximo": 40,
        "estado": "stock_bajo",
        "ultimaActualizacion": "2026-03-05",
        "categoria": "Electrónica",
        "proveedor": "Dell Technologies"
    },
    {
        "id": 3,
        "nombre": "Teclado Logitech MX Keys",
        "sku": "TEC-LG-003",
        "ubicacion": "A-08-02",
        "cantidad": 3,
        "stockMinimo": 10,
        "stockMaximo": 30,
        "estado": "stock_critico",
        "ultimaActualizacion": "2026-03-04",
        "categoria": "Accesorios",
        "proveedor": "Logitech"
    },
    {
        "id": 4,
        "nombre": "Mouse Inalámbrico Logitech",
        "sku": "MOU-LG-004",
        "ubicacion": "C-02-04",
        "cantidad": 42,
        "stockMinimo": 15,
        "stockMaximo": 60,
        "estado": "stock_normal",
        "ultimaActualizacion": "2026-03-06",
        "categoria": "Accesorios",
        "proveedor": "Logitech"
    },
    {
        "id": 5,
        "nombre": "Cable HDMI 2.1 2m",
        "sku": "CBL-HD-005",
        "ubicacion": "D-01-03",
        "cantidad": 5,
        "stockMinimo": 20,
        "stockMaximo": 100,
        "estado": "stock_critico",
        "ultimaActualizacion": "2026-03-03",
        "categoria": "Cables",
        "proveedor": "Genérico"
    },
    {
        "id": 6,
        "nombre": "Adaptador USB-C a HDMI",
        "sku": "ADP-UC-006",
        "ubicacion": "D-02-01",
        "cantidad": 12,
        "stockMinimo": 10,
        "stockMaximo": 25,
        "estado": "stock_bajo",
        "ultimaActualizacion": "2026-03-05",
        "categoria": "Accesorios",
        "proveedor": "Anker"
    }
]

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

    
@app.get("/materiales")
def obtener_materiales():
    return materiales


@app.get("/materiales/{id}")
def obtener_material(id: int):

    for material in materiales:
        if material["id"] == id:
            return material

    return {"mensaje": "Material no encontrado"}


@app.post("/materiales")
def agregar_material(material: Material):

    nuevo = material.dict()
    nuevo["id"] = len(materiales) + 1

    materiales.append(nuevo)

    return nuevo


@app.put("/materiales/{id}")
def actualizar_material(id: int, material_actualizado: Material):

    for material in materiales:
        if material["id"] == id:
            material["nombre"] = material_actualizado.nombre
            material["cantidad"] = material_actualizado.cantidad
            return material

    return {"mensaje": "Material no encontrado"}


@app.delete("/materiales/{id}")
def eliminar_material(id: int):

    for material in materiales:
        if material["id"] == id:
            materiales.remove(material)
            return {"mensaje": "Material eliminado"}

    return {"error": "Material no encontrado"}

    