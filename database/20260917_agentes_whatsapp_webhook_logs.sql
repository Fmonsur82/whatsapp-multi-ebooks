CREATE TABLE agentes_whatsapp_webhook_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    meta_waba_id VARCHAR(64) NULL,
    meta_phone_number_id VARCHAR(64) NULL,
    tipo_evento VARCHAR(80) NULL,
    firma_valida TINYINT(1) NOT NULL,
    resultado ENUM(
        'aceptado',
        'ignorado',
        'firma_invalida',
        'payload_invalido',
        'rechazado_tamano',
        'error_interno'
    ) NOT NULL,
    http_status_respuesta SMALLINT UNSIGNED NOT NULL,
    error_codigo VARCHAR(100) NULL,
    error_mensaje VARCHAR(500) NULL,
    payload JSON NULL,
    headers JSON NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_agentes_whatsapp_webhook_logs_creado_en (creado_en),
    KEY idx_agentes_whatsapp_webhook_logs_resultado_creado_en (resultado, creado_en),
    KEY idx_agentes_whatsapp_webhook_logs_meta_waba_id (meta_waba_id),
    KEY idx_agentes_whatsapp_webhook_logs_meta_phone_number_id (meta_phone_number_id)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE=utf8mb4_unicode_ci;
