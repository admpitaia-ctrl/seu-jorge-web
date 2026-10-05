-- BASE DE DADOS: seu_jorge_store
-- OBJETIVO: Base de dados do caso prático PHP da loja oficial do Seu Jorge.

CREATE DATABASE IF NOT EXISTS seu_jorge_store
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE seu_jorge_store;

-- TABELA USERS
-- Guarda os utilizadores registados no website.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    address VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,
    country VARCHAR(80) NULL DEFAULT 'Portugal'
);

-- TABELA PRODUCTS
-- Guarda os produtos da loja online.
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50)
);

-- TABELA CATEGORIES
-- Guarda as categorias disponíveis no dropdown do administrador.
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
);

-- TABELA ORDERS
-- Guarda as encomendas feitas pelos utilizadores.
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'in_transit', 'delivered') DEFAULT 'pending',
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    delivery_address VARCHAR(255) NULL,
    delivery_city VARCHAR(100) NULL,
    delivery_postal_code VARCHAR(20) NULL,
    delivery_country VARCHAR(80) NULL DEFAULT 'Portugal',
    delivery_maps_url VARCHAR(500) NULL,
    delivery_notes TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- TABELA ORDER_ITEMS
-- Guarda os produtos de cada encomenda.
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- UTILIZADOR ADMINISTRADOR PARA TESTE
-- Login: admin
-- Senha: admin123
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@seujorge.com', '$2y$12$KrmO5c4m37qd.p59H8rDmeg5lrrxk8MT8nfuJniPxxGGbxuCTvZ/C', 'admin')
ON DUPLICATE KEY UPDATE username = username;

-- CATEGORIAS INICIAIS DA LOJA
-- Estas categorias alimentam o dropdown do formulário de produtos.
INSERT INTO categories (name) VALUES
('roupa'),
('poster'),
('vinil'),
('Livro')
ON DUPLICATE KEY UPDATE name = name;

-- PRODUTOS INICIAIS DA LOJA
-- Produtos adaptados do ficheiro data/catalog.json do projeto original.
INSERT INTO products (name, description, price, stock, image, category) VALUES
('Blazer do Samba', 'Blazer Edição 2025 do Samba', 47.99, 10, 'picture/home/modelo/modelo-fundo-vermelho.jpg', 'roupa'),
('Conjunto Verão', 'Conjunto de verão - Seu Loiro', 37.99, 15, 'picture/home/slide/musica-brasileira.png', 'roupa'),
('Poster Edição Limitada', 'Poster exclusivo edição limitada', 15.00, 20, 'picture/home/slide/band.jpg', 'poster'),
('Vinil Álbum', 'Vinil clássico do artista', 30.00, 25, 'picture/home/slide/imagem-principal.jpg', 'vinil'),
('Álbum Musical ao vivo', 'Álbum ao vivo do artista', 13.99, 30, 'picture/home/slide/musica-brasileira.png', 'vinil'),
('Marighella - Livro', 'Livro sobre o movimento Marighella', 23.55, 20, 'picture/home/slide/musica-brasileira.png', 'Livro');
