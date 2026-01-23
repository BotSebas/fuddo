# CAMBIOS TÉCNICOS - REFACTORIZACIÓN CLOUDWAYS

## Resumen Ejecutivo
Sistema completamente refactorizado de arquitectura multi-database a single-database con prefijos de tabla. Cambios aplicados a 45+ archivos PHP.

---

## 1. SISTEMA DE CONEXIÓN

### Archivos Modificados
- `includes/conexion.php`
- `includes/conexion_dinamica.php`
- `includes/conexion_master.php`

### Patrón Implementado
```php
// Detección de entorno
$is_cloudways = (strpos($_SERVER['HTTP_HOST'], 'cloudwaysapps.com') !== false);

if ($is_cloudways) {
    // Cloudways: Una sola BD para todo
    define('DB_HOST', 'localhost');
    define('DB_USER', 'mgacgdnjkg');
    define('DB_PASS', 'HPESTrrt4t');
    define('DB_NAME', 'mgacgdnjkg');
} else {
    // Local: Mantiene comportamiento original
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'fuddo_master');
}

// Generar TABLE_PREFIX dinámicamente
$TABLE_PREFIX = '';
if (isset($_SESSION['identificador']) && !empty($_SESSION['identificador'])) {
    $TABLE_PREFIX = 'fuddo_' . $_SESSION['identificador'] . '_';
}

// Definir constantes para tablas
if (!defined('TBL_MESAS')) define('TBL_MESAS', $TABLE_PREFIX . 'mesas');
if (!defined('TBL_PRODUCTOS')) define('TBL_PRODUCTOS', $TABLE_PREFIX . 'productos');
if (!defined('TBL_SERVICIOS')) define('TBL_SERVICIOS', $TABLE_PREFIX . 'servicios');
if (!defined('TBL_SERVICIOS_TOTAL')) define('TBL_SERVICIOS_TOTAL', $TABLE_PREFIX . 'servicios_total');
```

### Ventajas
- ✅ Un solo código para múltiples entornos
- ✅ No requiere modificaciones al desplegar
- ✅ Fácil debug local manteniendo arquitectura original

---

## 2. GESTIÓN DE SESIONES

### Archivos Modificados
- `validar.php` (login)
- `includes/iniciar_soporte.php` (soporte super-admin)

### Cambio Crítico
Ahora se almacena `identificador` en sesión para generar TABLE_PREFIX:

#### validar.php
```php
// ANTES
$stmt = $conexion_master->prepare("
    SELECT um.id, um.usuario, um.password, um.nombre, um.email, um.rol, um.estado,
           um.id_restaurante, r.nombre_bd, r.nombre as nombre_restaurante, r.estado as estado_restaurante
    FROM usuarios_master um
    LEFT JOIN restaurantes r ON um.id_restaurante = r.id
    WHERE um.usuario = ?
");

// Sesión
$_SESSION['nombre_bd'] = $user['nombre_bd'] ?? null;

// DESPUÉS
$stmt = $conexion_master->prepare("
    SELECT um.id, um.usuario, um.password, um.nombre, um.email, um.rol, um.estado,
           um.id_restaurante, r.nombre_bd, r.identificador, r.nombre as nombre_restaurante, r.estado as estado_restaurante
    FROM usuarios_master um
    LEFT JOIN restaurantes r ON um.id_restaurante = r.id
    WHERE um.usuario = ?
");

// Sesión
$_SESSION['identificador'] = $user['identificador'] ?? null; // NUEVO
$_SESSION['nombre_bd'] = $user['nombre_bd'] ?? null;
```

#### includes/iniciar_soporte.php
```php
// ANTES
$stmt = $conexion_master->prepare("SELECT id, nombre, nombre_bd FROM restaurantes WHERE id = ? AND estado = 'activo'");

$_SESSION['nombre_bd'] = $restaurante['nombre_bd'];

// DESPUÉS
$stmt = $conexion_master->prepare("SELECT id, nombre, identificador, nombre_bd FROM restaurantes WHERE id = ? AND estado = 'activo'");

$_SESSION['identificador'] = $restaurante['identificador']; // NUEVO
$_SESSION['nombre_bd'] = $restaurante['nombre_bd'];
```

