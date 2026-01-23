# GUÍA RÁPIDA: Cómo Usar el Sistema de Permisos de Reportes

## ¿Qué es?

El sistema de permisos de reportes te permite controlar exactamente qué reportes puede ver cada restaurante. Esto es útil cuando:
- Algunos restaurantes necesitan reportes personalizados
- Quieres ofrecer paquetes premium con reportes avanzados
- No todos los clientes necesitan todos los reportes

## Paso a Paso para Asignar Permisos

### 1. Acceder al Módulo

Como **Super-Admin**:
1. Inicia sesión en FUDDO
2. En el menú lateral, busca la sección **ADMINISTRACIÓN**
3. Haz clic en **Permisos Reportes**

### 2. Seleccionar el Restaurante

1. En la parte superior verás un dropdown "Seleccionar Restaurante"
2. Busca y selecciona el restaurante que deseas configurar
3. La página se recargará mostrando los reportes disponibles

### 3. Asignar Reportes

Verás una lista de reportes disponibles con switches (interruptores):

- **Cierre de Caja**: Reporte completo de ventas con desglose por método de pago, horarios y productos
- **Inventario Valorizado**: Control de stock con valorización y alertas automáticas

Para cada reporte:
- **ON (verde)** = El restaurante PUEDE ver este reporte
- **OFF (gris)** = El restaurante NO puede ver este reporte

### 4. Guardar Cambios

1. Marca o desmarca los reportes según necesites
2. Puedes usar los botones:
   - **Seleccionar Todos**: Activa todos los reportes
   - **Deseleccionar Todos**: Desactiva todos los reportes
3. Haz clic en **Guardar Permisos**
4. Verás un mensaje verde de confirmación

## Ver el Resumen

En la parte inferior de la página hay una tabla que muestra:
- Nombre de cada restaurante
- Cantidad de reportes asignados (ej: 2/2 = todos asignados)
- Botón "Configurar" para editar rápidamente

## ¿Qué Verá el Usuario del Restaurante?

Cuando un usuario del restaurante entre a la sección **Reportes**:

### Si tiene reportes asignados:
- Verá solo las tarjetas de los reportes que le asignaste
- Podrá hacer clic y ver cada reporte normalmente
- Podrá exportar a Excel

### Si NO tiene reportes asignados:
- Verá un mensaje: "Sin reportes asignados"
- Se le indicará que contacte al administrador

### Si intenta acceder directamente a un reporte sin permiso:
- Verá un mensaje "Acceso Denegado"
- No podrá ver la información del reporte

## Ejemplos de Uso

### Ejemplo 1: Restaurante Premium
```
Restaurante: "La Casa del Chef"
Reportes asignados:
✓ Cierre de Caja
✓ Inventario Valorizado
```
Este restaurante podrá ver ambos reportes completos.

### Ejemplo 2: Restaurante Básico
```
Restaurante: "Comidas Rápidas Express"
Reportes asignados:
✓ Cierre de Caja
✗ Inventario Valorizado
```
Este restaurante solo verá el reporte de Cierre de Caja.

### Ejemplo 3: Cliente de Prueba
```
Restaurante: "Demo Restaurant"
Reportes asignados:
✗ Cierre de Caja
✗ Inventario Valorizado
```
Este restaurante no verá ningún reporte detallado, solo el resumen básico.

## Preguntas Frecuentes

### ¿El Super-Admin necesita permisos asignados?

**No.** El Super-Admin siempre tiene acceso a todos los reportes de todos los restaurantes, sin importar la configuración de permisos.

### ¿Puedo cambiar los permisos en cualquier momento?

**Sí.** Los cambios son inmediatos. Solo necesitas:
1. Ir a Permisos Reportes
2. Cambiar los switches
3. Guardar
4. El usuario verá los cambios la próxima vez que entre

### ¿Qué pasa si creo un nuevo reporte personalizado?

Los nuevos reportes se agregan a la tabla `reportes` y aparecerán automáticamente en el módulo de permisos. Podrás asignarlos a los restaurantes que desees.

### ¿Los permisos afectan al resumen de ventas?

**No.** El resumen de ventas básico (que ya existía en reportes.php) siempre está visible. Solo se controlan los reportes detallados nuevos.

## Consejos

1. **Asigna todos los reportes inicialmente** a restaurantes actuales para que no pierdan funcionalidad
2. **Usa el botón "Seleccionar Todos"** para configuración rápida de nuevos restaurantes
3. **Revisa el resumen** periódicamente para ver qué restaurantes tienen qué reportes
4. **Documenta** qué reportes incluye cada paquete de servicio que ofrezcas

## Soporte Técnico

Si tienes dudas o problemas:
1. Verifica que ejecutaste el script SQL de instalación
2. Confirma que las tablas `reportes` y `restaurante_reportes` existen en `fuddo_master`
3. Revisa que el usuario tenga rol de super-admin

---

**Nota:** Esta funcionalidad permite monetizar reportes personalizados y ofrecer diferentes niveles de servicio a tus clientes.
