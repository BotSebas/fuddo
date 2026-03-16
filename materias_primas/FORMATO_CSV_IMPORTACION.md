# Guía de Importación CSV - Materias Primas

## 📋 Descripción General

El sistema permite importar múltiples materias primas (raw materials/ingredientes) mediante un archivo CSV. Esta funcionalidad está disponible solo para:
- **Super administrador** del sistema
- Usuarios con permiso de la aplicación **"Productos"**

---

## 📊 Estructura del Archivo CSV

### Separadores Soportados

El sistema **detecta automáticamente** el separador del CSV. Puedes usar:

- **Coma (,)** - Estándar internacional
  ```csv
  nombre,unidad_medida,cantidad_base_comprada,costo_total_base
  Carne de Res,kg,10.5,250.50
  ```

- **Punto y Coma (;)** - Común en Europa y América Latina
  ```csv
  nombre;unidad_medida;cantidad_base_comprada;costo_total_base
  Carne de Res;kg;10,5;250,50
  ```

El sistema automáticamente sabrá cuál usar.

### Columnas Requeridas

El archivo CSV **DEBE** contener exactamente estas 4 columnas (en cualquier orden):

| Columna | Tipo | Descripción | Ejemplo |
|---------|------|-------------|---------|
| `nombre` | Texto | Nombre descriptivo de la materia prima | `Carne de Res Molida` |
| `unidad_medida` | Texto | Unidad en que se compra (ver tabla de unidades) | `kg` |
| `cantidad_base_comprada` | Número | Cantidad comprada en la última compra | `10.5` |
| `costo_total_base` | Número | Costo total de esa compra | `250.50` |

> ⚠️ **IMPORTANTE**: 
> - Las columnas pueden estar en cualquier orden, pero todas deben estar presentes
> - **NO incluyas** la columna `id_materia_prima` - el sistema la genera automáticamente como MP-1, MP-2, etc.
> - Solo se requieren estas 4 columnas

---

## 📏 Unidades de Medida Válidas

Los valores en la columna `unidad_medida` DEBEN ser uno de estos (case-insensitive):

```
kg    = Kilogramo
g     = Gramo
lb    = Libra
l     = Litro
ml    = Mililitro
und   = Unidad (para items individuales: huevos, tomates, etc)
```

**Ejemplo**:
```csv
id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
HUEVOS_001,Huevos Frescos,und,30,12.00
LECHE_001,Leche Entera,l,10,45.00
```

---

## 📝 Formato del Archivo

### Requisitos Técnicos

1. **Tipo de archivo**: `.csv` (separador de comas)
2. **Codificación**: UTF-8 (para caracteres especiales como ñ, á, é, etc)
3. **Tamaño máximo**: 5 MB
4. **Primera línea**: DEBE ser el encabezado con los nombres de las columnas
5. **Datos a partir de la línea 2**

### Primera Línea (Encabezado)

```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
```

---

## ✅ Validación de Datos

El sistema realiza validaciones automáticas:

| Campo | Validación |
|-------|-----------|
| `id_materia_prima` | No puede estar vacío, no puede repetirse en el archivo |
| `nombre` | No puede estar vacío |
| `unidad_medida` | Debe ser uno de: kg, g, lb, l, ml, und |
| `cantidad_base_comprada` | Debe ser un número positivo (> 0) |
| `costo_total_base` | Debe ser un número no negativo (≥ 0) |

---

## 📥 Ejemplos Completos

### Ejemplo 1: Restaurante (Ingredientes Básicos)

```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
Carne de Res Molida,kg,10.5,250.50
Pechuga de Pollo,kg,5,75.00
Tomate Fresco,kg,2,8.50
Cebolla Blanca,kg,3,9.00
Papa Blanca,kg,15,22.50
Aceite de Oliva,l,5,85.00
Leche Entera,l,10,45.00
Queso Mozzarella,kg,2.5,125.00
Harina de Trigo,kg,5,18.00
Sal Común,kg,1,2.50
```

### Ejemplo 2: Minimarket + Panadería

```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
Café Tostado,kg,2,32.00
Azúcar Blanca,kg,5,8.50
Harina Panificación,kg,10,22.00
Mantequilla,kg,1.5,28.50
Huevos Frescos,und,60,24.00
Leche Descremada,l,5,18.00
Levadura Seca,g,500,45.00
Chocolate Polvo,kg,2,35.00
```

### Ejemplo 3: Bebidas (Con decimales)

```csv
nombre,unidad_medida,cantidad_base_comprada,costo_total_base
Jugo de Naranja,l,10,25.50
Agua Mineral,l,20,8.00
Refresco Cola,l,15,18.75
Jugo de Limón,ml,500,12.50
Lemonada Concentrada,l,3.5,14.25
```

---

