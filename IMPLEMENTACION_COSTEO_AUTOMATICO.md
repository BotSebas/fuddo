# SISTEMA DE COSTEO AUTOMÁTICO - RESUMEN DE IMPLEMENTACIÓN

## 📋 Descripción General

Se ha implementado un sistema completo de **costeo automático de productos** basado en materias primas y recetas. El sistema realiza conversiones automáticas de unidades y cálculos precisos de costos, incluyendo la creación automática de productos vinculados.

---

## ✅ Componentes Implementados

### 1. **Funciones de Conversión y Cálculos**
**Archivo:** `includes/funciones_conversiones.php`

#### Características:
- ✓ Conversión automática de 6 unidades de medida
- ✓ Cálculos con precisión de 6 decimales
- ✓ Validación de unidades
- ✓ Funciones de formato y utilidad

#### Unidades Soportadas:
```
Peso:   kg → g | g | lb → g
Volumen: l → ml | ml
Unidad: und → und
```

---

### 2. **Módulo de Materias Primas**
**Ubicación:** `/materias_primas/`

#### Archivos:
- `materias_primas.php` - Interfaz CRUD
- `procesar.php` - Lógica de procesamiento

#### Características:
- ✓ CRUD completo de materias primas
- ✓ Conversión automática a unidad mínima
- ✓ Cálculo automático de costo unitario
- ✓ Búsqueda y filtrado
- ✓ Paginación
- ✓ Validación de dependencias (no elimina si está en uso)
- ✓ AJAX para cálculos en tiempo real

#### Captura:
```
Nueva Materia Prima:
├─ Nombre: "Pollo desmenuzado"
├─ Unidad de medida: kg
├─ Cantidad base: 5
├─ Costo total: 100000
└─ [Automático] Costo por g: 20
```

---

### 3. **Módulo de Recetas**
**Ubicación:** `/recetas/`

#### Archivos:
- `recetas.php` - Interfaz CRUD
- `procesar.php` - Lógica y integración con productos

#### Características:
- ✓ CRUD completo de recetas
- ✓ Ingredientes dinámicos (sin límite)
- ✓ Cálculo automático de costo total
- ✓ AJAX para agregar/eliminar ingredientes
- ✓ Vista de detalles desagregada
- ✓ Creación automática de productos
- ✓ Transacciones ACID para integridad

#### Captura:
```
Nueva Receta:
├─ Nombre: "Sopa de pollo"
├─ Descripción: (opcional)
├─ Ingredientes dinámicos:
│  ├─ Materia Prima: Pollo (500g @ $20/g = $10000)
│  ├─ Materia Prima: Caldo (300ml @ $5/ml = $1500)
│  └─ Materia Prima: Verduras (200g @ $50/g = $10000)
└─ [Automático] Costo total: $21500
   [Automático] Producto: "Sopa de pollo"
   [Automático] Precio con IVA: $25585
```

---

### 4. **Tablas de Base de Datos**

#### `{PREFIX}materias_primas`
```sql
- id (PK)
- id_materia_prima (UNIQUE) - "MP-00001"
- nombre
- unidad_medida (enum)
- cantidad_base_comprada
- costo_total_base
- costo_por_unidad_minima
- unidad_minima (g, ml, und)
- cantidad_en_unidad_minima
- estado (activo/inactivo)
- fecha_creacion
- fecha_ultima_actualizacion
```

#### `{PREFIX}recetas`
```sql
- id (PK)
- id_receta (UNIQUE) - "REC-00001"
- nombre_platillo
- descripcion
- costo_total_receta (calculado)
- id_producto_asociado (FK)
- estado (activo/inactivo)
- fecha_creacion
- fecha_ultima_actualizacion
```

#### `{PREFIX}receta_ingredientes`
```sql
- id (PK)
- id_receta (FK)
- id_materia_prima (FK)
- cantidad_usada
- unidad_cantidad (g, ml, und)
- costo_ingrediente (calculado)
- orden
- nota
- fecha_creacion
```

---

### 5. **Integración de Menús**

**Archivo:** `includes/menu.php`

#### Cambios:
- ✓ Agregado ítem "Costeo" al menú principal
- ✓ Sub-menús: "Materias Primas" y "Recetas"
- ✓ Uso de permisos del sistema existente

```
Costeo (calculadora)
├─ Materias Primas (hoja)
└─ Recetas (plato)
```

---

### 6. **Actualización de Conexión**

**Archivo:** `includes/conexion.php`

#### Cambios:
- ✓ Agregados 3 aliases de tablas:
  - `TBL_MATERIAS_PRIMAS`
  - `TBL_RECETAS`
  - `TBL_RECETA_INGREDIENTES`

---

### 7. **Actualización de Templates SQL**

