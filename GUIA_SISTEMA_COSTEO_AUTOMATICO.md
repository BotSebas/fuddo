# Sistema de Costeo Automático - Instalación y Uso

## Descripción General

Este módulo proporciona un sistema completo de costeo automático de productos basado en materias primas y recetas. Permite:

1. **Gestionar Materias Primas** con conversión automática de unidades
2. **Crear Recetas** con ingredientes dinámicos y cálculo automático de costos
3. **Integración automática** с productos para venta

## Características Principales

### Core del Sistema

- ✅ Conversión automática de unidades (kg, g, lb, l, ml, und)
- ✅ Cálculo preciso de costos en unidades mínimas estándar
- ✅ Manejo de decimales con 6 posiciones
- ✅ Integridad referencial completa
- ✅ Transacciones ACID para operaciones críticas

### Módulo: Materias Primas

**Localización:** `/materias_primas/`

**Características:**
- CRUD completo de materias primas
- Soporte para 6 tipos de unidades de medida
- Conversión automática a unidades estándar:
  - Peso: kg, lb → gramos (g)
  - Volumen: l → mililitros (ml)
  - Unidades: und → und
- Cálculo automático del costo por unidad mínima
- Búsqueda y filtrado por unidad
- Paginación de resultados
- Validación de dependencias antes de eliminar

**Archivos:**
- `materias_primas.php` - Interfaz principal
- `procesar.php` - Lógica CRUD y cálculos

### Módulo: Recetas

**Localización:** `/recetas/`

**Características:**
- CRUD completo de recetas
- Ingredientes dinámicos por receta
- Cálculo automático del costo total
- Creación automática de productos
- Actualización automática de costos al cambiar materias primas
- Vista de detalles con cálculos desagregados
- Gestión de notas por ingrediente

**Archivos:**
- `recetas.php` - Interfaz principal
- `procesar.php` - Lógica CRUD y sintegración con productos

### Funciones Utilitarias

**Archivo:** `includes/funciones_conversiones.php`

**Funciones disponibles:**
```php
// Conversión de unidades
convertirAUnidadMinima($cantidad, $unidad_original)
obtenerUnidadMinima($unidad_original)
calcularCostoUnitarioMinimo($costo_total, $cantidad_base, $unidad_original)

// Utilidades
obtenerDescripcionUnidad($unidad)
esUnidadValida($unidad)
obtenerUnidadesDisponibles()
agruparUnidadesPorTipo()
formatearCosto($numero, $decimales)
```

## Instalación

### 1. Aplicar Migraciones SQL

Para **nuevos restaurantes**, las tablas se crean automáticamente al crear el restaurante usando el template SQL actualizado.

Para **restaurantes existentes**, ejecutar el siguiente SQL en tu base de datos:

```sql
-- Reemplazar {PREFIX} con el prefijo real (ej: fuddo_pizzahouse_)
SOURCE /ruta/a/sql/add_costeo_automatico.sql
```

Alternativamente, ejecutar manualmente:

```sql
-- Ver archivo: sql/add_costeo_automatico.sql
```

### 2. Actualizar Conexión

La conexión ya incluye los aliases de las nuevas tablas:
- `TBL_MATERIAS_PRIMAS`
- `TBL_RECETAS`
- `TBL_RECETA_INGREDIENTES`

### 3. Otorgar Permisos

En la sección de **Permisos > Aplicaciones**:
1. Crear dos nuevas aplicaciones:
   - Nombre: `Materias Primas`, Clave: `materias_primas`
   - Nombre: `Recetas`, Clave: `recetas`

2. Asignarlas al restaurante deseado

### 4. Verificar Menú

El menú lateral mostrará "Costeo" con los submódulos:
- Materias Primas
- Recetas

## Flujo de Uso Típico

### Paso 1: Crear Materias Primas

1. Navegar a **Costeo > Materias Primas**
2. Click en "Nueva Materia Prima"
3. Completar:
   - Nombre (ej: Pollo desmenuzado)
   - Unidad de medida (ej: kg)
   - Cantidad comprada (ej: 5)
   - Costo total (ej: 100000)
4. El sistema calcula automáticamente:
   - Cantidad en g (5000 g)
   - Costo por g (20)
5. Guardar

### Paso 2: Crear Receta

