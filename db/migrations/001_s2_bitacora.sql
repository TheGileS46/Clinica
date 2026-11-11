-- Sprint 2 · HU-07 · RNF01.7: bitácora de accesos y fallos de autenticación.
-- Uso: mysql -u root clinica_db < db/migrations/001_s2_bitacora.sql
-- Después ejecutar: php bin/hash-passwords.php   (RNF01.2)

CREATE TABLE IF NOT EXISTS bitacora (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id INT UNSIGNED NULL,
  correo VARCHAR(150) NULL,
  evento VARCHAR(50) NOT NULL,
  resultado ENUM('exito','fallo') NOT NULL,
  detalle VARCHAR(255) NULL,
  ip VARCHAR(45) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bitacora_fecha (created_at),
  KEY idx_bitacora_usuario (usuario_id),
  KEY idx_bitacora_evento (evento, resultado),
  CONSTRAINT fk_bitacora_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
