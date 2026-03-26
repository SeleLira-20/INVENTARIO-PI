from fastapi import HTTPException, Depends, APIRouter, status
from app.models.usuario import Material
from app.data.database import materiales
from app.security.auth import verificar_Peticion

router = APIRouter(
    prefix="/materiales",
    tags=["Inventario"]
)

@router.get("/")
def obtener_materiales():
    return materiales

@router.get("/{id}")
def obtener_material(id: int):
    for material in materiales:
        if material["id"] == id:
            return material

    raise HTTPException(status_code=404, detail="Material no encontrado")

@router.post("/", status_code=status.HTTP_201_CREATED)
def agregar_material(material: Material):
    nuevo = material.dict()
    nuevo["id"] = len(materiales) + 1
    materiales.append(nuevo)
    return nuevo

@router.put("/{id}")
def actualizar_material(id: int, material_actualizado: Material):
    for material in materiales:
        if material["id"] == id:
            material["nombre"] = material_actualizado.nombre
            material["sku"] = material_actualizado.sku
            material["ubicacion"] = material_actualizado.ubicacion
            material["cantidad"] = material_actualizado.cantidad
            material["stockMinimo"] = material_actualizado.stockMinimo
            material["stockMaximo"] = material_actualizado.stockMaximo
            material["estado"] = material_actualizado.estado
            material["ultimaActualizacion"] = material_actualizado.ultimaActualizacion
            material["categoria"] = material_actualizado.categoria
            material["proveedor"] = material_actualizado.proveedor
            return material

    raise HTTPException(status_code=404, detail="Material no encontrado")

@router.delete("/{id}")
def eliminar_material(id: int, userAuth: str = Depends(verificar_Peticion)):
    for i, material in enumerate(materiales):
        if material["id"] == id:
            materiales.pop(i)
            return {"mensaje": f"Material eliminado por: {userAuth}"}

    raise HTTPException(status_code=404, detail="Material no encontrado")