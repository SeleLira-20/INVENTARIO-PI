import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# 1. URL de la base de datos (viene de la variable de entorno del docker-compose)
DATABASE_URL = os.getenv("DATABASE_URL", "postgresql://admin:123456@localhost:5434/DB_inventario")

# 2. Motor de conexión
engine = create_engine(DATABASE_URL)

# 3. Fábrica de sesiones
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# 4. Base declarativa
Base = declarative_base()

# 5. Función para obtener la sesión en cada petición
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()