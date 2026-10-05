-- ATUALIZAÇÃO: categorias para dropdown e upload de imagem
-- Use este ficheiro apenas se a base seu_jorge_store já existir.

USE seu_jorge_store;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL UNIQUE
);

INSERT IGNORE INTO categories (name)
SELECT DISTINCT category
FROM products
WHERE category IS NOT NULL AND category <> '';
