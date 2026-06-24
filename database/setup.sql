-- ============================================================
--  Torque Tolima — Setup inicial (ejecutar con: sudo mysql < database/setup.sql)
--  Crea la base de datos, las tablas y un usuario dedicado para la app.
-- ============================================================

SOURCE database/schema.sql;

-- Usuario de la aplicación (conexión por TCP/contraseña, no por socket).
CREATE USER IF NOT EXISTS 'torque'@'localhost' IDENTIFIED BY 'torque2024';
GRANT ALL PRIVILEGES ON torque_tolima.* TO 'torque'@'localhost';
FLUSH PRIVILEGES;
