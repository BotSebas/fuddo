# Sistema de Costeo Automático - Instalación Rápida

## Resumen

Sistema completo para gestionar materias primas, crear recetas con cálculo automático de costos y generar productos vinculados.

## Instalación en 3 Pasos

### 1. Aplicar Migraciones (Restaurantes Existentes)

Para restaurantes que ya existen, ejecutar las tablas SQL:

**Opción A: phpMyAdmin**
1. Abrir phpMyAdmin
2. Seleccionar base de datos `mgacgdnjkg`
3. Tab "SQL"
4. Copiar contenido de `sql/add_costeo_automatico.sql`
5. Reemplazar `{PREFIX}` con el prefijo real (ej: `fuddo_pizzahouse_`)
6. Ejecutar

**Opción B: Línea de comandos**
```bash
mysql -u root mgacgdnjkg < sql/add_costeo_automatico.sql
```

**Opción C: Con PHP (CLI)**
```bash
php admin/migrar_costeo.php
```

---

**Para restaurantes NUEVOS:** No requieren migración, se crean automáticamente.

### 2. Otorgar Permisos

1. Ir a **Inicio > Usuarios > Super Admin**
2. Ir a **Permisos > Aplicaciones**
3. Crear dos nuevas aplicaciones:

| Nombre | Clave | Descripción |
|--------|-------|-------------|
| Materias Primas | `materias_primas` | Gestión de materias primas |
| Recetas | `recetas` | Gestión de recetas |

4. Asignarlas al restaurante en la columna "Acciones"

### 3. Usar los Módulos

El menú lateral mostrará **Costeo** con dos opciones:
- **Materias Primas** - Gestionar ingredientes
- **Recetas** - Crear platillos

---

## Uso Rápido

### Crear una Materia Prima

```
Costeo > Materias Primas > Nueva Materia Prima
├─ Nombre: "Pollo deshilado"
├─ Unidad: kg
├─ Cantidad: 5
├─ Costo: 100000
└─ Guardar
```

**Resultado automático:**
- 5000 g = $100000
- Costo por g = $20

### Crear una Receta

```
Costeo > Recetas > Nueva Receta
├─ Nombre: "Sopa de pollo"
├─ Ingredientes:
│  ├─ Pollo: 500g ($10000)
│  ├─ Caldo: 300ml ($1500)
│  └─ Verduras: 200g ($10000)
└─ Guardar
```

**Resultado automático:**
- Costo total: $21500
- Se crea producto: "Sopa de pollo"
- Precio: $25585 (con IVA 19%)

---

## Unidades Soportadas

| Tipo | Unidades | Estándar |
|------|----------|---------|
| **Peso** | kg, g, lb | Gramo (g) |
| **Volumen** | l, ml | Mililitro (ml) |
| **Unidad** | und | Unidad (und) |

---

## Archivos Nuevos Creados

```
fuddo/
├── materias_primas/
│   ├── materias_primas.php     ← Interfaz principal
│   └── procesar.php            ← Procesamiento CRUD
├── recetas/
│   ├── recetas.php             ← Interfaz principal
│   └── procesar.php            ← Procesamiento CRUD
├── includes/
│   └── funciones_conversiones.php  ← Cálculos y conversiones
├── sql/
│   ├── template_restaurante.sql         ← Actualizado
│   ├── add_costeo_automatico.sql        ← Nuevo
│   └── migracion_costeo_existentes.sql  ← Nuevo
├── admin/
│   └── migrar_costeo.php       ← Script de migración
├── includes/
│   └── conexion.php            ← Actualizado
├── includes/
│   └── menu.php                ← Actualizado
└── GUIA_SISTEMA_COSTEO_AUTOMATICO.md   ← Documentación completa
```

---

## Relaciones de Base de Datos

```
materias_primas
    │
    ├─ id_materia_prima [PK]
    ├─ nombre
    ├─ unidad_medida
    ├─ costo_por_unidad_minima
    └─...

recetas
    │
    ├─ id_receta [PK]
    ├─ nombre_platillo
    ├─ costo_total_receta (calculado)
    ├─ id_producto_asociado [FK]
    └─...

receta_ingredientes
    │
    ├─ id [PK]
    ├─ id_receta [FK] → recetas
    ├─ id_materia_prima [FK] → materias_primas
    ├─ cantidad_usada
    ├─ costo_ingrediente (calculado)
    └─...

productos (ya existía)
    │
    ├─ id [PK]
    ├─ nombre_producto
    ├─ costo_producto (del que lo crea)
    └─... (se vincula desde recetas)
```

---

## Conversión de Unidades - Ejemplos

### Peso

| Entrada | Conversión |
|---------|-----------|
| 1 kg | 1000 g |
| 2.5 lb | 1134.8 g |
| 500 g | 500 g |

### Volumen

| Entrada | Conversión |
|---------|-----------|
| 1 l | 1000 ml |
| 250 ml | 250 ml |
| 0.5 l | 500 ml |

---

## Cambios en Archivos Existentes

### `includes/conexion.php`
- ✓ Agregados 3 aliases de tablas nuevas

### `includes/menu.php`
- ✓ Agregado ítem "Costeo" con submódulos

### `sql/template_restaurante.sql`
- ✓ Agregadas 3 tablas nuevas

---

## Preguntas Frecuentes

**P: ¿Qué pasa si elimino una materia prima?**
R: No se puede eliminar si la estás usando en una receta.

**P: ¿Puedo cambiar el costo después?**
R: Sí, en Materias Primas. Solo se actualiza el costo por unidad mínima.

**P: ¿El costo de la receta se actualiza automáticamente?**
R: No. Se puede editar manualmente o recalcular editando la receta.

**P: ¿En qué unidad creo ingredientes de recetas?**
R: En la unidad mínima del producto (g, ml o und).

**P: ¿Puedo cambiar el IVA?**
R: El 19% es por defecto. Modificar en `/recetas/procesar.php`.

**P: ¿Los productos creados son automáticos?**
R: Sí, se crean al guardar la receta con nombre y costo calculado.

---

## Soporte y Contacto

Para reportar errores o suger encias, contactar al equipo técnico.

---

**Versión:** 1.0
**Fecha:** Febrero 2026
**Autor:** Sistema FUDDO