## 🛠️ Cómo Crear el Archivo

### Opción 1: Usando Excel o LibreOffice Calc

1. Abre Excel, Calc o Google Sheets
2. En la fila 1, escribe los encabezados:
   - A1: `id_materia_prima`
   - B1: `nombre`
   - C1: `unidad_medida`
   - D1: `cantidad_base_comprada`
   - E1: `costo_total_base`
3. Completa los datos a partir de la fila 2
4. **Guarda como**: Archivo → Guardar Como → Formato: **CSV (delimitado por comas)**
5. Asegúrate de seleccionar **UTF-8** como codificación

### Opción 2: Usando el Bloc de Notas o Editor de Texto

1. Abre un editor de texto (Bloc de Notas, VS Code, Notepad++, etc)
2. Copia y pega el siguiente contenido, remplazando los datos:

```
id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
INGREDIENTE_001,Nombre del Ingrediente,kg,cantidad,costo
```

3. Reemplaza con tus datos
4. Guarda como: `mi_archivo.csv`

### Opción 3: Descarga la Plantilla del Sistema

1. En la ventana de importación, hace clic en "**Descargar plantilla de ejemplo**"
2. Se descargará un archivo `plantilla_materias_primas.csv`
3. Ábrelo en Excel/Calc y completa con tus datos
4. Guarda e importa

---

## ⚠️ Validaciones y Errores Comunes

### Error: "El archivo CMN debe ser formato CSV"
- Asegúrate de guardar con extensión `.csv`
- No guardes como `.xls`, `.xlsx` o `.ods`

### Error: "El CSV no tiene todas las columnas requeridas"
- Verifica que existan todas 4 columnas: `nombre, unidad_medida, cantidad_base_comprada, costo_total_base`
- Las columnas pueden estar en otro orden, pero todas deben estar presentes
- **NO incluyas** la columna `id_materia_prima` - el sistema la genera automáticamente

### Error: "El archivo supera 5MB"
- Divide tu importación en múltiples archivos más pequeños
- Máximo recomendado: 1000-1500 materias primas por archivo

### Error: "Unidad inválida"
- Verifica que las unidades sean exactamente: `kg, g, lb, l, ml, und`
- No uses variantes como "Kg", "KG", "kilogramo", etc

### Error: "Cantidad debe ser número positivo"
- No dejes campos vacíos en cantidad
- No uses comas como separador decimal (usa puntos: `10.5` no `10,5`)

---

## 🔄 Comportamiento de la Importación

### Materias Primas Nuevas
- Se insertan directamente en el sistema con ID automático (MP-1, MP-2, etc.)
- Quedan en estado **"activo"**
- Cada fila del CSV genera un nuevo ID secuencial

### Orden de IDs
- El primer registro importado recibe el siguiente número disponible (MP-N+1)
- Si importas 3 veces, los IDs serán: MP-1, MP-2, MP-3, MP-4, etc.
- Los IDs nunca se reutilizan

---

## 📋 Vista Previa Antes de Importar

Antes de confirmar la importación, el sistema muestra:
1. **Vista previa de hasta 10 primeras filas** del archivo
2. **Advertencias** sobre errores en los datos
3. **Contador** de filas con problemas

Puedes **revisar y corregir** antes de hacer clic en "Importar"

---

## 🎯 Resumen de Importación

Después de importar, el sistema muestra:
- ✅ Cantidad de registros **insertados** (nuevos)
- ❌ Cantidad de **errores** encontrados (por qué no se importaron)

Todos los registros insertados tendrán IDs automáticos (MP-1, MP-2, MP-3, etc.)

---

## 🔒 Consideraciones de Seguridad

- ✅ Solo usuarios autorizados pueden importar
- ✅ Validación estricta de todos los datos
- ✅ Los IDs se escapan para prevenir inyecciones SQL
- ✅ Transacciones seguras con prepared statements
- ✅ Máximo 5MB de archivo para evitar sobrecarga

---

## 📞 Soporte

Si encuentras problemas:
1. Revisa que el formato sea exacto según esta guía
2. Verifica la codificación UTF-8
3. Descarga la plantilla del sistema y úsala como referencia
4. Contacta al administrador si persisten los problemas

---

## ✨ Tips y Mejores Prácticas

1. **Usa IDs descriptivos**: `CARNE_MOLIDA_001` es mejor que `ING_001`
2. **Nombres claros**: `Carne de Res Molida (10kg)` es más útil que `Carne`
3. **Actualiza precios regularmente**: Importa archivos cada mes para mantener costos actualizados
4. **Prueba primero**: Crea un pequeño CSV de prueba con 5-10 items
5. **Respaldo**: Guarda una copia de tu CSV original como respaldo

---

**Última actualización**: 2024  
**Versión**: 1.0
