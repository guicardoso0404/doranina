USE loja_bolos;

ALTER TABLE pedidos
    ADD COLUMN tipo_entrega ENUM('retirada','motoboy') NOT NULL DEFAULT 'retirada' AFTER cliente_endereco;

UPDATE pedidos
SET tipo_entrega = 'motoboy'
WHERE tipo_entrega IS NULL OR tipo_entrega = '';

ALTER TABLE pedidos
    DROP COLUMN data_entrega,
    DROP COLUMN horario_entrega;
