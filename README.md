# RAMBUTÁN · CRM para clínica

Sistema web para administrar un núcleo de consultorios: usuarios y roles, médicos y sus horarios, especialidades, servicios, pacientes y citas con verificación de disponibilidad. Es un proyecto académico de la materia Calidad de Software, desarrollado por sprints a partir de una especificación de requisitos IEEE 830.

## Qué incluye

| Módulo | Qué hace | Quién lo usa |
| --- | --- | --- |
| Sitio público | Página de presentación de la clínica | Cualquier visitante |
| Autenticación | Inicio de sesión, cierre automático a los 15 minutos de inactividad, protección CSRF | Todos los roles |
| Usuarios | Alta, edición y activación de cuentas con su rol | Administrador |
| Médicos | Registro de médicos, especialidad y horarios de atención por día | Administrador |
| Especialidades y servicios | Catálogos que se usan al registrar médicos y citas | Administrador |
| Pacientes | Registro, búsqueda, edición y expediente básico | Administrador, Recepción |
| Citas | Agendar, reprogramar y cancelar; valida el horario del médico y evita empalmes | Administrador, Recepción |
| Bitácora | Registro de accesos y de intentos fallidos de inicio de sesión | Administrador |

Todavía no están hechos (aparecen como «Próximamente» en el menú): agenda, solicitudes e historial clínico.

## Tecnología

- **PHP 8.2 o superior**, con un MVC propio, sin framework ni Composer.
- **MySQL o MariaDB** mediante PDO.
- **Apache** con `mod_rewrite`.
- **Tailwind CSS** para los estilos. El CSS compilado ya viene en el repositorio, así que Node.js solo hace falta si vas a modificar los estilos.

## Instalación

La forma más sencilla es con [XAMPP](https://www.apachefriends.org/), que trae Apache, PHP y MariaDB. Las rutas de abajo son las de Linux (`/opt/lampp`); en Windows la carpeta equivalente es `C:\xampp`.

### 1. Clonar el proyecto dentro de `htdocs`

```bash
cd /opt/lampp/htdocs
git clone https://github.com/TheGileS46/Clinica.git
cd Clinica
```

La carpeta debe llamarse `Clinica` para que la dirección sea `http://localhost/Clinica/public`. Si le pones otro nombre, la dirección cambia igual.

### 2. Iniciar Apache y MySQL

```bash
sudo /opt/lampp/lampp start
```

En Windows se inician desde el panel de control de XAMPP.

### 3. Crear la base de datos

```bash
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE clinica_db CHARACTER SET utf8mb4"
/opt/lampp/bin/mysql -u root clinica_db < db/clinica_db.sql
```

También se puede hacer desde phpMyAdmin (`http://localhost/phpmyadmin`): crear la base `clinica_db` e importar el archivo `db/clinica_db.sql`.

Ese archivo ya trae todas las tablas, los catálogos y los usuarios de prueba. Los scripts de `db/migrations/` no hace falta ejecutarlos en una instalación nueva; solo sirven para actualizar una base creada con una versión anterior.

### 4. Configurar el entorno

```bash
cp .env.example .env
```

Los valores por defecto funcionan con XAMPP recién instalado (usuario `root` sin contraseña, puerto 3306). Si tu MySQL usa otros datos, cámbialos en `.env`:

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clinica_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Abrir el sistema

Entra a <http://localhost/Clinica> (redirige a `/Clinica/public`) y luego a «Iniciar sesión» con alguna de estas cuentas de prueba:

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@clinica.com` | `Admin123` |
| Recepción | `recepcion@clinica.com` | `Recepcion123` |
| Médico | `medico@clinica.com` | `Medico123` |

Para comprobar que la aplicación responde, abre <http://localhost/Clinica/public/health>.

## Modificar los estilos (opcional)

Los estilos se escriben en `resources/css/app.css` y se compilan a `public/assets/css/app.css`. Requiere Node.js 18 o superior.

```bash
npm install
npm run dev        # recompila al guardar
npm run build:css  # versión minificada para entregar
```

## Estructura del proyecto

```text
app/
  App.php          Arranque, carga de .env, enrutador y cabeceras de seguridad
  Database.php     Conexión PDO
  config/          Configuración, rutas (routes.php) y menú por rol (menu.php)
  Controllers/     Controladores por módulo
  Models/          Consultas a la base de datos
  Support/         Sesión y permisos, CSRF, bitácora, vistas y utilidades
  Views/           Plantillas PHP
bin/               Scripts de consola
db/                Script de instalación y migraciones
docs/              Pendientes de calidad
public/            Raíz web: index.php, .htaccess y recursos estáticos
resources/css/     Fuente de los estilos de Tailwind
```

Para agregar una pantalla nueva: se declara la ruta en `app/config/routes.php`, se crea el método en un controlador y su vista en `app/Views/`.

## Scripts de consola

| Comando | Para qué sirve |
| --- | --- |
| `bash bin/dump-schema.sh` | Regenera `db/clinica_db.sql` desde la base local |
| `php bin/hash-passwords.php` | Convierte a hash las contraseñas que sigan en texto plano |

## Problemas frecuentes

- **MySQL no inicia o la aplicación no conecta.** Lo más común es que otro MySQL (uno instalado en el sistema o un contenedor de Docker) ya esté usando el puerto 3306. Detén ese servicio, o cambia el puerto de XAMPP y pon el mismo en `DB_PORT`.
- **Error 404 en todas las páginas menos la de inicio.** Apache no está aplicando `public/.htaccess`. Revisa que `mod_rewrite` esté activo y que la carpeta `htdocs` tenga `AllowOverride All` (en XAMPP viene así por defecto).
- **Al entrar a `/Clinica` aparece el listado de carpetas en vez del sistema.** Apache está ignorando los `.htaccess` (misma causa que el punto anterior). Con ellos activos, `/Clinica` redirige solo a `/Clinica/public` y el resto de las carpetas responde 403.
- **La sesión se cierra sola.** Es intencional: caduca a los 15 minutos sin actividad. Se ajusta con `SESSION_TIMEOUT` (en segundos) en `.env`.

## Documentación

- [`docs/PENDIENTES.md`](docs/PENDIENTES.md): hallazgos de la auditoría de calidad y su estado.
