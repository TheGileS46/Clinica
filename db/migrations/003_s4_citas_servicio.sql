-- Sprint 4 · HU-10 · RF05: una cita relaciona paciente, médico y servicio (Apéndice A).
-- Uso: mysql -u root clinica_db < db/migrations/003_s4_citas_servicio.sql

ALTER TABLE citas
  ADD COLUMN IF NOT EXISTS servicio_id INT UNSIGNED NULL AFTER medico_id,
  ADD COLUMN IF NOT EXISTS cancelada_por INT UNSIGNED NULL AFTER creado_por,
  ADD COLUMN IF NOT EXISTS motivo_cancelacion VARCHAR(255) NULL AFTER cancelada_por;

ALTER TABLE citas
  ADD KEY IF NOT EXISTS idx_cita_servicio (servicio_id),
  ADD KEY IF NOT EXISTS idx_cita_medico_estado (medico_id, estado, inicio);

-- MariaDB 10.4 no admite IF NOT EXISTS en FOREIGN KEY: se agrega solo si falta.
SET @fk := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'citas' AND CONSTRAINT_NAME = 'fk_citas_servicio');
SET @sql := IF(@fk = 0,
  'ALTER TABLE citas ADD CONSTRAINT fk_citas_servicio FOREIGN KEY (servicio_id) REFERENCES servicios (id), ADD CONSTRAINT fk_citas_cancelada_por FOREIGN KEY (cancelada_por) REFERENCES usuarios (id) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
