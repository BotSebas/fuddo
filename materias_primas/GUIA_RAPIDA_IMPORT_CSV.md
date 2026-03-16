# 📥 Guía Rápida - Importar Materias Primas CSV

## ⚡ 3 Pasos Rápidos

### Paso 1: Ir a Materias Primas
- Inicia sesión en FUDDO
- Ve a **Materias Primas**
- Verás un botón azul **"Importar CSV"**

### Paso 2: Descargar Plantilla (Opcional pero Recomendado)
- Haz clic en el botón **"Importar CSV"**
- En el modal, haz clic en **"Descargar plantilla de ejemplo"**
- Se descargará `plantilla_materias_primas_[FECHA].csv`

### Paso 3: Seleccionar y Subir Archivo
1. Abre el archivo CSV descargado en Excel
2. Modifica los nombres, cantidades y precios según tu restaurante
3. **IMPORTANTE**: No cambies los encabezados ni agregues/elimines columnas
4. Guarda el archivo
5. En FUDDO, haz clic en "Importar CSV" → Selecciona tu archivo
6. Haz clic en **"Ver Previa"** para revisar
7. Si todo está bien, haz clic en **"Importar"**

---

## 📊 Archivo CSV - Estructura

Tu archivo DEBE tener EXACTAMENTE estas 4 columnas (SIN id_materia_prima):

```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
```

### Ejemplo Válido:
```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
Carne Molida,kg,10.5,250.50
Pechuga de Pollo,kg,5.0,75.00
Leche Entera,l,10.0,45.00
Huevos,und,30.0,12.00
```

**IMPORTANTE**: El sistem automáticamente asignará IDs como MP-1, MP-2, MP-3, etc.

---

## ✅ Reglas Importantes

| Campo | Regla |
|-------|-------|
| `nombre` | No puede estar vacío |
| `unidad_medida` | Debe ser: **kg**, **g**, **lb**, **l**, **ml**, o **und** |
| `cantidad_base_comprada` | Debe ser número positivo (ej: 10.5) |
| `costo_total_base` | Debe ser número (ej: 250.50) |

**IMPORTANTE**: NO incluyas una columna `id_materia_prima` - el sistema la genera automáticamente

---

## 🎯 Unidades de Medida Válidas

```
kg  = Kilogramo
g   = Gramo
lb  = Libra
l   = Litro
ml  = Mililitro
und = Unidad individual (huevos, tomates, etc)
```

---

## 📱 Usando Excel

1. Descarga la plantilla
2. Abre en Excel
3. Rellena tus datos
4. Presiona Ctrl+Shift+S (Guardar Como)
5. Elige formato **CSV (delimitado por comas)** 
6. Haz clic en **"Usar CSV"** cuando pregunte sobre formato
7. Guarda el archivo

> **Tip**: En LibreOffice Calc es similar - Archivo → Guardar Como → CSV

---

## ⚠️ Errores Comunes

### ❌ "El archivo debe ser formato CSV"
- **Causa**: Guardaste como .xlsx o .xls
- **Solución**: Guarda como CSV (delimitado por comas)

### ❌ "El CSV no tiene todas las columnas requeridas"
- **Causa**: Falta una columna o los nombres están mal escritos
- **Solución**: Descarga la plantilla y copia el encabezado

### ❌ "Unidad inválida"
- **Causa**: Escribiste "Kg" en lugar de "kg" o "kilogramo" en lugar de "kg"
- **Solución**: Usa exactamente: kg, g, lb, l, ml, o und

### ❌ "Cantidad debe ser número positivo"
- **Causa**: Dejaste el campo vacío o escribiste un número negativo
- **Solución**: Coloca un número > 0 (ej: 10.5)

---

## 🔍 Antes de Importar

El sistema mostrará una **vista previa** con:
- ✅ Tabla de tus datos
- ⚠️ Lista de errores encontrados (si los hay)

**REVISA BIEN** antes de hacer clic en "Importar"

---

## ✨ Después de Importar

