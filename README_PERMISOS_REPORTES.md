# Sistema de Permisos de Reportes - FUDDO

## Descripción General

El sistema de permisos de reportes permite controlar de manera granular qué reportes puede visualizar cada restaurante. Esto es especialmente útil cuando se ofrecen reportes personalizados según las necesidades de cada cliente.

## Estructura de Base de Datos

### Tabla `reportes` (en fuddo_master)

Almacena todos los reportes disponibles en el sistema.

```sql
CREATE TABLE IF NOT EXISTS reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(100) NOT NULL,
    icono VARCHAR(50),
    orden INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `id`: Identificador único del reporte
- `clave`: Clave única para identificar el reporte (ej: 'cierre_caja')
- `nombre`: Nombre descriptivo del reporte
- `descripcion`: Descripción detallada de lo que muestra el reporte
- `archivo`: Nombre del archivo PHP del reporte
- `icono`: Clase CSS del icono FontAwesome
- `orden`: Orden de visualización en el menú
- `estado`: Estado del reporte (activo/inactivo)

### Tabla `restaurante_reportes` (en fuddo_master)

Tabla de relación que asigna permisos de reportes a restaurantes.

```sql
CREATE TABLE IF NOT EXISTS restaurante_reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_reporte INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_reporte) REFERENCES reportes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurante_reporte (id_restaurante, id_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Reportes Disponibles

### 1. Cierre de Caja (`cierre_caja`)

**Archivo:** `reportes/cierre_caja.php`

**Características:**
- Filtros por día, semana o mes
- KPIs: Total ventas, número de servicios, ticket promedio/mín/máx
- Desglose por método de pago (efectivo, llave, nequi, daviplata, tarjeta)
- Desglose por día de la semana
- Desglose por hora (solo para período día)
- Top productos vendidos
- Exportable a Excel

### 2. Inventario Valorizado (`inventario_valorizado`)

**Archivo:** `reportes/inventario_valorizado.php`

**Características:**
- Valor total del inventario
- Listado de productos con precio y stock
- Alertas automáticas:
  - Sin stock (0 unidades) - Rojo
  - Stock crítico (≤5 unidades) - Amarillo
  - Stock bajo (≤10 unidades) - Azul
  - Stock normal (>10 unidades) - Verde
- Exportable a Excel

## Instalación

### 1. Ejecutar Script SQL

```bash
# Conectarse a MySQL y ejecutar:
mysql -u root -p < sql/07_crear_sistema_permisos_reportes.sql
```

O ejecutar manualmente en phpMyAdmin las sentencias del archivo `sql/07_crear_sistema_permisos_reportes.sql`.

### 2. Verificar Tablas Creadas

Confirmar que en la base de datos `fuddo_master` existen las siguientes tablas:
- `reportes`
- `restaurante_reportes`

### 3. Asignar Permisos Iniciales

Como **super-admin**, ir a:
1. Menú lateral > **Permisos Reportes**
2. Seleccionar un restaurante
3. Marcar los reportes que desea asignar
4. Hacer clic en **Guardar Permisos**

## Uso del Sistema

### Para Super-Admin

#### Gestionar Permisos

1. Acceder al módulo **Permisos Reportes** desde el menú lateral
2. Seleccionar un restaurante del dropdown
3. Marcar/desmarcar los reportes disponibles
4. Usar botones "Seleccionar Todos" o "Deseleccionar Todos" para agilizar
5. Guardar cambios

#### Ver Resumen de Permisos

En la misma página se muestra una tabla con:
- Nombre del restaurante
- Cantidad de reportes asignados vs. total disponible
- Botón para configurar rápidamente

### Para Usuarios de Restaurante

Los usuarios verán únicamente los reportes que han sido asignados a su restaurante:

1. Acceder al módulo **Reportes** desde el menú lateral
2. Solo aparecerán las tarjetas de los reportes permitidos
3. Si no tiene ningún reporte asignado, verá un mensaje informativo

## Funciones PHP

### `tieneReporte($clave)`

**Ubicación:** `includes/menu.php`

Verifica si el usuario actual tiene permiso para ver un reporte específico.

**Parámetros:**
- `$clave` (string): Clave del reporte a verificar

**Retorno:**
- `true`: Si es super-admin O si el restaurante tiene el permiso
- `false`: Si no tiene permiso

**Ejemplo de uso:**

```php
<?php if (tieneReporte('cierre_caja')): ?>
    <!-- Mostrar contenido del reporte -->
<?php endif; ?>
```

### `tienePermiso($clave)`

**Ubicación:** `includes/menu.php`

Verifica permisos de aplicaciones (mesas, productos, cocina, etc.).

## Protección de Archivos

Todos los archivos de reportes están protegidos con validación de permisos:

```php
// Verificar permiso de acceso al reporte
if (!tieneReporte('nombre_reporte')) {
    // Mostrar mensaje de acceso denegado
    include '../includes/footer.php';
    exit();
}
```

**Archivos protegidos:**
- `reportes/cierre_caja.php`
- `reportes/inventario_valorizado.php`

## Agregar Nuevos Reportes

### 1. Crear el archivo PHP del reporte

```php
<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';
include '../includes/menu.php';

// Verificar super-admin sin restaurante
if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin' && !isset($_SESSION['id_restaurante'])) {
    // Mostrar mensaje de restricción
    exit();
}

include '../includes/conexion.php';

// Verificar permiso del reporte
if (!tieneReporte('mi_nuevo_reporte')) {
    // Mostrar acceso denegado
    exit();
}

// ... Código del reporte ...
?>
```

### 2. Insertar en la base de datos

```sql
INSERT INTO reportes (clave, nombre, descripcion, archivo, icono, orden) 
VALUES (
    'mi_nuevo_reporte', 
    'Mi Nuevo Reporte', 
    'Descripción detallada del reporte',
    'mi_nuevo_reporte.php', 
    'fas fa-chart-line', 
    3
);
```

### 3. Agregar tarjeta en reportes.php

```php
<?php if (tieneReporte('mi_nuevo_reporte')): ?>
<div class="col-lg-4 col-md-6">
  <div class="card">
    <div class="card-header" style="background-color: #27ae60; color: white;">
      <h3 class="card-title">
        <i class="fas fa-chart-line"></i> Mi Nuevo Reporte
      </h3>
    </div>
    <div class="card-body">
      <p>Descripción del reporte</p>
      <a href="reportes/mi_nuevo_reporte.php" class="btn btn-success btn-block">
        <i class="fas fa-eye"></i> Ver Reporte
      </a>
    </div>
  </div>
</div>
<?php endif; ?>
```

## Variables de Sesión

El sistema utiliza las siguientes variables de sesión:

```php
$_SESSION['rol_master']      // Rol en la base de datos master
$_SESSION['rol']             // Rol actual del usuario
$_SESSION['id_restaurante']  // ID del restaurante activo
$_SESSION['usuario']         // Nombre del usuario
```

## Archivos Modificados

### Archivos Nuevos
- `sql/07_crear_sistema_permisos_reportes.sql` - Script de creación de tablas
- `permisos_reportes.php` - Interfaz de gestión de permisos
- `README_PERMISOS_REPORTES.md` - Esta documentación

### Archivos Modificados
- `includes/menu.php` - Agregada función `tieneReporte()` y carga de permisos
- `reportes.php` - Agregadas validaciones de permisos en tarjetas
- `reportes/cierre_caja.php` - Agregada validación de acceso
- `reportes/inventario_valorizado.php` - Agregada validación de acceso

## Solución de Problemas

### Usuario no ve ningún reporte

**Solución:**
1. Verificar que el restaurante tenga reportes asignados en `Permisos Reportes`
2. Confirmar que los reportes estén en estado 'activo' en la tabla `reportes`
3. Verificar que el usuario tiene permiso 'reportes' en aplicaciones

### Error al guardar permisos

**Solución:**
1. Verificar que las tablas `reportes` y `restaurante_reportes` existan
2. Confirmar que el usuario es super-admin
3. Revisar logs de PHP para errores de conexión a base de datos

### Reporte muestra "Acceso Denegado"

**Solución:**
1. Ir a `Permisos Reportes` como super-admin
2. Seleccionar el restaurante
3. Marcar el reporte correspondiente
4. Guardar cambios
5. Recargar la página del reporte

## Consideraciones de Seguridad

1. **Validación en servidor:** Todos los reportes validan permisos en el backend
2. **Super-admin bypass:** El super-admin siempre tiene acceso total
3. **Restaurante activo:** Los permisos solo se cargan si hay un restaurante activo en sesión
4. **SQL Injection:** Se usan prepared statements para todas las consultas
5. **CSRF:** El formulario de permisos usa POST para modificaciones

## Mejoras Futuras

1. **Permisos por rol:** Permitir diferentes permisos según el rol del usuario (admin, cajero, mesero)
2. **Reportes programados:** Envío automático de reportes por correo
3. **Caché de permisos:** Almacenar permisos en sesión para mejorar rendimiento
4. **Auditoría:** Registrar cambios de permisos en tabla de logs
5. **Reportes personalizados:** Constructor visual de reportes

## Soporte

Para problemas o consultas sobre el sistema de permisos de reportes, contactar al equipo de desarrollo.

---

**Versión:** 1.0  
**Fecha:** 2024  
**Autor:** Equipo FUDDO
