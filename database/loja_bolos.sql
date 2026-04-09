CREATE DATABASE IF NOT EXISTS loja_bolos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja_bolos;

DROP TABLE IF EXISTS pedido_itens;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('admin','cliente') NOT NULL DEFAULT 'cliente',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    imagem VARCHAR(255) DEFAULT NULL,
    categoria VARCHAR(100) DEFAULT NULL,
    destaque TINYINT(1) NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cliente_nome VARCHAR(150) NOT NULL,
    cliente_telefone VARCHAR(40) NOT NULL,
    cliente_endereco VARCHAR(255) NOT NULL,
    tipo_entrega ENUM('retirada','motoboy') NOT NULL DEFAULT 'retirada',
    observacoes TEXT DEFAULT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Novo',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT DEFAULT NULL,
    nome_produto VARCHAR(150) NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedido_itens_pedidos FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
);

INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Administrador', 'admin@doranina.com', '$2y$12$Yk5sVW7nRnjVdA1xKywThOlisig2Q0x.WB/VJFkj2TRZZ1fPYteBW', 'admin');

INSERT INTO produtos (nome, descricao, preco, imagem, categoria, destaque, ativo) VALUES
('Cenourinha', 'Bolo de cenoura com cobertura de brigadeiro gourmet de chocolate meio amargo.', 30.00, 'https://images.unsplash.com/photo-1550617931-e17a7b70dce2?auto=format&fit=crop&w=900&q=80', 'Com cobertura', 1, 1),
('Chocolate', 'Bolo de chocolate com cobertura de brigadeiro gourmet de chocolate meio amargo.', 40.00, 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?auto=format&fit=crop&w=900&q=80', 'Com cobertura', 1, 1),
('Formigueiro', 'Bolo formigueiro com cobertura de brigadeiro de chocolate meio amargo.', 35.00, 'https://images.unsplash.com/photo-1621303837174-89787a7d4729?auto=format&fit=crop&w=900&q=80', 'Com cobertura', 0, 1),
('Laranjinha', 'Bolo de laranja suave com cobertura de brigadeiro de laranja.', 30.00, 'https://images.unsplash.com/photo-1464306076886-da185f6a9d05?auto=format&fit=crop&w=900&q=80', 'Com cobertura', 0, 1),
('Limão Siciliano com Mirtilo', 'Bolo especial com cobertura de ganache de chocolate branco.', 40.00, 'https://images.unsplash.com/photo-1562440499-64c9a111f713?auto=format&fit=crop&w=900&q=80', 'Especiais', 1, 1),
('Coco com Tapioca', 'Com calda de leite de coco, doce de leite e finalização com coco ralado.', 35.00, 'https://images.unsplash.com/photo-1535141192574-5d4897c12636?auto=format&fit=crop&w=900&q=80', 'Especiais', 0, 1);