**Archivo:** `sql/template_restaurante.sql`

#### Cambios:
- ✓ Agregadas 3 tablas completas para nuevos restaurantes
- ✓ Integridad referencial con FOREIGN KEYS
- ✓ Índices para optimización

---

### 8. **Scripts de Migración**

#### `sql/add_costeo_automatico.sql`
- Script para aplicar a bases de datos existentes
- Uso de `IF NOT EXISTS` para evitar errores
- Exportable por phpMyAdmin

#### `sql/migracion_costeo_existentes.sql`
- Versión con formato de reemplazo
- Fácil de ejecutar manualmente

#### `admin/migrar_costeo.php`
- Script PHP CLI para migración automática
- Opción AJAX para aplicar desde la Web
- Procesamiento por restaurante

---

## 🔄 Flujo de Procesos

### A. Crear Materia Prima

```
Usuario → Ingresa datos → Validación
  ↓
Conversión automática (unidad → g/ml/und)
  ↓
Cálculo costo unitario (costo ÷ cantidad_mínima)
  ↓
Almacenamiento (6 decimales)
  ↓
Confirmación
```

### B. Crear Receta

```
Usuario → Ingresa datos → Validación
  ↓
Para cada ingrediente:
  ├─ Obtiene costo unitario de materia prima
  ├─ Calcula costo (cantidad × costo unitario)
  └─ Suma al total
  ↓
Transacción ACID:
  ├─ Inserta receta
  ├─ Inserta ingredientes
  └─ Crear producto automáticamente
  ↓
Confirmación
```

---

## 📊 Ejemplos de Cálculos

### Ejemplo 1: Pollo por Kilogramo
```
ENTRADA:
  Nombre: Pollo desmenuzado
  Unidad: kg
  Cantidad: 1
  Costo: $20.000

CONVERSIÓN INTERNA:
  1 kg = 1000 g

COSTO UNITARIO:
  $20.000 ÷ 1000 g = $20/g

USO EN RECETAS:
  500 g de pollo = 500 × $20 = $10.000
```

### Ejemplo 2: Aceite por Litro
```
ENTRADA:
  Nombre: Aceite de oliva
  Unidad: l
  Cantidad: 1
  Costo: $15.000

CONVERSIÓN INTERNA:
  1 l = 1000 ml

COSTO UNITARIO:
  $15.000 ÷ 1000 ml = $15/ml

USO EN RECETAS:
  300 ml de aceite = 300 × $15 = $4.500
```

### Ejemplo 3: Receta Completa
```
RECETA: Sopa de pollo

INGREDIENTES:
  Pollo (MP-00001):     500g @ $20/g = $10.000
  Caldo (MP-00002):     300ml @ $5/ml = $1.500
  Verduras (MP-00003):  200g @ $50/g = $10.000
                        SUBTOTAL = $21.500

IVA (19%):                        $4.085
TOTAL CON IVA:                   $25.585

PRODUCTO AUTOMÁTICO:
  ID: PR-1 (o siguiente)
  Nombre: "Sopa de pollo"
  Costo sin IVA: $21.500
  Precio con IVA: $25.585
  Inventario inicial: 0
  Inventario mínimo: 1
  Estado: activo
```

---

## 🔐 Seguridad e Integridad

### Validaciones Implementadas:
- ✓ Validación de sesión en cada módulo
- ✓ Control de permisos por aplicación
- ✓ Escape de datos (SQL injection prevention)
- ✓ Validación de tipos (int, float, enum)
- ✓ Transacciones ACID para operaciones críticas

### Restricciones de Base de Datos:
- ✓ FOREIGN KEYS con ON DELETE CASCADE
- ✓ UNIQUE constraints en IDs
- ✓ Índices para búsquedas rápidas
- ✓ Enums para valores limitados

### Restricciones de Negocio:
- ✓ No se puede eliminar materia prima si está en uso
- ✓ No se puede crear receta sin ingredientes
- ✓ No se puede crear materia prima sin valores válidos
- ✓ Los costos no permiten valores negativos

---

## 📂 Estructura de Archivos

```
fuddo/
├── includes/
│   ├── conexion.php                    [ACTUALIZADO]
│   ├── menu.php                        [ACTUALIZADO]
│   └── funciones_conversiones.php      [NUEVO]
│
├── materias_primas/                    [NUEVO]
│   ├── materias_primas.php
│   └── procesar.php
│
├── recetas/                            [NUEVO]
│   ├── recetas.php
│   └── procesar.php
│
├── sql/
│   ├── template_restaurante.sql        [ACTUALIZADO]
│   ├── add_costeo_automatico.sql       [NUEVO]
│   └── migracion_costeo_existentes.sql [NUEVO]
│
├── admin/
│   └── migrar_costeo.php               [NUEVO]
│
└── [NUEVA DOCUMENTACIÓN]
    ├── GUIA_SISTEMA_COSTEO_AUTOMATICO.md
    ├── INSTALACION_COSTEO_RAPIDA.md
    └── IMPLEMENTACION_COSTEO_AUTOMATICO.md
```

