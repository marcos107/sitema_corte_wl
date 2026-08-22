-- Mantem backups antigos compativeis com as consultas da versao atual.
ALTER TABLE empreendimentos
    ADD COLUMN IF NOT EXISTS escala VARCHAR(15) NULL AFTER data_add;

UPDATE empreendimentos
SET escala = ''
WHERE escala IS NULL;

ALTER TABLE empreendimentos
    MODIFY COLUMN escala VARCHAR(15) NOT NULL AFTER data_add;