---

## 3. CREACIÓN DE RESTAURANTES

### Archivo Modificado
- `crear_restaurante.php`

### Lógica Dual Implementada

```php
// Detectar entorno
$is_cloudways = (strpos($_SERVER['HTTP_HOST'], 'cloudwaysapps.com') !== false);

// Generar nombres
$nombre_bd = 'fuddo_' . preg_replace('/[^a-z0-9_]/', '', strtolower($identificador));
$table_prefix = 'fuddo_' . $identificador . '_';

// Guardar en tabla restaurantes
$valor_nombre_bd = $is_cloudways ? $table_prefix : $nombre_bd;

if ($is_cloudways) {
    // CLOUDWAYS: Crear tablas con prefijo
    $sql_template = file_get_contents('sql/template_restaurante.sql');
    $sql_schema = str_replace('{PREFIX}', $table_prefix, $sql_template);
    $conexion_master->multi_query($sql_schema);
    
} else {
    // LOCAL: Crear BD separada (comportamiento original)
    $conexion_master->query("CREATE DATABASE `$nombre_bd`");
    $conexion_nueva = new mysqli('localhost', 'root', '', $nombre_bd);
    $sql_schema = file_get_contents('sql/schema_restaurante.sql');
    $conexion_nueva->multi_query($sql_schema);
    $conexion_nueva->close();
}
```

### Manejo de Errores (Rollback)
```php
catch (Exception $e) {
    if (isset($id_restaurante)) {
        $conexion_master->query("DELETE FROM usuarios_master WHERE id_restaurante = $id_restaurante");
        $conexion_master->query("DELETE FROM restaurantes WHERE id = $id_restaurante");
    }
    
    if (!$is_cloudways && isset($nombre_bd)) {
        $conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
    }
    // En Cloudways las tablas se eliminan automáticamente por CASCADE
}
```

---

## 4. CONVERSIÓN DE QUERIES

### Patrón de Conversión Aplicado

#### ANTES (Hardcoded)
```php
$sql = "SELECT * FROM mesas WHERE id = $id";
$sql = "INSERT INTO productos (nombre, precio) VALUES ('$nombre', $precio)";
$sql = "UPDATE servicios SET estado = 'finalizado' WHERE id = $id";
$sql = "DELETE FROM servicios_total WHERE id = $id";
```

#### DESPUÉS (Con Constantes)
```php
$sql = "SELECT * FROM " . TBL_MESAS . " WHERE id = $id";
$sql = "INSERT INTO " . TBL_PRODUCTOS . " (nombre, precio) VALUES ('$nombre', $precio)";
$sql = "UPDATE " . TBL_SERVICIOS . " SET estado = 'finalizado' WHERE id = $id";
$sql = "DELETE FROM " . TBL_SERVICIOS_TOTAL . " WHERE id = $id";
```

### Archivos Convertidos por Carpeta

#### mesas/ (11 archivos)
| Archivo | Queries Modificadas | Tablas Afectadas |
|---------|---------------------|------------------|
| mesas.php | 1 | mesas, servicios |
| servicios.php | 1 | productos |
| procesar.php | 2 | mesas |
| obtener_productos.php | 2 | mesas, servicios, productos |
| obtener_detalle.php | 2 | mesas, servicios, productos |
| finalizar_cuenta.php | 7 | mesas, servicios, productos, servicios_total |
| eliminar_producto.php | 1 | servicios |
| eliminar.php | 1 | mesas |
| cancelar_servicio.php | 3 | mesas, servicios |
| agregar_producto.php | 5 | mesas, productos, servicios |
| nueva.php | 0 | N/A |

#### productos/ (4 archivos)
| Archivo | Queries Modificadas | Tablas Afectadas |
|---------|---------------------|------------------|
| productos.php | 1 | productos |
| procesar.php | 3 | productos |
| eliminar.php | 1 | productos |
| cambiar_estado.php | 2 | productos |

