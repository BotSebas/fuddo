# Implementación de Campo "Costo de Producto"

## Resumen de Cambios

Se ha agregado el campo **"Costo de Producto"** a toda la aplicación, permitiendo registrar y gestionar el costo de cada producto para análisis de ganancias.

## Archivos Modificados

### 1. Base de Datos
- **Archivo**: `sql/agregar_costo_producto.sql` (NUEVO)
  - Script para agregar la columna `costo_producto` (DECIMAL 10,2) a la tabla `productos`
  - Se inserta después del campo `nombre_producto`

### 2. Backend (PHP)

#### `productos/productos.php`
- Actualizado SELECT para incluir `costo_producto`
- Agregado campo de costo en la tabla de visualización (después del nombre)
- Actualizado modal con campo input para `costo_producto`
- Actualizado función `abrirModal()` para cargar el costo al editar
- Actualizado colspan en tabla para reflejar nueva columna
- Actualizado ejemplo en modal de carga masiva

#### `productos/procesar.php`
- Agregada lectura y validación de `costo_producto` (flotante)
- Actualizado INSERT para incluir `costo_producto`
- Actualizado UPDATE para modificar `costo_producto` al editar

#### `productos/carga_masiva.php`
- Actualizado para leer 6 columnas (antes eran 5):
  1. Nombre del Producto
  2. **Costo Producto** (nuevo)
  3. Valor sin IVA
  4. Valor con IVA
  5. Inventario
  6. Mínimo Inventario (opcional)
- Actualizado bind_param de prepared statement para incluir costo

## Pasos para Implementar

### Paso 1: Ejecutar Script SQL
```sql
ALTER TABLE `productos` ADD COLUMN `costo_producto` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `nombre_producto`;
```

Usa phpMyAdmin o tu cliente MySQL para ejecutar este comando.

### Paso 2: Verificar Archivos
Los siguientes archivos están listos para usar:
- ✅ `productos/productos.php` - Vista actualizada
- ✅ `productos/procesar.php` - Backend actualizado
- ✅ `productos/carga_masiva.php` - Carga masiva actualizada

## Características Nuevas

### En el Formulario Modal
- Campo "Costo Producto" con input numérico (2 decimales)
- Posicionado justo después del nombre del producto
- Valor por defecto: 0.00
- Es requerido para crear/editar productos

### En la Tabla de Productos
- Nueva columna "Costo Producto" visible después del nombre
- Formato monetario ($X.XX)
- Actualizable al editar el producto

### En Carga Masiva
- Ahora aceptan 6 columnas en CSV (antes 5)
- Nuevo ejemplo: `Pizza Margarita,5000,8000,9500,50,5`
  - Nombre: Pizza Margarita
  - Costo: 5000
  - Valor sin IVA: 8000
  - Valor con IVA: 9500
  - Inventario: 50
  - Mínimo: 5

## Validaciones

- El costo acepta hasta 2 decimales
- El costo no puede ser negativo (min="0")
- Si no se especifica, el costo será 0.00

## Testing Recomendado

1. Ejecutar script SQL ✅
2. Crear un nuevo producto con costo
3. Editar un producto existente y agregar costo
4. Verificar que aparezca en la tabla
5. Probar carga masiva con archivo CSV de 6 columnas
6. Verificar que datos se guarden correctamente en BD

---
**Fecha**: 5 de Febrero, 2026
**Estado**: Implementación Completa
