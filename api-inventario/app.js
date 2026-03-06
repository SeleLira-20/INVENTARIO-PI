const express = require("express");
const cors = require("cors");

const app = express();

app.use(cors());
app.use(express.json());

let materiales = [
 { id: 1, nombre: "Tornillos", cantidad: 100 },
 { id: 2, nombre: "Cables", cantidad: 50 }
];

app.get("/materiales", (req, res) => {
 res.json(materiales);
});

app.post("/materiales", (req, res) => {

 const nuevo = {
  id: materiales.length + 1,
  nombre: req.body.nombre,
  cantidad: req.body.cantidad
 };

 materiales.push(nuevo);

 res.json(nuevo);
});

app.delete("/materiales/:id", (req, res) => {

 materiales = materiales.filter(m => m.id != req.params.id);

 res.json({mensaje:"Material eliminado"});
});

app.listen(3000, () => {
 console.log("API corriendo en puerto 3000");
});