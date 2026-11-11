#!/usr/bin/env bash
# Regenera db/clinica_db.sql (esquema + catálogos y usuarios de demostración) desde la BD local.
# Uso: bash bin/dump-schema.sh
set -euo pipefail
cd "$(dirname "$0")/.."
MYSQLDUMP=${MYSQLDUMP:-/opt/lampp/bin/mysqldump}
DB=${DB_DATABASE:-clinica_db}
OUT=db/clinica_db.sql
{
  echo "-- RAMBUTÁN · Script de instalación de la base de datos ($DB)"
  echo "-- Generado con bin/dump-schema.sh el $(date '+%d/%m/%Y')."
  echo "-- Instalación: mysql -u root -e 'CREATE DATABASE $DB CHARACTER SET utf8mb4' && mysql -u root $DB < $OUT"
  echo "-- Usuarios demo (contraseñas con password_hash): admin@clinica.com / Admin123,"
  echo "--   recepcion@clinica.com / Recepcion123, medico@clinica.com / Medico123"
  echo
  "$MYSQLDUMP" -uroot --no-data --skip-comments --skip-routines --skip-triggers "$DB" | sed 's/ AUTO_INCREMENT=[0-9]*//'
  echo
  echo "-- Datos base"
  "$MYSQLDUMP" -uroot --no-create-info --skip-comments --skip-triggers --complete-insert --skip-extended-insert "$DB" \
    roles especialidades servicios usuarios ${SEED_TABLES:-}
} > "$OUT"
echo "Generado $OUT"