---

## 🚀 Instrucciones de Instalación

### Para Restaurantes NUEVOS:
Automático al crear el restaurante (usa `sql/template_restaurante.sql`)

### Para Restaurantes EXISTENTES:

**Opción 1: phpMyAdmin**
1. Abrir phpMyAdmin
2. Seleccionar base de datos `mgacgdnjkg`
3. Tab SQL
4. Ejecutar contenido de `sql/add_costeo_automatico.sql`
5. Reemplazar `{PREFIX}` con el identificador real

**Opción 2: CLI MySQL**
```bash
mysql -u root mgacgdnjkg < sql/add_costeo_automatico.sql
```

**Opción 3: PHP CLI**
```bash
php admin/migrar_costeo.php
```

### Después de SQL:
1. Otorgar permisos en Administración > Permisos > Aplicaciones
2. Crear "Materias Primas" (clave: `materias_primas`)
3. Crear "Recetas" (clave: `recetas`)
4. Asignar al restaurante

---

## 🎯 Capacidades del Sistema

### ✅ Completadas:
- [x] Conversión automática de 6 unidades
- [x] Cálculo de costo unitario con 6 decimales
- [x] CRUD de materias primas
- [x] CRUD de recetas con ingredientes dinámicos
- [x] Cálculo automático de costo total
- [x] Creación automática de productos
- [x] Vinculación receta ↔ producto
- [x] Búsqueda y filtrado
- [x] Paginación
- [x] Validación de dependencias
- [x] Interfaz responsiva con Bootstrap
- [x] AJAX para operaciones sin recarga
- [x] Transacciones ACID
- [x] Permisos y seguridad
- [x] Documentación completa
- [x] Scripts de migración

---

## 📝 Notas Importantes

### Características NOT Implementadas (Por Diseño):
- ❌ Auto-actualización de costos de recetas cuando cambia materia prima (se hace manualmente editando)
- ❌ Historial de precios (se puede agregar después)
- ❌ Recalcular todos los costos automáticamente (requiere procesamiento batch)

### Configuraciones que se pueden Personalizar:
- IVA (actualmente 19%) en `/recetas/procesar.php` línea ~180
- Número de decimales para costos (actualmente 6) en funciones_conversiones.php
- Formato de IDs (actualmente "MP-0000X", "REC-0000X", "PR-X")

---

## 🧪 Testing Recomendado

### Tests Manuales:
1. Crear materia prima con cada unidad (kg, g, lb, l, ml, und)
2. Crear receta con múltiples ingredientes
3. Editar receta (agregar/eliminar ingredientes)
4. Verificar producto creado automáticamente
5. Modificar materia prima y verificar costo
6. Intentar eliminar materia prima en uso (debe fallar)

### Tests de Integridad:
1. Verificar conversiones correctas (ej: 1kg = 1000g)
2. Verificar cálculos de costo (ej: 500 × $20 = $10000)
3. Verificar IVA (ej: $21500 × 1.19 = $25585)

---

## 📞 Soporte y Mantenimiento

### Contacto:
- Para bugs: Reportar con pasos para reproducir
- Para mejoras: Sugerir e incluir caso de uso
- Para migraciones: Usar script `migrar_costeo.php`

### Backup Recomendado:
Hacer backup de tablas:
- `{PREFIX}materias_primas`
- `{PREFIX}recetas`
- `{PREFIX}receta_ingredientes`

### Limpieza (si es necesario):
```sql
-- Eliminar todo el sistema
DROP TABLE {PREFIX}receta_ingredientes;
DROP TABLE {PREFIX}recetas;
DROP TABLE {PREFIX}materias_primas;

-- Los productos creados quedan en la tabla productos
```

---

## 📅 Versión y Fecha

- **Versión:** 1.0
- **Fecha de Implementación:** Febrero 2026
- **Estado:** Producción

---

## 📋 Checklist de Despliegue

- [ ] SQL ejecutado en base de datos
- [ ] Permisos otorgados a aplicaciones
- [ ] Menú visible en sidebar
- [ ] Crear materia prima de prueba
- [ ] Crear receta de prueba
- [ ] Verificar producto creado
- [ ] Documentación revisada
- [ ] Usuario capacitado

---

**Fin de documento de implementación.**

Para más información, consultar:
- `GUIA_SISTEMA_COSTEO_AUTOMATICO.md` - Documentación completa
- `INSTALACION_COSTEO_RAPIDA.md` - Guía de instalación rápida
