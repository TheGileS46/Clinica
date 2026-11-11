# Pendientes de calidad

Correcciones detectadas en las pruebas de calidad. Cada una indica de qué prueba salió, su severidad, qué hay que hacer y su estado. Al corregirla se marca como resuelta con la versión correspondiente y se vuelve a ejecutar su prueba.

## Auditoría del 11/11/2026

Pruebas de caja negra (82 casos), carga y estrés (hasta 150 usuarios), análisis estático (php -l, PHPStan 2 nivel 6, PSR-12, npm audit) y navegador real (Chrome headless), ejecutadas sobre el commit `e7adeb0` con una base aislada de 3000 pacientes y 3158 citas. Todos los umbrales de rendimiento de la ERS se cumplieron (RNF02.1, RNF02.2, RNF02.3) y no hubo errores fatales de PHP. La evidencia completa se conserva fuera del repositorio.

| ID | Severidad | Pendiente | Detectado por | Requisito | Estado |
| --- | --- | --- | --- | --- | --- |
| H-01 | **Media** | Las peticiones `HEAD` a rutas GET responden **405 Method Not Allowed**. | Caja negra, caso CP-14 (`curl -I /login` → 405) | RNF04.5 | Resuelto (0.7) · CP-77: HEAD → 200 |
| H-02 | **Media** | Latencia de cola con ráfagas de ≥ 100 usuarios: el p99 llega a **4.6 s** (p95 de 136 ms). | Estrés: búsqueda de pacientes con 100 usuarios | RNF02.3 | Pendiente de despliegue (configuración del servidor) |
| H-03 | Baja | `npm audit`: 2 vulnerabilidades en `source-map-js` (severidad alta: denegación de servicio). | Estático: `npm audit` | RNF05.3 | Resuelto (0.7) · `npm audit`: 0 vulnerabilidades |
| H-04 | Baja | PHPStan nivel 6: 102 arreglos sin tipo de elementos documentado y 2 comparaciones redundantes en `app/Support/Auth.php` (líneas 58 y 185). | Estático: PHPStan 2.3.5 | RNF05.4 | Resuelto (0.7) · PHPStan nivel 6: 0 errores |
| H-05 | Baja | PSR-12: 109 líneas de más de 120 caracteres y 4 errores por scripts de consola que declaran funciones y además ejecutan código. | Estático: PHP_CodeSniffer 3.13.6 | RNF05.2 | Resuelto (0.7) · PSR-12 en `app/` y `bin/`: 0 errores, 0 advertencias |
| H-06 | Baja | Al registrar una excepción, el log de PHP incluye los argumentos de las funciones, entre ellos el correo del usuario truncado. | Caja negra, caso CP-13 (BD no disponible) | RNF01.8, OR-02 | Resuelto (0.7) · traza sin argumentos verificada en CP-13 |
| H-07 | Baja | En móvil (360 px) las tablas de citas y pacientes se recortan; hay que deslizarlas de lado y las columnas de estado y acciones quedan ocultas al inicio. | Navegador: captura «citas del día» en móvil | RNF03.1 | Resuelto (0.7) · tablas apiladas a 360 px, sin desbordamiento |

### Verificación de las correcciones (versión 0.7)

Se repitió la auditoría completa sobre el código corregido. Resultados: 82 de 82 casos de caja negra aprobados, incluido el nuevo **CP-77** (`HEAD /login` → 200); PHPStan 2 nivel 6 sin errores; PSR-12 sin errores ni advertencias en `app/` y `bin/`; `npm audit` sin vulnerabilidades; trazas de excepción sin argumentos (CP-13); 15 páginas sin desbordamiento horizontal a 360 px, con las tablas mostradas como tarjetas; flujo de agendado aprobado en 4 interacciones. El p99 con 100 usuarios sigue en ~5 s, como se esperaba, porque H-02 depende de la configuración del servidor.

Cambios aplicados:

