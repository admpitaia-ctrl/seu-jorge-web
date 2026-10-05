-- ATUALIZAÇÃO DOS ESTADOS DAS ENCOMENDAS
-- Objetivo: trocar o modelo antigo pending/completed por Pendente/A caminho/Entregue.

USE seu_jorge_store;

UPDATE orders SET status = 'delivered' WHERE status = 'completed';

ALTER TABLE orders
MODIFY status ENUM('pending', 'in_transit', 'delivered') DEFAULT 'pending';