1. Navegar a **Costeo > Recetas**
2. Click en "Nueva Receta"
3. Completar:
   - Nombre del platillo (ej: Pollo a la naranja)
   - Descripción (opcional)
4. Agregar ingredientes:
   - Seleccionar materia prima
   - Indicar cantidad (en unidad mínima)
   - El sistema calcula costo automáticamente
5. El costo total se auto-calcula
6. Guardar

**Resultado:** Se crea automáticamente un producto en el módulo Productos con:
- Nombre = nombre de la receta
- Costo sin IVA = costo de la receta
- Costo con IVA = costo + 19%
- Inventario inicial = 0
- Inventario mínimo = 1

### Paso 3: Gestionar Productos Creados

1. Navegar a **Productos**
2. Los productos creados desde recetas puedensera:
   - Modificar precio manualmente
   - Ajustar inventario
   - Cambiar estado

## Conversión de Unidades - Referencia

### Peso
| Original | Convertido a | Factor |
|----------|-------------|--------|
| kg | g | × 1000 |
| lb | g | × 453.592 |
| g | g | × 1 |

### Volumen
| Original | Convertido a | Factor |
|----------|-------------|--------|
| l | ml | × 1000 |
| ml | ml | × 1 |

### Unidad
| Original | Convertido a | Factor |
|----------|-------------|--------|
| und | und | × 1 |

## Ejemplos Prácticos

### Ejemplo 1: Pollo por Kilogramo

**Entrada:**
- Materia Prima: Pollo desmenuzado
- Unidad: kg
- Cantidad: 1
- Costo: $20.000

**Cálculo interno:**
- Convertir 1 kg → 1000 g
- Costo por g = $20.000 / 1000 = $20/g

### Ejemplo 2: Aceite por Litro

**Entrada:**
- Materia Prima: Aceite de oliva
- Unidad: l
- Cantidad: 1
- Costo: $15.000

**Cálculo interno:**
- Convertir 1 l → 1000 ml
- Costo por ml = $15.000 / 1000 = $15/ml

### Ejemplo 3: Receta Completa

**Nombre:** Sopa de Pollo
**Ingredientes:**
- 500g de pollo (MP-00001) @ $20/g = $10.000
- 300ml de caldo (MP-00002) @ $5/ml = $1.500
- 200g de verduras (MP-00003) @ $50/g = $10.000

**Costo total:** $21.500

**Producto automático:**
- Costo sin IVA: $21.500
- Costo con IVA (19%): $25.585

## Limitaciones y Consideraciones

1. **Unidades Mixtas:** No soporta diferentes unidades entre peso/volumen
2. **Eliminación:** No se pueden eliminar materias primas que estén en uso
3. **Recalcuso:** Los costos de recetas NO se actualizan automáticamente si cambia el precio de una materia prima (se pueden editar manualmente)
4. **IVA:** El sistema usa 19% por defecto (Colombia). Puede modificarse en el código

## Mantenimiento

### Backup de Datos

Las tablas importantes son:
- `{PREFIX}materias_primas`
- `{PREFIX}recetas`
- `{PREFIX}receta_ingredientes`

### Integridad Referencial

- Sus gn ingredientes de una receta que se elimina
- Las materias primas NO se pueden eliminar si están en uso
- Los productos se crean automáticamente con FK a productos

## Solución de Problemas

### Error: "No hay materias primas disponibles"
**Solución:** Crear al menos una materia prima primero

### Error: "La receta debe tener al menos un ingrediente"
**Solución:** Agregar al menos un ingrediente a la receta

### Costo no se actualiza
**Solución:** Editar y guardar nuevamente la receta

### Producto no se crear automáticamente
**Solución:** Verificar que el usuario tenga permiso sobre la tabla productos

## Desarrollo y Extensión

### Agregar nueva unidad

1. Actualizar `funciones_conversiones.php`:
```php
case 'nueva_unidad':
    // Implementar conversión
```

2. Actualizar select html 

3. Ejecutar migraciones

### Cambiar porcentaje de IVA

En `/recetas/procesar.php`:
```php
$porcentaje_iva = 0.19; // Cambiar valor
```

## Contacto y Soporte

Para reportar errores o sugerir mejoras, contactar al equipo de desarrollo.