El sistema te dirá:
- ✅ Cuantos registros se insertaron (nuevos)
- ⚠️ Cuantos se actualizaron (que ya existían)
- ❌ Cuantos errores hubo

Si todo fue bien:
- ✓ Se recargará la página
- ✓ Verás tus nuevas materias primas en la lista

---

## 📝 Columnas del CSV Explicadas

### `nombre` (Nombre) ⭐
- Nombre descriptivo para el usuario
- Ejemplo: `Carne de Res Molida`, `Pechuga de Pollo`
- Puede tener caracteres especiales: ñ, á, é, etc
- NO puede estar vacío

### `unidad_medida` (Unidad) ⭐
- Cómo se compra generalmente
- Debe ser: kg, g, lb, l, ml, o und
- El sistema calcula automáticamente el costo por unidad mínima

### `cantidad_base_comprada` (Cantidad) ⭐
- Cantidad de la última compra
- Ejemplo: 10.5 kg, 30 unidades, 5 litros
- Número decimal con punto (10.5, NO 10,5)
- Debe ser > 0

### `costo_total_base` (Costo Total) ⭐
- Costo total de esa compra
- Ejemplo: 250.50, 75.00, 45.00
- El sistema calcula automáticamente el costo por unidad

### ID Automático 🤖
- **NO** incluyas `id_materia_prima` en tu CSV
- El sistema genera automáticamente: MP-1, MP-2, MP-3, etc.
- Los IDs se asignan en orden secuencial
- Cada importación continúa desde el número anterior

---

## 🎓 Ejemplo Paso a Paso

### Paso 1: Tu archivo CSV se ve así:
```csv
id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
```

### Paso 2: Completas con tus datos:
```csv
id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
CARNE_001,Carne Molida,kg,10.5,250.50
POLLO_001,Pollo Fresco,kg,5,75
```

### Paso 3: Guardas como CSV en Excel

### Paso 4: En FUDDO, subes el archivo

### Paso 5: Haces clic "Ver Previa" y ves tus datos

### Paso 6: Haces clic "Importar"

### ✅ ¡Listo! Tus materias primas están importadas

---

## 💡 Tips

1. **Empieza pequeño**: Importa 5-10 items primero para probar
2. **IDs descriptivos**: Usa `CARNE_MOLIDA_001` no solo `001`
3. **Actualizar precios**: Puedes importar el mismo archivo cada mes con precios actualizados
4. **Caracteres especiales**: Güevos, Jamón, Cebolla - todo funciona
5. **Comas vs Puntos**: Usa PUNTOS para decimales (10.5 no 10,5)

---

## ❓ Preguntas Frecuentes

**P: ¿Puedo importar 1000 items?**
A: Sí, máximo 5MB. Funciona bien hasta ~2000 items.

**P: ¿Qué pasa si el ID ya existe?**
A: Se actualiza con los nuevos datos (nombre, precio, cantida).

**P: ¿Puedo borrar items con CSV?**
A: No, el CSV solo crea/actualiza. Para borrar, usa el botón de eliminar.

**P: ¿Dónde descargo la plantilla?**
A: En el modal de importación, haz clic en "Descargar plantilla de ejemplo"

**P: ¿Cuál es el tamaño máximo del archivo?**
A: 5 MB. Divide si es más grande.

**P: ¿Cuáles son las unidades válidas?**
A: kg, g, lb, l, ml, und

---

## 📚 Más Información

Para documentación completa:
- Lee `FORMATO_CSV_IMPORTACION.md` (en la carpeta materias_primas)
- Lee `IMPLEMENTACION_CSV_IMPORT.md` (para administradores)
- Lee `TESTING_CSV_IMPORT.md` (para QA/Testing)

---

## 🆘 Soporte

Si tienes problemas:
1. Descarga la plantilla y úsala como referencia
2. Verifica que las unidades sean exactas (kg, g, lb, l, ml, o und)
3. Revisa que NO haya espacios extras en las columnas
4. Contacta al administrador si persiste el problema

---

**¡A importar!** 🚀

