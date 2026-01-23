# Instrucciones para permitir Super Admin sin Restaurante

## Paso 1: Ejecutar el script SQL

Ejecuta el siguiente archivo SQL en tu base de datos `fuddo_master`:

**Archivo**: `sql/05_permitir_superadmin_sin_restaurante.sql`

Puedes hacerlo de 2 formas:

### Opción A: Desde phpMyAdmin
1. Abre phpMyAdmin
2. Selecciona la base de datos `fuddo_master`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido del archivo `sql/05_permitir_superadmin_sin_restaurante.sql`
5. Haz clic en "Continuar"

### Opción B: Desde línea de comandos
```bash
mysql -u root -p fuddo_master < sql/05_permitir_superadmin_sin_restaurante.sql
```

## Paso 2: (Opcional) Actualizar tu usuario super-admin

Si quieres que tu usuario super-admin NO esté asociado a ningún restaurante, ejecuta:

```sql
USE fuddo_master;
UPDATE usuarios_master SET id_restaurante = NULL WHERE rol = 'super-admin';
```

## Paso 3: ¡Listo!

Ahora puedes:
- ✅ Ingresar con tu usuario super-admin sin necesidad de tener un restaurante asociado
- ✅ Crear usuarios super-admin sin asignarles restaurante
- ✅ Gestionar todos los restaurantes desde el panel de administración

## Cambios Realizados en el Código:

1. **validar.php**
   - Cambio de `INNER JOIN` a `LEFT JOIN` para permitir usuarios sin restaurante
   - Solo valida restaurante si el rol NO es super-admin
   - Usa valores NULL para id_restaurante, nombre_bd y nombre_restaurante

2. **usuarios/procesar.php**
   - El campo restaurante solo es obligatorio para admin-restaurante
   - Super-admin puede crearse sin restaurante

3. **usuarios/usuarios.php**
   - Campo restaurante ya no es obligatorio
   - JavaScript dinámico que muestra/oculta el requerimiento según el rol

4. **sql/01_crear_bd_maestra.sql**
   - `id_restaurante` ahora permite NULL
   - Foreign key con `ON DELETE SET NULL`

## Verificación

Para verificar que funciona:
1. Cierra sesión
2. Intenta iniciar sesión con tu usuario super-admin
3. Deberías poder ingresar sin problemas aunque no haya restaurantes
