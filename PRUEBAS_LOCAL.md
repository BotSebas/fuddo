# 🧪 GUÍA DE PRUEBAS LOCAL - MODO CLOUDWAYS

## ✅ Instalación Completada

La base de datos `mgacgdnjkg` ha sido creada exitosamente con todas las tablas maestras.

### Credenciales de Acceso

**Base de Datos:**
- Nombre: `mgacgdnjkg`
- Usuario: `root` (local)
- Password: (vacío)

**Super-Admin:**
- Usuario: `admin`
- Password: `admin123`

**URL de Acceso:**
- http://localhost/fuddo/login.php

---

## 📝 PLAN DE PRUEBAS

### ✅ PASO 1: Login Super-Admin

1. Acceder a http://localhost/fuddo/login.php
2. Ingresar credenciales:
   - Usuario: `admin`
   - Password: `admin123`
3. Verificar que redirige a `home.php`

**Resultado Esperado:**
- Login exitoso
- Dashboard de super-admin visible
- Menú lateral con opciones de administración

---

### ✅ PASO 2: Crear Restaurante de Prueba

1. Ir a: **Restaurantes** → **Nuevo Restaurante**
2. Completar formulario:
   ```
   Nombre: Restaurante Demo
   Identificador: demo
   Contacto: Juan Pérez
   Email: demo@fuddo.com
   Teléfono: 3001234567
   ```
3. Hacer clic en **Guardar**