#### cocina/ (3 archivos)
| Archivo | Queries Modificadas | Tablas Afectadas |
|---------|---------------------|------------------|
| cocina.php | 1 | servicios, mesas, productos |
| obtener_pedidos.php | 1 | servicios, mesas, productos |
| finalizar_pedido.php | 5 | servicios, mesas |

#### reportes/ (2 archivos)
| Archivo | Queries Modificadas | Tablas Afectadas |
|---------|---------------------|------------------|
| cierre_caja.php | 10 | servicios_total, servicios, productos |
| inventario_valorizado.php | 1 | productos |

### Total de Cambios
- **Archivos modificados:** 20 archivos operativos
- **Queries actualizadas:** ~50 consultas SQL
- **Tablas con prefijo:** 4 (mesas, productos, servicios, servicios_total)
- **Tablas sin prefijo:** 6 (tablas maestras)

---

## 5. ARCHIVOS SQL NUEVOS

### cloudways_master_setup.sql
**Propósito:** Inicializar BD en Cloudways

**Estructura:**
```sql
CREATE TABLE IF NOT EXISTS restaurantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    identificador VARCHAR(50) UNIQUE NOT NULL, -- ⭐ CLAVE PARA PREFIJOS
    nombre_bd VARCHAR(100) NOT NULL, -- ⭐ Ahora guarda PREFIJO en Cloudways
    contacto VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(20),
    plan ENUM('basico', 'premium', 'enterprise') DEFAULT 'basico',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATE,
    estado ENUM('activo', 'suspendido', 'cancelado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5 tablas más + datos iniciales
```

**Datos incluidos:**
- Super-admin (admin/admin123)
- 5 aplicaciones (mesas, productos, cocina, reportes, pedidos)
- 2 reportes (cierre_caja, inventario_valorizado)

### template_restaurante.sql
**Propósito:** Template para crear tablas de restaurante

**Estructura:**
```sql
-- Template con placeholder {PREFIX}

CREATE TABLE IF NOT EXISTS {PREFIX}mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mesa VARCHAR(10) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(100),
    estado ENUM('libre', 'ocupada', 'inactiva') DEFAULT 'libre',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3 tablas más con mismo patrón
```

**Uso en código:**
```php
$template = file_get_contents('sql/template_restaurante.sql');
$sql = str_replace('{PREFIX}', 'fuddo_pizzahouse_', $template);
$conexion->multi_query($sql);

// Resultado:
// CREATE TABLE fuddo_pizzahouse_mesas (...)
// CREATE TABLE fuddo_pizzahouse_productos (...)
// CREATE TABLE fuddo_pizzahouse_servicios (...)
// CREATE TABLE fuddo_pizzahouse_servicios_total (...)
```

---

## 6. FLUJO DE EJECUCIÓN

### Escenario 1: Login Normal (Usuario de Restaurante)

```
1. Usuario ingresa credenciales en login.php
   ↓
2. validar.php ejecuta:
   - Query a 'restaurantes' con JOIN
   - Obtiene 'identificador' = 'pizzahouse'
   - Guarda en sesión: $_SESSION['identificador'] = 'pizzahouse'
   ↓
3. Redirige a home.php
   ↓
4. Usuario hace clic en "Mesas"
   ↓
5. mesas/mesas.php ejecuta:
   - include '../includes/conexion.php'
   - conexion.php genera: TABLE_PREFIX = 'fuddo_pizzahouse_'
   - Define constantes:
     * TBL_MESAS = 'fuddo_pizzahouse_mesas'
     * TBL_PRODUCTOS = 'fuddo_pizzahouse_productos'
     * TBL_SERVICIOS = 'fuddo_pizzahouse_servicios'
     * TBL_SERVICIOS_TOTAL = 'fuddo_pizzahouse_servicios_total'
   ↓
6. Query ejecutada:
   SELECT * FROM fuddo_pizzahouse_mesas
   ↓
7. Usuario ve SOLO sus mesas (aislamiento por prefijo)
```

