from fastapi import FastAPI
from app.routers.materiales import router as materiales_router

app = FastAPI(
    title="CRUD de gestión de inventario"
)

app.include_router(materiales_router)