**Resultado Esperado:**
- Mensaje de éxito: "Restaurante creado exitosamente"
- En phpMyAdmin (http://localhost/phpmyadmin), verificar que se crearon:
  - `fuddo_demo_mesas`
  - `fuddo_demo_productos`
  - `fuddo_demo_servicios`
  - `fuddo_demo_servicios_total`

**Verificación en Base de Datos:**
```sql
USE mgacgdnjkg;
SHOW TABLES LIKE 'fuddo_demo%';
```

**Debe mostrar:**
```
+----------------------------------+
| Tables_in_mgacgdnjkg             |
+----------------------------------+
| fuddo_demo_mesas                 |
| fuddo_demo_productos             |
| fuddo_demo_servicios             |
| fuddo_demo_servicios_total       |
+----------------------------------+
```

---

### ✅ PASO 3: Crear Usuario para el Restaurante

1. Ir a: **Usuarios** → **Nuevo Usuario**
2. Completar formulario:
   ```
   Nombre: María García
   Usuario: maria
   Email: maria@demo.com
   Password: 123456
   Rol: Administrador
   Restaurante: Restaurante Demo
   ```
3. Hacer clic en **Guardar**

**Resultado Esperado:**
- Usuario creado exitosamente
- Asociado al restaurante "Restaurante Demo"

---

### ✅ PASO 4: Asignar Permisos al Restaurante

1. Ir a: **Permisos** → **Aplicaciones**
2. Seleccionar "Restaurante Demo"
3. Marcar todas las aplicaciones:
   - ✅ Mesas
   - ✅ Productos
   - ✅ Cocina
   - ✅ Reportes
   - ✅ Pedidos
4. Hacer clic en **Guardar Permisos**

5. Ir a: **Permisos** → **Reportes**
6. Seleccionar "Restaurante Demo"
7. Marcar todos los reportes:
   - ✅ Cierre de Caja
   - ✅ Inventario Valorizado
8. Hacer clic en **Guardar Permisos**

**Resultado Esperado:**
- Permisos guardados exitosamente

---

### ✅ PASO 5: Dar Soporte al Restaurante (Super-Admin)

1. Como super-admin, hacer clic en el ícono de **herramientas** (🔧) en el navbar
2. Se abre modal "Soporte a Restaurantes"
3. Seleccionar "Restaurante Demo" del dropdown
4. Hacer clic en **Iniciar Soporte**

**Resultado Esperado:**
- Mensaje: "Conectado al restaurante: Restaurante Demo"
- El menú lateral ahora muestra: Mesas, Productos, Cocina, Reportes
- El navbar indica: "Modo Soporte: Restaurante Demo"

---

### ✅ PASO 6: Crear Productos

1. Ir a: **Productos** → **Nuevo Producto**
2. Crear varios productos:

**Producto 1:**
```
ID Producto: (auto-generado PR-1)
Nombre: Hamburguesa Clásica
Valor sin IVA: 10000
Valor con IVA: 11900
Inventario: 50
```

**Producto 2:**
```
ID Producto: (auto-generado PR-2)
Nombre: Papas Fritas
Valor sin IVA: 5000
Valor con IVA: 5950
Inventario: 100
```

**Producto 3:**
```
ID Producto: (auto-generado PR-3)
Nombre: Coca Cola
Valor sin IVA: 3000
Valor con IVA: 3570
Inventario: 200
```

**Resultado Esperado:**
- 3 productos creados exitosamente

**Verificación en BD:**
```sql
SELECT * FROM fuddo_demo_productos;
```

---

### ✅ PASO 7: Crear Mesas

1. Ir a: **Mesas** → **Nueva Mesa**
2. Crear varias mesas:

**Mesa 1:**
```
ID Mesa: MSA-1
Nombre: Mesa 1
Ubicación: Zona Principal
Estado: Libre
```

**Mesa 2:**
```
ID Mesa: MSA-2
Nombre: Mesa 2
Ubicación: Zona Principal
Estado: Libre
```

**Mesa 3:**
```
ID Mesa: MSA-3
Nombre: Mesa 3
Ubicación: Terraza
Estado: Libre
```

**Resultado Esperado:**
- 3 mesas creadas y visibles en el módulo Mesas

**Verificación en BD:**
```sql
SELECT * FROM fuddo_demo_mesas;
```

---

### ✅ PASO 8: Tomar Pedido en Mesa

1. En el módulo **Mesas**, hacer clic en **Mesa 1**
2. Se abre modal "Servicios de Mesa 1"
3. Hacer clic en **Agregar Producto**
4. Seleccionar "Hamburguesa Clásica"
5. Cantidad: 2
6. Hacer clic en **Agregar**

7. Repetir para agregar:
   - Papas Fritas x 2
   - Coca Cola x 2

**Resultado Esperado:**
- Productos agregados exitosamente
- Mesa 1 ahora muestra estado "Ocupada"
- Total de cuenta visible: $43,040 (2 hamburguesas + 2 papas + 2 cocacolas)

**Verificación en BD:**
```sql
SELECT * FROM fuddo_demo_servicios WHERE id_mesa = 'MSA-1';
```

---

### ✅ PASO 9: Ver Pedido en Cocina

1. Ir a: **Cocina**
2. Verificar que aparece el pedido de Mesa 1

**Resultado Esperado:**
- Card de "Mesa 1" visible
- Productos listados:
  - Hamburguesa Clásica x2
  - Papas Fritas x2
  - Coca Cola x2
- Botón "Marcar como Listo" disponible

---

### ✅ PASO 10: Finalizar Cuenta (Cerrar Mesa)

1. Volver a **Mesas**
2. Hacer clic en **Mesa 1**
3. Verificar productos y total
4. Seleccionar **Método de Pago**: Efectivo
5. Ingresar **Con cuánto paga**: 50000
6. Hacer clic en **Finalizar Cuenta**

**Resultado Esperado:**
- Mensaje: "Cuenta cerrada exitosamente"
- Mesa 1 vuelve a estado "Libre"
- Inventario descontado:
  - Hamburguesas: 50 → 48
  - Papas: 100 → 98
  - Coca Cola: 200 → 198

**Verificación en BD:**
```sql
-- Servicios finalizados
SELECT * FROM fuddo_demo_servicios WHERE estado = 'finalizado';

-- Servicios_total con pago
SELECT * FROM fuddo_demo_servicios_total;

-- Inventario actualizado
SELECT nombre_producto, inventario FROM fuddo_demo_productos;
```

---

### ✅ PASO 11: Generar Reporte de Cierre de Caja

1. Ir a: **Reportes** → **Cierre de Caja**
2. Seleccionar:
   - Período: **Día**
   - Fecha: Hoy
3. Hacer clic en **Generar Reporte**

**Resultado Esperado:**
- Reporte muestra:
  - Número de Servicios: 1
  - Total Ventas: $43,040
  - Ticket Promedio: $43,040
  - Desglose por método de pago: Efectivo $43,040
  - Productos vendidos con cantidades

4. Hacer clic en **Exportar a Excel**

**Resultado Esperado:**
- Descarga archivo `cierre_caja_[fecha].xls`
- Archivo abre correctamente en Excel

---

### ✅ PASO 12: Generar Reporte de Inventario

1. Ir a: **Reportes** → **Inventario Valorizado**
2. Ver tabla de productos

**Resultado Esperado:**
- Productos listados con:
  - Stock actual (descontado)
  - Valor total
  - Estado del stock
  - Alertas si aplican

3. Hacer clic en **Exportar a Excel**

**Resultado Esperado:**
- Descarga archivo `inventario_valorizado_[fecha].xls`

---

### ✅ PASO 13: Cerrar Soporte (Super-Admin)

1. Hacer clic en **Salir de Soporte** (en el navbar)

**Resultado Esperado:**
- Mensaje: "Has salido del modo soporte"
- Menú lateral vuelve a mostrar solo opciones de super-admin
- Ya no puede acceder a Mesas, Productos, etc.

---

### ✅ PASO 14: Login con Usuario del Restaurante

1. Cerrar sesión
2. Login con:
   - Usuario: `maria`
   - Password: `123456`

**Resultado Esperado:**
- Login exitoso
- Dashboard muestra solo opciones del restaurante
- Puede acceder a: Mesas, Productos, Cocina, Reportes
- NO puede acceder a: Restaurantes, Usuarios, Permisos

---

### ✅ PASO 15: Crear Segundo Restaurante (Aislamiento)

1. Login como super-admin (admin/admin123)
2. Crear otro restaurante:
   ```
   Nombre: Pizzería Italia
   Identificador: pizzaitalia
   ```
3. Dar soporte a "Pizzería Italia"
4. Crear productos y mesas diferentes

**Resultado Esperado:**
- Se crean tablas: `fuddo_pizzaitalia_*`
- Al cambiar de restaurante en modo soporte, solo ve datos de ese restaurante
- Datos completamente aislados entre restaurantes

**Verificación de Aislamiento:**
```sql
-- Ver todas las tablas con prefijos
SHOW TABLES LIKE 'fuddo_%';

-- Debe mostrar:
-- fuddo_demo_*
-- fuddo_pizzaitalia_*
```

---

## ✅ CHECKLIST DE VALIDACIÓN

- [ ] Login super-admin funciona
- [ ] Crear restaurante genera tablas con prefijo
- [ ] Crear usuario y asignar a restaurante
- [ ] Asignar permisos (aplicaciones y reportes)
- [ ] Modo soporte cambia sesión correctamente
- [ ] Crear productos actualiza tabla con prefijo
- [ ] Crear mesas actualiza tabla con prefijo
- [ ] Agregar productos a mesa (servicios)
- [ ] Mesa cambia de estado libre → ocupada
- [ ] Cocina muestra pedidos activos
- [ ] Finalizar cuenta descuenta inventario
- [ ] Servicios_total guarda método de pago
- [ ] Reporte cierre de caja muestra datos correctos
- [ ] Exportar a Excel funciona
- [ ] Reporte inventario muestra stock actualizado
- [ ] Salir de soporte restaura sesión
- [ ] Login con usuario de restaurante funciona
- [ ] Usuario de restaurante no ve opciones de admin
- [ ] Segundo restaurante tiene tablas separadas
- [ ] Aislamiento de datos entre restaurantes

---

## 🐛 PROBLEMAS COMUNES

### Error: "Tabla no encontrada"
**Causa:** TABLE_PREFIX no está generándose
**Solución:** Verificar que `$_SESSION['identificador']` existe

```php
// Debug en cualquier página
var_dump($_SESSION['identificador']);
var_dump(TABLE_PREFIX);
var_dump(TBL_MESAS);
```

### Error: "Access denied"
**Causa:** Conexión usando credenciales incorrectas
**Solución:** Verificar que está usando `root` para local

### Mesa no cambia de estado
**Causa:** Query no está usando TBL_MESAS
**Solución:** Verificar que todas las queries usan constantes

---

## 📊 VERIFICACIÓN EN BASE DE DATOS

```sql
-- Conectar a BD
USE mgacgdnjkg;

-- Ver todas las tablas
SHOW TABLES;

-- Ver estructura de tabla con prefijo
DESCRIBE fuddo_demo_mesas;

-- Ver datos de servicios activos
SELECT * FROM fuddo_demo_servicios WHERE estado = 'activo';

-- Ver totales de ventas
SELECT * FROM fuddo_demo_servicios_total;

-- Ver inventario actual
SELECT nombre_producto, inventario, valor_con_iva 
FROM fuddo_demo_productos 
ORDER BY inventario ASC;
```

---

## 🎯 PRÓXIMOS PASOS DESPUÉS DE PRUEBAS

Una vez validado todo localmente:

1. ✅ Sistema funciona con arquitectura de prefijos
2. ✅ Datos aislados entre restaurantes
3. ✅ Reportes generan correctamente
4. ✅ Inventario se descuenta correctamente
5. ✅ Permisos funcionan

**Entonces estamos listos para:**
- 🚀 Desplegar en Cloudways
- 📤 Subir archivos por SFTP
- 🗄️ Importar SQL en servidor remoto
- 🌐 Configurar dominio
- 🔒 Cambiar passwords de producción

---

**¡Sistema listo para pruebas locales!** 🎉
