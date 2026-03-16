# Testing Checklist - CSV Import de Materias Primas

## ✅ Verificación de Funcionalidad

### 1. Interfaz de Usuario
- [ ] El botón "Importar CSV" aparece en materias_primas.php
- [ ] El botón solo es visible para super-admin
- [ ] El botón solo es visible para usuarios con permiso 'productos'
- [ ] Al hacer clic, abre el modal #modalImportarCSV
- [ ] El modal tiene campo de entrada de archivo (accept=".csv")

### 2. Vista Previa del CSV
- [ ] El botón "Ver Previa" funciona
- [ ] Muestra tabla con encabezados del CSV
- [ ] Muestra hasta 10 primeras filas de datos
- [ ] Valida columnas requeridas
- [ ] Muestra advertencias si hay errores
- [ ] Permite cambiar archivo si hay errores
- [ ] Cambia el botón a "Importar" después de previa correcta

### 3. Validación de Archivo
- [ ] Rechaza archivos > 5MB
- [ ] Rechaza archivos que no sean CSV
- [ ] Rechaza archivos sin encabezado
- [ ] Valida que existan todas 5 columnas requeridas
- [ ] Valida IDs de materia prima no vacíos
- [ ] Valida nombres no vacíos
- [ ] Valida unidades de medida
- [ ] Valida cantidades > 0
- [ ] Valida costos >= 0

### 4. Importación de Datos
- [ ] Los registros nuevos se insertan correctamente
- [ ] Los registros duplicados (mismo ID) se actualizan
- [ ] El sistema muestra contador de registros insertados
- [ ] El sistema muestra contador de registros actualizados
- [ ] El sistema muestra contador de errores
- [ ] Después de importar exitoso, redirige a materias_primas.php

### 5. Descarga de Plantilla
- [ ] El enlace "Descargar plantilla" funciona
- [ ] Descarga archivo CSV válido
- [ ] El archivo tiene codificación UTF-8
- [ ] El archivo contiene 15 ejemplos de prueba
- [ ] El archivo tiene encabezado correcto

### 6. Permisos y Seguridad
- [ ] Super-admin puede acceder
- [ ] Usuario con app 'productos' puede acceder
- [ ] Usuario sin app 'productos' NO puede acceder
- [ ] Los datos se escapan correctamente (prepared statements)
- [ ] No hay vulnerabilidades SQL injection

---

## 🧪 Casos de Prueba

### Test 1: Importación Exitosa - Nuevos Registros
**Objetivo**: Verificar que registros nuevos se insertan

1. Descarga plantilla
2. Guarda como `test_nuevos.csv`
3. Sube el archivo
4. Haz clic en "Ver Previa"
5. Verifica que muestre tabla correcta
6. Haz clic en "Importar"
7. Verifica mensaje de éxito
8. Revisa en materias_primas.php que estén los nuevos registros

**Resultado Esperado**: ✅ Todos los registros se insertan, estado = "activo"

---

### Test 2: Importación Exitosa - Actualización de Registros Existentes
**Objetivo**: Verificar que registros duplicados se actualizan

1. Importa el archivo de prueba 1 (ver Test 1)
2. Crea un nuevo CSV con IDs iguales pero nombres y precios diferentes:
   ```csv
   id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
   CARNE_MOLIDA_001,CARNE ACTUALIZADA,kg,15.0,350.00
   ```
3. Importa este archivo
4. Verifica en materias_primas.php que el nombre y precio fueron actualizados
5. Verifica que NO hay duplicados

**Resultado Esperado**: ✅ Registro actualizado, sin duplicados

---

### Test 3: Error - Archivo CSV Inválido
**Objetivo**: Verificar rechazo de archivos no CSV

1. Crea un archivo `test.txt` o `test.xlsx`
2. Intenta subirlo en el formulario
3. Sistema debe mostrar error

**Resultado Esperado**: ❌ Error: "El archivo debe ser formato CSV"

---

### Test 4: Error - Columnas Faltantes
**Objetivo**: Verificar rechazo de CSV sin todas las columnas

1. Crea CSV con solo 3 columnas (falta unidad_medida y costo_total_base):
   ```csv
   id_materia_prima,nombre,cantidad_base_comprada
   TEST_001,Test,10
   ```
