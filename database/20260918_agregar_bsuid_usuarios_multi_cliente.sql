ALTER TABLE usuarios_multi_cliente
    ADD COLUMN bsuid VARCHAR(140) NULL AFTER telefono,
    ADD KEY idx_usuarios_multi_cliente_bsuid (bsuid);