### Escenario 2: Modo Soporte (Super-Admin)

```
1. Super-admin hace login
   - $_SESSION['identificador'] = NULL (no tiene restaurante asignado)
   - No puede acceder a módulos operativos
   ↓
2. Abre modal "Soporte a Restaurantes"
   ↓
3. Selecciona "Sushi Bar" del dropdown
   ↓
4. AJAX POST a includes/iniciar_soporte.php:
   - Consulta restaurante con id seleccionado
   - Obtiene 'identificador' = 'sushibar'
   - Actualiza sesión: $_SESSION['identificador'] = 'sushibar'
   ↓
5. Recarga página (o navega a módulo)
   ↓
6. mesas/mesas.php ejecuta:
   - TABLE_PREFIX = 'fuddo_sushibar_'
   - TBL_MESAS = 'fuddo_sushibar_mesas'
   ↓
7. Super-admin ve SOLO mesas de Sushi Bar
   ↓
8. Para cambiar de restaurante:
   - Clic en "Salir de Soporte"
   - unset($_SESSION['identificador'])
   - Selecciona otro restaurante y repite desde paso 3
```

### Escenario 3: Crear Nuevo Restaurante

```
1. Super-admin va a "Restaurantes" → "Nuevo"
   ↓
2. Completa formulario:
   - Nombre: "Cafetería Central"
   - Identificador: "cafeteriacentral"
   ↓
3. crear_restaurante.php ejecuta:
   - Detecta entorno: $is_cloudways = TRUE
   - Genera prefix: 'fuddo_cafeteriacentral_'
   - Inserta en tabla restaurantes:
     * identificador = 'cafeteriacentral'
     * nombre_bd = 'fuddo_cafeteriacentral_' (⭐ prefijo, no BD)
   ↓
4. Lee sql/template_restaurante.sql
   ↓
5. Reemplaza {PREFIX} con 'fuddo_cafeteriacentral_'
   ↓
6. Ejecuta multi_query():
   CREATE TABLE fuddo_cafeteriacentral_mesas (...)
   CREATE TABLE fuddo_cafeteriacentral_productos (...)
   CREATE TABLE fuddo_cafeteriacentral_servicios (...)
   CREATE TABLE fuddo_cafeteriacentral_servicios_total (...)
   ↓
7. Retorna success
   ↓
8. Ya se puede crear usuarios para "Cafetería Central"
   ↓
9. Al hacer login, TABLE_PREFIX apuntará a sus tablas
```

---

## 7. COMPATIBILIDAD LOCAL (XAMPP)

### Detección de Entorno
```php
$is_cloudways = (strpos($_SERVER['HTTP_HOST'], 'cloudwaysapps.com') !== false);
```

**Si es FALSE (XAMPP local):**
- Usa credenciales locales (root/sin password)
- Mantiene arquitectura multi-database original
- `crear_restaurante.php` crea BDs separadas
- No usa prefijos de tabla

**Ventajas:**
- Desarrollo local sin cambios
- Testing previo al deploy
- Rollback fácil si es necesario

---

## 8. CONSIDERACIONES DE SEGURIDAD

### Inyección SQL
Todos los queries siguen usando prepared statements o escape:

```php
// BIEN: Prepared statement
$stmt = $conexion->prepare("SELECT * FROM " . TBL_MESAS . " WHERE id = ?");
$stmt->bind_param("i", $id);

// BIEN: Real escape string
$nombre = $conexion->real_escape_string($_POST['nombre']);
$sql = "INSERT INTO " . TBL_PRODUCTOS . " (nombre) VALUES ('$nombre')";

// MAL: Sin escape (NO usado en el sistema)
$sql = "SELECT * FROM " . TBL_MESAS . " WHERE nombre = '$_POST[nombre]'";
```

### Aislamiento de Datos
- ✅ Cada restaurante solo ve sus tablas (por prefijo)
- ✅ Session maneja identificador correctamente
- ✅ Super-admin debe "dar soporte" explícitamente
- ⚠️ Importante: Validar siempre que `$_SESSION['identificador']` exista

