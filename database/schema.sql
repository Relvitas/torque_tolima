-- ============================================================
--  Torque Tolima — Esquema de base de datos (MySQL / MariaDB)
-- ============================================================
--  Uso:
--    mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS torque_tolima
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE torque_tolima;

-- ---------- Clientes ----------
CREATE TABLE IF NOT EXISTS clientes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  telefono     VARCHAR(20)  NOT NULL,
  nombre       VARCHAR(120) NOT NULL,
  placa        VARCHAR(20)  DEFAULT NULL,
  moto         VARCHAR(120) DEFAULT NULL,
  foto         VARCHAR(255) DEFAULT NULL,   -- ruta del archivo en /public/uploads
  lavadas      INT UNSIGNED NOT NULL DEFAULT 0,
  total_gratis INT UNSIGNED NOT NULL DEFAULT 0,
  creado_en    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_telefono (telefono),
  KEY idx_placa (placa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Historial de lavadas ----------
CREATE TABLE IF NOT EXISTS lavadas (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cliente_id  INT UNSIGNED NOT NULL,
  telefono    VARCHAR(20)  NOT NULL,   -- snapshot al momento de la lavada
  nombre      VARCHAR(120) NOT NULL,
  placa       VARCHAR(20)  DEFAULT NULL,
  moto        VARCHAR(120) DEFAULT NULL,
  precio      INT UNSIGNED NOT NULL DEFAULT 0,
  gratis      TINYINT(1)   NOT NULL DEFAULT 0,
  pagado      TINYINT(1)   NOT NULL DEFAULT 1,   -- 1 = pagada, 0 = pendiente (debe)
  num_lavada  INT UNSIGNED NOT NULL DEFAULT 0,
  creado_en   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_cliente (cliente_id),
  KEY idx_fecha (creado_en),
  CONSTRAINT fk_lavada_cliente FOREIGN KEY (cliente_id)
    REFERENCES clientes (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Egresos (gastos del negocio) ----------
CREATE TABLE IF NOT EXISTS egresos (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  concepto  VARCHAR(160) NOT NULL,
  categoria VARCHAR(60)  NOT NULL DEFAULT 'Otros',
  monto     INT UNSIGNED NOT NULL DEFAULT 0,
  nota      VARCHAR(255) DEFAULT NULL,
  creado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_fecha (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Citas ----------
CREATE TABLE IF NOT EXISTS citas (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fecha     DATE         NOT NULL,
  hora      VARCHAR(5)   NOT NULL,     -- formato HH:MM
  nombre    VARCHAR(120) NOT NULL,
  telefono  VARCHAR(20)  NOT NULL,
  placa     VARCHAR(20)  DEFAULT NULL,
  nota      VARCHAR(255) DEFAULT NULL,
  creado_en TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fecha_hora (fecha, hora),
  KEY idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
