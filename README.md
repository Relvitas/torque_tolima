# 🏍️ Torque Tolima — Autolavado de Motos (PHP MVC)

Aplicación de gestión para un autolavado de motos: registro de lavadas,
clientes con programa de fidelidad, agenda de citas, historial, facturación
POS (80 mm) y resumen del negocio.

Reescrita desde el prototipo de una sola página (`torque_tolima.html`,
`localStorage`) a una **arquitectura MVC en PHP con MySQL/MariaDB**.

---

## 🏛️ Arquitectura

```
torque_tolima/
├── public/                 ← Raíz web (DocumentRoot)
│   ├── index.php           ← Front controller (único punto de entrada)
│   ├── .htaccess           ← Reescritura de URLs
│   ├── css/app.css
│   ├── js/app.js           ← Interacción de UI (calendario, previews)
│   └── uploads/            ← Fotos de las motos
├── app/
│   ├── Core/               ← Núcleo del mini-framework
│   │   ├── Router.php      ← Enrutador
│   │   ├── Controller.php  ← Controlador base
│   │   ├── Model.php       ← Modelo base (PDO)
│   │   ├── Database.php    ← Conexión PDO singleton
│   │   ├── View.php        ← Renderizador de vistas/JSON
│   │   └── helpers.php     ← e(), url(), cop()...
│   ├── Controllers/        ← Lavada, Cita, Cliente, Historial, Resumen, Factura
│   ├── Models/             ← Cliente, Lavada, Cita
│   └── Views/              ← Plantillas .php + layout
├── config/config.php       ← Configuración (BD, negocio)
└── database/schema.sql     ← Esquema de la base de datos
```

**Flujo:** `public/index.php` → `Router` → `Controller` → `Model` (datos) → `View` (HTML).

---

## ⚙️ Instalación

### 1. Crear la base de datos

```bash
mysql -u root -p < database/schema.sql
```

### 2. Configurar credenciales

Edita `config/config.php` (o usa variables de entorno):

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'torque_tolima');
define('DB_USER', 'root');
define('DB_PASS', 'tu_clave');
```

También puedes exportarlas: `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_HOST`, `BASE_URL`.

### 3. Levantar el servidor

**Desarrollo (PHP integrado):**

```bash
php -S localhost:8000 -t public
```

Abre 👉 http://localhost:8000

**Producción (Apache):** apunta el `DocumentRoot` a la carpeta `public/`
y asegúrate de tener `mod_rewrite` activo.

---

## 🧭 Rutas

| Método | Ruta                | Acción                              |
|--------|---------------------|-------------------------------------|
| GET    | `/`                 | Formulario de nueva lavada          |
| GET    | `/lavada/buscar`    | Autocompletar cliente (JSON)        |
| POST   | `/lavada/registrar` | Registrar lavada                    |
| GET    | `/citas`            | Calendario de citas                 |
| POST   | `/citas/agendar`    | Agendar cita                        |
| POST   | `/citas/eliminar`   | Eliminar cita                       |
| GET    | `/clientes`         | Lista de clientes (`?q=` busca)     |
| GET    | `/historial`        | Historial de lavadas (`?q=` busca)  |
| GET    | `/resumen`          | Resumen / estadísticas              |
| GET    | `/factura/{id}`     | Factura imprimible (POS 80 mm)      |

---

## 🎁 Programa de fidelidad

Cada 6.ª lavada es **gratis** (configurable en `LAVADAS_PARA_GRATIS`).
El contador se lleva por cliente en la base de datos.

## 🔐 Notas de seguridad

- Todas las consultas usan **sentencias preparadas** (PDO) → sin inyección SQL.
- La salida en vistas se escapa con `e()` → mitiga XSS.
- Las fotos subidas se validan con `getimagesize()` antes de guardarse.