2. Intenta importar
3. Sistema debe mostrar error

**Resultado Esperado**: ❌ Error: "El CSV no tiene todas las columnas requeridas"

---

### Test 5: Error - Datos Inválidos en Vista Previa
**Objetivo**: Verificar detección de errores en los datos

1. Crea CSV con errores:
   ```csv
   id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
   ,Nombre Sin ID,kg,10,100
   VALIDO_001,Nombre Válido,kg,-5,100
   VALIDO_002,Nombre,INCORRECTO,10,100
   VALIDO_003,Nombre,kg,10,invalido
   ```
2. Haz clic en "Ver Previa"
3. Debe mostrar advertencias

**Resultado Esperado**: ⚠️ Muestra advertencias de filas con problemas

---

### Test 6: Archivo CSV Muy Grande
**Objetivo**: Verificar límite de tamaño

1. Crea un CSV de prueba > 5MB
2. Intenta subirlo
3. Sistema debe rechazar

**Resultado Esperado**: ❌ Error: "El archivo supera 5MB"

---

### Test 7: Permisos - Usuario sin Permiso
**Objetivo**: Verificar que solo usuarios autorizados pueden acceder

1. Inicia sesión con usuario que NO tiene app 'productos'
2. Ve a materias_primas.php
3. El botón "Importar CSV" NO debe aparecer

**Resultado Esperado**: 🚫 Botón NO visible, sin acceso

---

### Test 8: Permisos - Super Admin
**Objetivo**: Verificar que super-admin siempre tiene acceso

1. Inicia sesión como super-admin
2. Ve a materias_primas.php
3. El botón "Importar CSV" DEBE aparecer

**Resultado Esperado**: ✅ Botón visible, puede importar

---

### Test 9: Caracteres Especiales
**Objetivo**: Verificar que ñ, á, é, etc., se importan correctamente

1. Crea CSV con caracteres especiales:
   ```csv
   id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
   CHOCO_001,Chocolate Español,kg,2.5,45.50
   JAMON_001,Jamón Serrano Ibérico,kg,1.0,85.00
   QUESO_001,Queso Oaxaca,kg,2.0,55.00
   ```
2. Importa
3. Verifica en la BD que se guardaron correctamente

**Resultado Esperado**: ✅ Caracteres especiales se guardan sin corrupción

---

### Test 10: Cálculos de Costo Unitario
**Objetivo**: Verificar que se calculan correctamente los costos

1. Importa este CSV:
   ```csv
   id_materia_prima,nombre,unidad_medida,cantidad_base_comprada,costo_total_base
   PRUEBA_CALC_001,Prueba Cálculo,kg,5.0,100.00
   ```
2. Ver en tabla de materias primas:
   - Cantidad en unidad mínima debe ser 5000g
   - Costo por unidad mínima debe ser 0.02 (100 / 5000g)

**Resultado Esperado**: ✅ Cálculos correctos

---

## 📊 Estadísticas Después de Testing

| Test | Resultado | Notas |
|------|-----------|-------|
| Test 1 | ✅/❌ | |
| Test 2 | ✅/❌ | |
| Test 3 | ✅/❌ | |
| Test 4 | ✅/❌ | |
| Test 5 | ✅/❌ | |
| Test 6 | ✅/❌ | |
| Test 7 | ✅/❌ | |
| Test 8 | ✅/❌ | |
| Test 9 | ✅/❌ | |
| Test 10 | ✅/❌ | |

---

## 🐛 Bugs Encontrados

```
1. [FECHA]: Descripción del bug
   - Paso para reproducir:
   - Error esperado:
   - Resultado actual:
```

---

## 📝 Notas Adicionales

- Las funciones de cálculo de conversión (`convertirAUnidadMinima`, `calcularCostoUnitarioMinimo`) ya existen en `includes/funciones_conversiones.php`
- La validación de permisos usa `tienePermiso('productos')` que ya está implementada
- El prepared statement previene SQL injection
- El ON DUPLICATE KEY UPDATE permite actualizar registros existentes

---

**Fecha de Testing**: ___________  
**Usuario de Test**: ___________  
**Resultado General**: ✅ APROBADO / ❌ FALLA  

