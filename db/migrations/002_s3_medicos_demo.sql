-- Sprint 3 · HU-08: vincula la cuenta demo medico@clinica.com con un registro de médico
-- y le asigna horarios de lunes a viernes (09:00–14:00 y 16:00–19:00). Idempotente.
-- Uso: mysql -u root clinica_db < db/migrations/002_s3_medicos_demo.sql

INSERT INTO medicos (usuario_id, especialidad_id, nombre, cedula_profesional, telefono, correo, activo)
SELECT u.id, (SELECT id FROM especialidades WHERE nombre = 'Medicina General'), u.nombre, NULL, NULL, u.correo, 1
FROM usuarios u
WHERE u.correo = 'medico@clinica.com'
  AND NOT EXISTS (SELECT 1 FROM medicos m WHERE m.usuario_id = u.id);

INSERT INTO horarios_medico (medico_id, dia_semana, hora_inicio, hora_fin, activo)
SELECT m.id, d.dia, b.inicio, b.fin, 1
FROM medicos m
INNER JOIN usuarios u ON u.id = m.usuario_id AND u.correo = 'medico@clinica.com'
CROSS JOIN (SELECT 1 AS dia UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) d
CROSS JOIN (SELECT '09:00:00' AS inicio, '14:00:00' AS fin UNION ALL SELECT '16:00:00', '19:00:00') b
WHERE NOT EXISTS (SELECT 1 FROM horarios_medico h WHERE h.medico_id = m.id);