- **H-01:** `App::dispatch()` atiende `HEAD` como `GET`; los métodos no soportados siguen respondiendo 405.
- **H-03:** `npm audit fix` actualizó `source-map-js` en `package-lock.json`.
- **H-04:** anotaciones `@param`/`@return`/`@var` con tipos de arreglo en controladores, modelos y soporte; se quitaron el `?? 'Lax'` de `Auth::logout()` y la comparación redundante de `Auth::sessionData()`.
- **H-05:** líneas largas partidas (llamadas, arreglos, SQL y textos); validador de correo compartido `Controller::validEmail()`; comentario de cabecera de `bin/hash-passwords.php` antes de `declare`.
- **H-06:** `App::bootstrap()` activa `zend.exception_ignore_args`.
- **H-07:** clase `.table-stack` en `resources/css/app.css` y atributo `data-label` en las celdas de 7 tablas.

### Qué hacer (detalle original)

**H-01 · `HEAD` → 405.** Los monitores de disponibilidad, los balanceadores y algunos proxies verifican el servicio con `HEAD`; hoy `/health` les responde 405 y lo darían por caído. Según RFC 9110 §9.3.2, `HEAD` debe responder igual que `GET`, sin cuerpo. Cambio propuesto en `app/App.php`, al inicio de `dispatch()`:

```php
// HEAD se atiende como GET; Apache descarta el cuerpo de la respuesta (RFC 9110 §9.3.2).
$method = $method === 'HEAD' ? 'GET' : $method;
```

Verificación: `curl -sI http://localhost/Clinica/public/health` debe responder `200 OK`, y `curl -X PUT …/login` debe seguir respondiendo 405.

**H-02 · Latencia de cola (pendiente de despliegue).** Coincide con la configuración `prefork` de XAMPP (`StartServers 5`, `MinSpareServers 5`, `MaxSpareServers 10`): ante una ráfaga, Apache crea procesos nuevos mientras las peticiones esperan. En el servidor de producción (Sprint 5, HU-17): subir `StartServers` y `MinSpareServers` a la concurrencia esperada (≥ 20), o usar `mpm_event` con PHP-FPM. Repetir la prueba de estrés en ese servidor. No incumple la ERS, porque sus umbrales se miden en p95. Configuración sugerida para el bloque `<IfModule mpm_prefork_module>` de `httpd-mpm.conf`:

```apache
StartServers            20
MinSpareServers         20
MaxSpareServers         40
MaxRequestWorkers      150
```

**H-03 · Dependencias.** Ejecutar `npm audit fix` y volver a compilar con `npm run build:css`. Es una dependencia de compilación de Tailwind que no se ejecuta en el servidor.

**H-04 · Tipos.** Documentar los arreglos con `@param array<string, mixed>` y `@return list<array<string, mixed>>`. Eliminar el `?? 'Lax'` de `Auth::logout()` y la comparación `!== null` de `Auth::sessionData()`.

**H-05 · Estilo.** Partir las líneas largas de las vistas y de las consultas SQL, y mover las funciones auxiliares de los scripts de consola a una clase.

**H-06 · Datos personales en logs.** En `php.ini` de producción: `zend.exception_ignore_args = On`. La contraseña no aparece porque PHP la oculta como `SensitiveParameterValue`, pero el correo sí.

**H-07 · Tablas en móvil.** Debajo de `sm` (640 px), mostrar cada fila como una tarjeta apilada en lugar de usar una tabla con desplazamiento horizontal. Va junto con la verificación de RNF03.1 del Sprint 5.

### Observaciones que no son defectos

- El navegador registró una petición `ERR_ABORTED` a `/citas/disponibilidad`: es la consulta que el JavaScript cancela a propósito al cambiar de médico a fecha.
- Las respuestas 404, 405 y 500 del registro de acceso las provocan a propósito los casos CP-13, CP-19, CP-44 y CP-74.

### Fuera del alcance de esta auditoría

Siguen pendientes de medir: HTTPS/TLS (RNF01.1), disponibilidad mensual (RNF04.2), compatibilidad con Firefox, Edge y Safari (RNF05.1) y contraste WCAG 2.1 AA (RNF03.4). Los tiempos son de un equipo local donde cliente y servidor comparten la máquina; hay que repetir la medición en el servidor de entrega.
