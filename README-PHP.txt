CASO PRÁTICO PHP — WEBSITE SEU JORGE

1. INSTALAÇÃO LOCAL
- Colocar a pasta do projeto dentro de C:\xampp\htdocs\
- Ligar Apache e MySQL no XAMPP.
- Abrir http://localhost/phpmyadmin
- Criar uma base de dados chamada: seu_jorge_store
- Importar o ficheiro: database/seu_jorge_store.sql

2. PORTA DO MYSQL
O ficheiro de ligação está em: config/database.php
Se o MySQL estiver na porta 3307, deixar 3307.
Se estiver na porta padrão, alterar para 3306.

3. PÁGINA INICIAL
Abrir no navegador:
http://localhost/nome-da-pasta/index.php

4. ACESSO ADMINISTRATIVO
Utilizador: admin
Senha: admin123

5. PERCURSO PRINCIPAL DO ADMIN
Login -> Painel Admin -> Gerir Encomendas -> Detalhes da encomenda -> Guia de entrega / Gerar PDF

6. ORGANIZAÇÃO DO PROJETO
- admin/: páginas da área administrativa.
- actions/: ações PHP usadas por formulários e AJAX.
- config/: ligação à base de dados.
- database/: export SQL da base de dados.
- includes/: header, navbar, footer e funções reutilizáveis.
- css/: estilos do projeto.
- js/: JavaScript, AJAX e autocomplete de morada.
- picture/: imagens usadas no website.
- uploads/products/: imagens carregadas pelo admin nos produtos.
- documentacao/: checklist e análises do projeto.
- original-html/: backup da versão HTML inicial.

7. OBSERVAÇÃO SOBRE OS FICHEIROS HTML
Os ficheiros .html da raiz existem para cumprir a estrutura pedida e redirecionam para as versões finais em PHP.
