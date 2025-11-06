CREATE DATABASE IF NOT EXISTS produtos_db;
USE produtos_db;

CREATE TABLE IF NOT EXISTS produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  preco DECIMAL(10,2),
  categoria VARCHAR(50),
  quantidade INT,
  imagem VARCHAR(255)
);
