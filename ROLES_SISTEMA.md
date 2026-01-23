# Sistema de Roles - FUDDO

## Roles Disponibles en el Sistema

El sistema FUDDO utiliza **únicamente 2 roles** que se almacenan en la tabla `usuarios_master` de la base de datos maestra `fuddo_master`:

### 1. **super-admin** (Super Administrador)
- **Descripción**: Administrador de toda la plataforma FUDDO
- **Permisos**:
  - ✅ Gestionar todos los restaurantes
  - ✅ Crear nuevos restaurantes
  - ✅ Gestionar TODOS los usuarios del sistema
  - ✅ Cambiar estados de usuarios
  - ✅ Eliminar usuarios
  - ✅ Ver y modificar cualquier restaurante
  - ✅ Acceso completo a la base de datos maestra

### 2. **admin-restaurante** (Administrador de Restaurante)
- **Descripción**: Administrador de UN restaurante específico
- **Permisos**:
  - ✅ Gestionar su propio restaurante
  - ✅ Gestionar productos de su restaurante
  - ✅ Gestionar mesas de su restaurante
  - ✅ Ver servicios y ventas de su restaurante
  - ✅ Gestionar inventario
  - ❌ NO puede crear restaurantes
  - ❌ NO puede gestionar usuarios del sistema
  - ❌ NO puede acceder a otros restaurantes

## Arquitectura de Almacenamiento

### Base de Datos Maestra (`fuddo_master`)
Contiene:
- Tabla `restaurantes`: Todos los restaurantes en la plataforma
- Tabla `usuarios_master`: Todos los usuarios con sus roles (`super-admin` o `admin-restaurante`)

**NO existe una tabla de usuarios local en cada restaurante**. Todos los usuarios y contraseñas se almacenan centralizadamente en `fuddo_master`.

### Bases de Datos de Restaurantes
Cada restaurante tiene su propia base de datos (ej: `fuddo_restaurante1`) que contiene:
- Productos
- Mesas
- Servicios
- Inventario
- **NO contiene usuarios ni contraseñas**

## Cambios Realizados

### Archivos Modificados:

1. **validar.php**
   - ❌ Eliminada conversión `super-admin` → `fuddo-admin`
   - ✅ Ahora usa directamente el rol de la BD maestra

2. **usuarios/usuarios.php**
   - ✅ Formulario actualizado: solo 2 opciones de rol
   - ✅ Verificación cambiada a `super-admin`
   - ✅ Badges actualizados para mostrar solo los 2 roles

3. **usuarios/procesar.php**
   - ✅ Validación de roles: solo acepta `admin-restaurante` y `super-admin`
   - ✅ Verificación de permisos cambiada a `super-admin`

4. **usuarios/eliminar.php**
   - ✅ Solo usuarios con rol `super-admin` pueden eliminar

5. **usuarios/cambiar_estado.php**
   - ✅ Solo usuarios con rol `super-admin` pueden cambiar estados

6. **includes/menu.php**
   - ✅ Menú de Usuarios visible solo para `super-admin`
   - ✅ Menú de Restaurantes visible solo para `super-admin`

## Uso en Código

### Verificar si es Super Admin:
```php
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin') {
    // Código para super-admin
}
```

### Verificar si es Admin de Restaurante:
```php
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin-restaurante') {
    // Código para admin de restaurante
}
```

## Notas Importantes

- ⚠️ **NO usar** roles como `admin`, `cajero`, `mesero`, `fuddo-admin` - estos NO EXISTEN en la base de datos
- ✅ Solo usar: `super-admin` y `admin-restaurante`
- ✅ Todos los usuarios se almacenan en `fuddo_master.usuarios_master`
- ✅ Las contraseñas se almacenan hasheadas con `password_hash()` de PHP
