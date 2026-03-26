# Dashboard Bulma - Documentación

## 📋 Descripción

Dashboard completo con **Dark Mode** integrado, construido con **Bulma CSS**, **JavaScript**, **PHP** y **MySQL**.

---

## 🎨 Dark Mode

### Características

- ✅ **Toggle automático** de Light/Dark mode
- ✅ **Persistencia** en localStorage
- ✅ **Transiciones suaves** entre temas
- ✅ **Variables CSS** personalizadas para cada tema
- ✅ **Detección automática** de preferencia del sistema
- ✅ **Colores optimizados** para ambos modos

### Cómo funciona

El sistema de Dark Mode utiliza **variables CSS personalizadas** que cambian según el estado:

```css
/* Light Mode (por defecto) */
:root {
  --bg-primary: #ffffff;
  --text-primary: #222222;
  /* ... más variables ... */
}

/* Dark Mode */
html.dark-mode {
  --bg-primary: #1a1a1a;
  --text-primary: #e9e9e9;
  /* ... más variables ... */
}
```

### Paleta de Colores

#### Light Mode
- **Fondo primario**: #ffffff (Blanco)
- **Fondo secundario**: #f5f5f5 (Gris claro)
- **Texto primario**: #222222 (Casi negro)
- **Color primario**: #3273dc (Azul)

#### Dark Mode
- **Fondo primario**: #1a1a1a (Negro oscuro)
- **Fondo secundario**: #242424 (Gris oscuro)
- **Texto primario**: #e9e9e9 (Gris claro)
- **Color primario**: #4a9eff (Azul brillante)

#### Colores de Estado (Ambos modos)
- **Success**: #48c774 (Verde)
- **Danger**: #f14668 (Rojo claro) / #ff6b7a (Rojo brillante en dark)
- **Warning**: #ffdd57 (Amarillo)
- **Info**: #3298dc (Azul claro)

---

## 🗄️ Base de Datos

### Instalación