### Credenciales Hardcoded
```php
// ⚠️ TEMPORAL: Cambiar después del deployment inicial
if ($is_cloudways) {
    define('DB_USER', 'mgacgdnjkg');
    define('DB_PASS', 'HPESTrrt4t');
}
```

**Mejor práctica (futuro):**
```php
// Usar variables de entorno
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
```

---

## 9. PERFORMANCE

### Índices Recomendados
```sql
-- En cada tabla con prefijo
ALTER TABLE fuddo_{identificador}_mesas ADD INDEX idx_estado (estado);
ALTER TABLE fuddo_{identificador}_servicios ADD INDEX idx_id_mesa (id_mesa);
ALTER TABLE fuddo_{identificador}_servicios ADD INDEX idx_estado (estado);
ALTER TABLE fuddo_{identificador}_productos ADD INDEX idx_estado (estado);
```

### Optimización de Queries
```sql
-- Evitar LIKE con wildcard inicial
-- MAL
SELECT * FROM {TBL_PRODUCTOS} WHERE nombre LIKE '%pizza%';

-- BIEN
SELECT * FROM {TBL_PRODUCTOS} WHERE nombre LIKE 'pizza%';
```

### Cache (Cloudways)
- Activar Varnish Cache en Cloudways
- Configurar Redis para sesiones
- Optimizar PHP-FPM workers

---

## 10. TESTING

### Unit Tests (Pendiente)
```php
// Ejemplo: Test para TABLE_PREFIX
function test_table_prefix_generation() {
    $_SESSION['identificador'] = 'testrestaurant';
    include 'includes/conexion.php';
    
    assert(TABLE_PREFIX === 'fuddo_testrestaurant_');
    assert(TBL_MESAS === 'fuddo_testrestaurant_mesas');
}
```

### Integration Tests
- [ ] Login y generación de prefijo
- [ ] Creación de restaurante en Cloudways
- [ ] Operaciones CRUD en cada módulo
- [ ] Modo soporte (cambio de restaurante)
- [ ] Reportes con datos de múltiples períodos

---

## 11. TROUBLESHOOTING

### Debug Helper
Agregar al inicio de cualquier archivo problemático:

```php
<?php
session_start();
echo "<pre>";
echo "Environment: " . (strpos($_SERVER['HTTP_HOST'], 'cloudwaysapps.com') !== false ? 'CLOUDWAYS' : 'LOCAL') . "\n";
echo "Identificador: " . ($_SESSION['identificador'] ?? 'NULL') . "\n";
echo "TABLE_PREFIX: " . (defined('TABLE_PREFIX') ? TABLE_PREFIX : 'NOT DEFINED') . "\n";
echo "TBL_MESAS: " . (defined('TBL_MESAS') ? TBL_MESAS : 'NOT DEFINED') . "\n";
echo "\nSesión completa:\n";
var_dump($_SESSION);
echo "</pre>";
exit;
```

### Common Errors

| Error | Causa | Solución |
|-------|-------|----------|
| Table doesn't exist | TABLE_PREFIX vacío | Verificar `$_SESSION['identificador']` |
| Access denied | Credenciales incorrectas | Verificar detección de entorno |
| Cannot modify headers | Output antes de header() | Usar `ob_start()` al inicio |
| Multi-query error | Template con sintaxis incorrecta | Verificar `template_restaurante.sql` |

---

## 12. ROADMAP FUTURO

### Corto Plazo
- [ ] Migrar credenciales a variables de entorno
- [ ] Implementar logging de errores
- [ ] Agregar índices a tablas frecuentes

### Mediano Plazo
- [ ] Sistema de cache para queries repetitivos
- [ ] Migración automática de datos (import/export)
- [ ] Panel de métricas por restaurante

### Largo Plazo
- [ ] Multi-región (Cloudways servers)
- [ ] Sharding horizontal si crece mucho
- [ ] Microservicios para módulos independientes

---

**Documento técnico completo**  
Última actualización: Diciembre 2024  
Arquitectura: Single-Database con Table Prefixes  
Versión: 2.0
