ALTER TABLE usuarios_multi_cliente
    ADD COLUMN acepta_pol_priv TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN acepta_tyc TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN acepta_fecha_hora DATETIME NULL,
    ADD COLUMN acepta_dispositivo VARCHAR(500) NULL,
    ADD COLUMN acepta_ip VARCHAR(45) NULL,
    ADD COLUMN acepta_pol_priv_version VARCHAR(30) NULL,
    ADD COLUMN acepta_tyc_version VARCHAR(30) NULL;

CREATE TABLE agentes_whatsapp_bsuid_verificaciones (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    bsuid VARCHAR(140) NOT NULL,
    token_hash CHAR(64) NOT NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usado_en DATETIME NULL,
    revocado_en DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_agentes_bsuid_verificaciones_token_hash (token_hash),
    KEY idx_agentes_bsuid_verificaciones_bsuid (bsuid),
    KEY idx_agentes_bsuid_verificaciones_estado (usado_en, revocado_en)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