1. **Abrir phpMyAdmin** (http://localhost/phpmyadmin)
2. **Crear nueva base de datos** (o usar la existente)
3. **Copiar y ejecutar** el contenido de `sql/database.sql`

```bash
# Alternativa: Desde línea de comandos
mysql -u root -p < sql/database.sql
```

### Estructura de Tablas

#### 1. **usuarios**
Almacena información de los usuarios del dashboard

```sql
CREATE TABLE usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'usuario', 'moderador'),
  estado ENUM('activo', 'inactivo', 'suspendido'),
  fecha_registro TIMESTAMP,
  ultimo_acceso DATETIME
);
```

**Campos:**
- `id`: ID único del usuario
- `nombre`: Nombre completo
- `email`: Correo único
- `password`: Contraseña (hasheada)
- `rol`: Rol del usuario (admin, usuario, moderador)
- `estado`: Estado actual
- `fecha_registro`: Cuándo se registró
- `ultimo_acceso`: Último acceso al dashboard

#### 2. **configuracion**
Almacena configuraciones del sistema

```sql
CREATE TABLE configuracion (
  id INT PRIMARY KEY AUTO_INCREMENT,
  clave VARCHAR(100) UNIQUE NOT NULL,
  valor TEXT,
  tipo ENUM('texto', 'numero', 'booleano', 'json')
);
```

**Ejemplos:**
- `nombre_sitio`: "Dashboard Bulma"
- `color_tema`: "#3273dc"
- `modo_mantenimiento`: "false"

#### 3. **reportes**
Almacena reportes generados

```sql
CREATE TABLE reportes (
  id INT PRIMARY KEY AUTO_INCREMENT,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  usuario_id INT NOT NULL,
  tipo ENUM('ventas', 'usuarios', 'trafico', 'otros'),
  datos JSON,
  fecha_creacion TIMESTAMP
);
```

#### 4. **estadisticas**
Estadísticas generales del dashboard

```sql
CREATE TABLE estadisticas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  total_usuarios INT,
  usuarios_activos INT,
  ingresos DECIMAL(10, 2),
  conversiones DECIMAL(5, 2),
  fecha_actualizacion TIMESTAMP
);
```

#### 5. **logs**
Registro de todas las acciones

```sql
CREATE TABLE logs (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT,
  accion VARCHAR(200) NOT NULL,
  tipo ENUM('acceso', 'edicion', 'eliminacion', 'error'),
  ip_address VARCHAR(45),
  fecha TIMESTAMP
);
```

#### 6. **sesiones**
Gestión de sesiones activas

```sql
CREATE TABLE sesiones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  token VARCHAR(255) UNIQUE,
  ip_address VARCHAR(45),
  fecha_inicio TIMESTAMP,
  fecha_expiracion DATETIME,
  estado ENUM('activa', 'cerrada', 'expirada')
);
```

### Usuarios de ejemplo

La base de datos viene preconfigurada con usuarios de ejemplo:

| Email | Contraseña | Rol |
|-------|-----------|-----|
| admin@dashboard.com | admin123 | admin |
| juan@example.com | pass123 | usuario |
| maria@example.com | pass123 | usuario |

### Vistas (Views)

Se crean automáticamente dos vistas útiles:

```sql
-- Usuarios activos
SELECT * FROM vista_usuarios_activos;

-- Estadísticas resumen
SELECT * FROM vista_estadisticas_resumen;
```

---

## 💻 Archivos PHP

### config.php
Archivo de configuración y conexión a la base de datos.

```php
<?php
require_once 'php/config.php';

// Usar la conexión global $conn
$usuarios = obtenerDatos('usuarios');
?>
```

**Funciones disponibles:**
- `obtenerDatos($tabla, $where, $orderBy)` - Obtener múltiples registros
- `obtenerPorId($tabla, $id)` - Obtener un registro por ID
- `insertar($tabla, $datos)` - Insertar un nuevo registro
- `actualizar($tabla, $datos, $id)` - Actualizar un registro
- `eliminar($tabla, $id)` - Eliminar un registro
- `escape($string)` - Escapar strings

---

## 🎯 Uso del Dark Mode en HTML

El sistema de Dark Mode está completamente integrado. Solo necesitas:

1. **Incluir el CSS**: 
```html
<link rel="stylesheet" href="./css/style.css">
```

2. **Incluir el JavaScript**:
```html
<script src="./js/main.js"></script>
```

3. **El botón de toggle** ya está en la navbar:
```html
<button class="button is-light" id="themeToggle">
    <span id="themeIcon">🌙 Dark</span>
</button>
```

### Agregar Dark Mode a nuevos componentes

Todos los estilos usan variables CSS, así que automáticamente se adaptan:

```css
/* Tus nuevos estilos */
.mi-componente {
  background-color: var(--card-bg);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}
```

---

## 📁 Estructura del Proyecto

```
Dashboard - Bulma/
├── index.html              # Página principal
├── css/
│   └── style.css           # Estilos (con Dark Mode integrado)
├── js/
│   └── main.js             # JavaScript (toggle Dark Mode)
├── php/
│   └── config.php          # Configuración BD y funciones
├── sql/
│   └── database.sql        # Script de base de datos
└── src/                    # Carpeta para componentes reutilizables
```

---

## 🔧 Modificar Colores

Para cambiar los colores del tema, edita `/css/style.css`:

```css
:root {
  /* Light Mode */
  --color-primary: #3273dc; /* Cambia aquí */
}

html.dark-mode {
  /* Dark Mode */
  --color-primary: #4a9eff; /* Cambia aquí */
}
```

---

## 🛡️ Seguridad

- Las contraseñas se hashean con `SHA2` (en la BD)
- Las consultas usan `prepared statements`
- Se tienen triggers para registrar cambios
- Logs de todas las acciones para auditoría

---

## 📞 Soporte

Para más información, revisa los comentarios en el código.

**Última actualización**: Marzo 2026
