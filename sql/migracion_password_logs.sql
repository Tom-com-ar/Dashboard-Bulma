-- Script de migración para actualizar la tabla password_logs
-- Ejecutar este script en phpMyAdmin o en la consola de MySQL

-- Agregar columnas a password_logs si no existen
ALTER TABLE password_logs
ADD COLUMN user_id INT(11) DEFAULT NULL AFTER id,
ADD COLUMN security_level INT(1) DEFAULT 0 AFTER password_hash,
ADD COLUMN strength_label VARCHAR(20) DEFAULT 'débil' AFTER security_level;

-- Agregar índice para user_id
ALTER TABLE password_logs 
ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Crear índice para búsquedas más rápidas
ALTER TABLE password_logs
ADD INDEX idx_user_date (user_id, fecha_creacion DESC);

-- Verificar estructura de la tabla
DESC password_logs;
