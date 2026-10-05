/* ---------------*/
/*    CAROUSEL    */
/* ---------------*/

/*
Inicializa o carousel apenas se ele existir
na página. Isso evita erro em outras páginas
do site que não possuem o carousel.
*/

var heroCarousel = document.getElementById('heroCarousel');

if(heroCarousel){

var carousel = new bootstrap.Carousel(heroCarousel, {
interval:3000,
ride:"carousel"
});

}


/* ---------------*/
/* ARTIST SECTION */
/* ---------------*/

/*
Este script ativa a animação da linha dourada
quando a seção aparece no ecrã durante o scroll.
*/

var section = document.querySelector(".artist-section");

/*
Verificação de segurança:
se a seção não existir, o script não executa
(evita erro em outras páginas).
*/

if(section){

window.addEventListener("scroll", function(){

var position = section.getBoundingClientRect().top;

var screenHeight = window.innerHeight;

/*
Quando a seção entra na área visível
da tela, adiciona a classe "active".
*/

if(position < screenHeight - 100){

section.classList.add("active");

}

});

}

/* ---------------------------*/
/* PAGINA SOBRE - TRAJETORIA */
/* ---------------------------*/

window.addEventListener("scroll", function(){

const items = document.querySelectorAll(".career-item");

items.forEach(function(item){

const position = item.getBoundingClientRect().top;
const screen = window.innerHeight;

if(position < screen - 100){
item.classList.add("active");
}

});

});

/* =============================== */
/* CINEMA VIDEO INTERACTION */
/* =============================== */

const cinemaCards = document.querySelectorAll(".cinema-card");

cinemaCards.forEach(card => {

const video = card.querySelector("video");
const poster = card.querySelector(".film-poster");

/* HOVER (DESKTOP) */

card.addEventListener("mouseenter", () => {

if(video){

video.style.opacity = "1";
poster.style.opacity = "0";

video.play();

}

});

card.addEventListener("mouseleave", () => {

if(video){

video.pause();
video.currentTime = 0;

video.style.opacity = "0";
poster.style.opacity = "1";

}

});

/* CLICK (MOBILE) */

card.addEventListener("click", () => {

if(video){

if(video.style.opacity === "1"){

video.pause();
video.currentTime = 0;

video.style.opacity = "0";
poster.style.opacity = "1";

}else{

video.style.opacity = "1";
poster.style.opacity = "0";

video.play();

}

}

});

});

/* =============================== */
/* PAGINA SOBRE - CITAÇÃO ARTÍSTICA */
/* ===============================*/

const quote = document.querySelector(".reveal-quote");

window.addEventListener("scroll", function(){

if(!quote) return;

const position = quote.getBoundingClientRect().top;
const screenHeight = window.innerHeight;

if(position < screenHeight - 120){

quote.classList.add("active");

}

});


/* =============================== */
/* PAGINA TOUR - MAPA INTERATIVO TOUR */
/* =============================== */

const cities = document.querySelectorAll(".tour-map-nav span");

const map = document.querySelector(".tour-map iframe");

cities.forEach(city => {

city.addEventListener("click", () => {

map.src = city.dataset.map;

});

});


/* ================================= */
/* PAGINA CONTACTO - FORM VALIDATION */
/* ================================= */

/*
    OBJETIVO:
    Fazer uma validação mais cuidadosa no formulário de contacto.

    REGRAS:
    - Nome e apelido precisam ter pelo menos 3 letras.
    - Email precisa ter formato válido.
    - Telefone precisa ter formato válido para contacto em Portugal.
    - Data de nascimento precisa indicar idade mínima de 18 anos.
    - Mensagem precisa ter pelo menos 10 caracteres.

    IMPORTANTE:
    Usamos setCustomValidity para manter o balão/aviso padrão do navegador,
    mas com mensagens personalizadas no modelo do projeto.
*/

const form = document.getElementById("contactForm");
const message = document.getElementById("formMessage");

if(form && message){

function calcularIdade(dataNascimento){

    const hoje = new Date();
    const nascimentoData = new Date(dataNascimento);

    if(isNaN(nascimentoData.getTime())){
        return -1;
    }

    let idade = hoje.getFullYear() - nascimentoData.getFullYear();
    const mes = hoje.getMonth() - nascimentoData.getMonth();

    if(mes < 0 || (mes === 0 && hoje.getDate() < nascimentoData.getDate())){
        idade--;
    }

    return idade;
}

function contarLetras(valor){
    const letras = valor.match(/[A-Za-zÀ-ÿ]/g);
    return letras ? letras.length : 0;
}

function limparTelefone(valor){
    return valor.replace(/[\s().-]/g, "");
}

function obterFeedback(campo){
    return form.querySelector('[data-feedback-for="' + campo.id + '"]');
}

function escreverFeedback(campo, texto, tipo){

    const feedback = obterFeedback(campo);

    if(!feedback) return;

    feedback.textContent = texto;
    feedback.classList.remove("field-feedback-error", "field-feedback-success", "field-feedback-active");

    if(texto !== ""){
        feedback.classList.add("field-feedback-active");
        feedback.classList.add(tipo === "sucesso" ? "field-feedback-success" : "field-feedback-error");
    }
}

function marcarCampo(campo, valido, mensagemErro, mensagemSucesso, mostrarMensagem){

    campo.setCustomValidity(valido ? "" : mensagemErro);

    campo.classList.remove("campo-obrigatorio-vazio", "campo-obrigatorio-correto");

    if(campo.value.trim() === "" && !mostrarMensagem){
        escreverFeedback(campo, "", "erro");
        return valido;
    }

    if(valido){
        campo.classList.add("campo-obrigatorio-correto");
        escreverFeedback(campo, mensagemSucesso, "sucesso");
    }else{
        campo.classList.add("campo-obrigatorio-vazio");
        escreverFeedback(campo, mensagemErro, "erro");
    }

    return valido;
}

function validarCampoContacto(campo, mostrarMensagem = true){

    const valor = campo.value.trim();

    if(campo.id === "nome"){
        if(valor === "") return marcarCampo(campo, false, "Preencha o nome.", "", mostrarMensagem);
        if(contarLetras(valor) < 3) return marcarCampo(campo, false, "O nome deve ter pelo menos 3 letras.", "Nome válido.", mostrarMensagem);
        return marcarCampo(campo, true, "", "Nome válido.", mostrarMensagem);
    }

    if(campo.id === "apelido"){
        if(valor === "") return marcarCampo(campo, false, "Preencha o apelido.", "", mostrarMensagem);
        if(contarLetras(valor) < 3) return marcarCampo(campo, false, "O apelido deve ter pelo menos 3 letras.", "Apelido válido.", mostrarMensagem);
        return marcarCampo(campo, true, "", "Apelido válido.", mostrarMensagem);
    }

    if(campo.id === "nascimento"){
        if(valor === "") return marcarCampo(campo, false, "Preencha a data de nascimento. A idade mínima é de 18 anos.", "", mostrarMensagem);

        const idade = calcularIdade(valor);

        if(idade < 18){
            return marcarCampo(campo, false, "A idade mínima para enviar o formulário é 18 anos.", "Idade válida.", mostrarMensagem);
        }

        return marcarCampo(campo, true, "", "Idade válida.", mostrarMensagem);
    }

    if(campo.id === "email"){
        campo.setCustomValidity("");

        if(valor === "") return marcarCampo(campo, false, "Preencha o email.", "", mostrarMensagem);
        if(!campo.checkValidity()) return marcarCampo(campo, false, "Insira um email válido. Exemplo: nome@email.com", "Email válido.", mostrarMensagem);

        return marcarCampo(campo, true, "", "Email válido.", mostrarMensagem);
    }

    if(campo.id === "telefone"){
        if(valor === "") return marcarCampo(campo, false, "Preencha o telefone.", "", mostrarMensagem);

        const telefoneLimpo = limparTelefone(valor);
        const telefoneValido = /^(\+351)?(2\d{8}|9\d{8})$/.test(telefoneLimpo);

        if(!telefoneValido){
            return marcarCampo(campo, false, "Insira um telefone válido. Exemplo: 912345678 ou +351912345678.", "Telefone válido.", mostrarMensagem);
        }

        return marcarCampo(campo, true, "", "Telefone válido.", mostrarMensagem);
    }

    if(campo.id === "mensagem"){
        if(valor === "") return marcarCampo(campo, false, "Escreva a mensagem.", "", mostrarMensagem);
        if(valor.length < 10) return marcarCampo(campo, false, "A mensagem deve ter pelo menos 10 caracteres.", "Mensagem válida.", mostrarMensagem);
        return marcarCampo(campo, true, "", "Mensagem válida.", mostrarMensagem);
    }

    return campo.checkValidity();
}

function mostrarMensagemContacto(texto, tipo){

    message.textContent = texto;
    message.classList.remove("sucesso", "erro", "contact-feedback-active");
    message.classList.add(tipo);

    setTimeout(function(){
        message.classList.add("contact-feedback-active");
    }, 20);
}

const camposContacto = form.querySelectorAll("input[required], textarea[required]");

camposContacto.forEach(function(campo){

    campo.addEventListener("input", function(){
        validarCampoContacto(campo, true);
    });

    campo.addEventListener("blur", function(){
        validarCampoContacto(campo, true);
    });

    campo.addEventListener("invalid", function(){
        validarCampoContacto(campo, true);
    });

});

form.addEventListener("submit", function(e){

    e.preventDefault();

    let formularioValido = true;
    let primeiroCampoInvalido = null;

    camposContacto.forEach(function(campo){
        const campoValido = validarCampoContacto(campo, true);

        if(!campoValido && !primeiroCampoInvalido){
            primeiroCampoInvalido = campo;
        }

        if(!campoValido){
            formularioValido = false;
        }
    });

    if(!formularioValido){
        mostrarMensagemContacto("Verifique os campos assinalados antes de enviar a mensagem.", "erro");

        if(primeiroCampoInvalido){
            primeiroCampoInvalido.focus();
            primeiroCampoInvalido.reportValidity();
        }

        return;
    }

    mostrarMensagemContacto("Mensagem enviada com sucesso. Obrigado pelo contacto!", "sucesso");

    form.reset();

    form.querySelectorAll(".campo-obrigatorio-correto, .campo-obrigatorio-vazio").forEach(function(campo){
        campo.classList.remove("campo-obrigatorio-correto", "campo-obrigatorio-vazio");
    });

    form.querySelectorAll(".field-feedback").forEach(function(feedback){
        feedback.textContent = "";
        feedback.classList.remove("field-feedback-active", "field-feedback-error", "field-feedback-success");
    });

});

}

/* =============================== */
/* SUAVIDADE FADE NAS IMAGENS - BODY */
/* =============================== */


const elements = document.querySelectorAll(".fade-in");

window.addEventListener("scroll", () => {

elements.forEach(el => {

const position = el.getBoundingClientRect().top;
const screen = window.innerHeight;

if(position < screen - 100){
el.classList.add("show");
}

});

});


/* --------------------------------------------*/
/* CARRINHO GLOBAL */
/* --------------------------------------------*/

let carrinho = JSON.parse(localStorage.getItem("carrinho")) || [];

const contadorCarrinho = document.getElementById("contador-carrinho");
const listaCarrinho = document.getElementById("lista-carrinho");
const totalCarrinho = document.getElementById("total-carrinho");
const toast = document.getElementById("toast");

/* --------------------------------------------*/
/*       PAGINAÇÃO - BOTÃO VER MAIS            */
/* --------------------------------------------*/

let limite = 9;
let produtosAtuais = [];

/* --------------------------------------------*/
/* RENDER CARRINHO */
/* --------------------------------------------*/

function renderCarrinho(){

    if(!listaCarrinho || !totalCarrinho) return;

    listaCarrinho.innerHTML = "";

    let total = 0;

    carrinho.forEach((item, index) => {

        const li = document.createElement("li");

        li.innerHTML = `
            <div class="carrinho-item d-flex justify-content-between align-items-center">

                <div class="carrinho-info">
                    <strong>${item.nome}</strong><br>

                    <div class="d-flex align-items-center gap-2 mt-1">
                        <button class="btn btn-sm btn-outline-light btn-menos">-</button>
                        <span>${item.quantidade}</span>
                        <button class="btn btn-sm btn-outline-light btn-mais">+</button>
                    </div>
                </div>

                <div class="carrinho-actions text-end">
                    <div>€${(item.preco * item.quantidade).toFixed(2)}</div>

                    <div class="mt-2 d-flex gap-2 justify-content-end">
                        <button class="btn btn-sm btn-outline-light btn-detalhes-carrinho">Ver detalhes</button>
                        <button class="btn btn-sm btn-outline-danger">Remover</button>
                    </div>
                </div>

            </div>
        `;

        const btnRemover = li.querySelector(".btn-outline-danger");
        const btnMais = li.querySelector(".btn-mais");
        const btnMenos = li.querySelector(".btn-menos");
        const btnDetalhes = li.querySelector(".btn-detalhes-carrinho");

        btnMais.addEventListener("click", () => {
            carrinho[index].quantidade++;
            salvarCarrinho();
            renderCarrinho();
            atualizarContador();
        });

        btnMenos.addEventListener("click", () => {
            if(carrinho[index].quantidade > 1){
                carrinho[index].quantidade--;
            } else {
                carrinho.splice(index, 1);
            }
            salvarCarrinho();
            renderCarrinho();
            atualizarContador();
        });

        btnRemover.addEventListener("click", () => {
            carrinho.splice(index, 1);
            salvarCarrinho();
            renderCarrinho();
            atualizarContador();
        });

        btnDetalhes.addEventListener("click", () => {
            const lightbox = document.getElementById("lightbox");

            if(!lightbox) return;

            lightbox.style.display = "flex";

            document.getElementById("lightbox-title").textContent = item.nome;
            document.getElementById("lightbox-price").textContent = "€" + item.preco;
            document.getElementById("lightbox-description").textContent = item.descricao;
            document.getElementById("lightbox-image").src = item.imagem;

            document.body.style.overflow = "hidden";
        });

        listaCarrinho.appendChild(li);
        total += item.preco * item.quantidade;
    });

    totalCarrinho.textContent = "Total: €" + total.toFixed(2);
}

/* --------------------------------------------*/
/* UTIL */
/* --------------------------------------------*/

function salvarCarrinho(){
    localStorage.setItem("carrinho", JSON.stringify(carrinho));
}

function mostrarToast(){
    if(!toast) return;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2000);
}

function atualizarContador(){
    if(!contadorCarrinho) return;

    let totalItens = 0;
    carrinho.forEach(item => totalItens += item.quantidade);

    contadorCarrinho.textContent = totalItens;
}

/* --------------------------------------------*/
/* LOJA */
/* --------------------------------------------*/

const lista = document.getElementById("lista-produtos");
const filtro = document.getElementById("filtro-categoria");
const busca = document.getElementById("busca-produto");
const lightbox = document.getElementById("lightbox");
const btnVerMais = document.getElementById("btn-ver-mais");

if(lista && filtro){

    fetch('data/catalog.json')
    .then(res => res.json())
    .then(dados => {

        const produtos = dados.products;

        /* --------------------------------------------*/
        /* MOSTRAR PRODUTOS */
        /* --------------------------------------------*/

        function mostrarProdutos(listaProdutos){

            lista.innerHTML = "";

            if(listaProdutos.length === 0){
                lista.innerHTML = `<p style="color:#aaa;">❌ Nenhum produto encontrado</p>`;
                return;
            }

            listaProdutos.forEach(produto => {

                const div = document.createElement("div");
                div.classList.add("col-md-4");

                div.innerHTML = `
                    <div class="card p-3">
                        <h5>${produto.nome}</h5>
                        <p>Preço: €${produto.preco}</p>

                        <div class="d-flex gap-2 mt-2">

                            <button class="btn btn-dark btn-detalhes"
                                data-nome="${produto.nome}"
                                data-preco="${produto.preco}"
                                data-descricao="${produto.descricao}"
                                data-imagem="${produto.imagem}">
                                Ver detalhes
                            </button>

                            <button class="btn btn-success btn-add"
                                data-nome="${produto.nome}"
                                data-preco="${produto.preco}"
                                data-imagem="${produto.imagem}"
                                data-descricao="${produto.descricao}">
                                🛒
                            </button>

                        </div>
                    </div>
                `;

                lista.appendChild(div);

                const btnDetalhes = div.querySelector(".btn-detalhes");
                const btnAdd = div.querySelector(".btn-add");

                btnDetalhes.addEventListener("click", function(){
                    lightbox.style.display = "flex";

                    document.getElementById("lightbox-title").textContent = this.dataset.nome;
                    document.getElementById("lightbox-price").textContent = "€" + this.dataset.preco;
                    document.getElementById("lightbox-description").textContent = this.dataset.descricao;
                    document.getElementById("lightbox-image").src = this.dataset.imagem;
                });

                btnAdd.addEventListener("click", function(){

                    const item = {
                        nome: this.dataset.nome,
                        preco: parseFloat(this.dataset.preco),
                        quantidade: 1,
                        imagem: this.dataset.imagem,
                        descricao: this.dataset.descricao
                    };

                    const itemExistente = carrinho.find(p => p.nome === item.nome);

                    if(itemExistente){
                        itemExistente.quantidade++;
                    } else {
                        carrinho.push(item);
                    }

                    salvarCarrinho();
                    renderCarrinho();
                    atualizarContador();
                    mostrarToast();
                });

            });
        }

        /* --------------------------------------------*/
        /* FILTRO + BUSCA */
        /* --------------------------------------------*/

        function aplicarFiltros(){

            const categoriaSelecionada = filtro.value;
            const texto = busca.value.toLowerCase();

            const filtrados = produtos.filter(p => {
                return (categoriaSelecionada === "" || p.categoria === categoriaSelecionada)
                    && p.nome.toLowerCase().includes(texto);
            });

            produtosAtuais = filtrados;
            limite = 9;

            mostrarProdutos(produtosAtuais.slice(0, limite));

            if(btnVerMais){
                btnVerMais.style.display = produtosAtuais.length > limite ? "block" : "none";
            }
        }

        /* --------------------------------------------*/
        /* CATEGORIAS */
        /* --------------------------------------------*/

        const categorias = [...new Set(produtos.map(p => p.categoria))];

        categorias.forEach(cat => {
            const option = document.createElement("option");
            option.value = cat;
            option.textContent = cat;
            filtro.appendChild(option);
        });

        /* --------------------------------------------*/
        /* EVENTOS */
        /* --------------------------------------------*/

        filtro.addEventListener("change", aplicarFiltros);
        busca.addEventListener("input", aplicarFiltros);

        if(btnVerMais){
            btnVerMais.addEventListener("click", () => {
                limite += 9;
                mostrarProdutos(produtosAtuais.slice(0, limite));

                if(limite >= produtosAtuais.length){
                    btnVerMais.style.display = "none";
                }
            });
        }

        /* INIT */
        produtosAtuais = produtos;
        mostrarProdutos(produtos.slice(0, limite));
        renderCarrinho();
        atualizarContador();

    });
}

/* --------------------------------------------*/
/* BOTÃO COMPRAR */
/* --------------------------------------------*/

const btnComprar = document.getElementById("btn-comprar");

if(btnComprar){
    btnComprar.addEventListener("click", function(){

        if(carrinho.length === 0){
            alert("Carrinho vazio!");
            return;
        }

        alert("Compra finalizada com sucesso!");

        carrinho = [];
        salvarCarrinho();
        renderCarrinho();
        atualizarContador();
    });
}

/* --------------------------------------------*/
/* LIGHTBOX */
/* --------------------------------------------*/

const closeBtn = document.querySelector(".close-btn");

if(closeBtn){
    closeBtn.addEventListener("click", () => {
        lightbox.style.display = "none";
        document.body.style.overflow = "auto";
    });
}

if(lightbox){
    lightbox.addEventListener("click", (e) => {
        if(e.target === lightbox){
            lightbox.style.display = "none";
            document.body.style.overflow = "auto";
        }
    });
}
/* ========================================= */
/* FORMULÁRIOS - VALIDAÇÃO VISUAL GERAL      */
/* ========================================= */

/*
    OBJETIVO:
    Dar feedback visual nos campos obrigatórios sem retirar o aviso padrão do navegador.

    REGRA IMPORTANTE:
    - O verde só aparece quando o campo está realmente válido.
    - Campo vazio não recebe verde.
    - Campo preenchido incorretamente recebe vermelho.
*/

function campoEstaVazio(campo){

    if(campo.type === "file"){
        return campo.required && campo.files.length === 0;
    }

    if(campo.tagName === "SELECT"){
        return campo.required && campo.value === "";
    }

    return campo.required && campo.value.trim() === "";
}

function campoTemConteudo(campo){

    if(campo.type === "file"){
        return campo.files && campo.files.length > 0;
    }

    if(campo.tagName === "SELECT"){
        return campo.value !== "";
    }

    return campo.value.trim() !== "";
}

function marcarCampoObrigatorio(campo, mostrarVazio = false){

    campo.classList.remove("campo-obrigatorio-vazio", "campo-obrigatorio-correto");

    if(campoEstaVazio(campo)){
        if(mostrarVazio){
            campo.classList.add("campo-obrigatorio-vazio");
        }
        return;
    }

    if(campo.checkValidity()){
        campo.classList.add("campo-obrigatorio-correto");
    }else if(campoTemConteudo(campo)){
        campo.classList.add("campo-obrigatorio-vazio");
    }
}

const camposObrigatorios = document.querySelectorAll("input[required], textarea[required], select[required]");

camposObrigatorios.forEach(function(campo){

    campo.addEventListener("invalid", function(){
        marcarCampoObrigatorio(campo, true);
    });

    campo.addEventListener("input", function(){
        marcarCampoObrigatorio(campo, false);
    });

    campo.addEventListener("change", function(){
        marcarCampoObrigatorio(campo, false);
    });

    campo.addEventListener("blur", function(){
        marcarCampoObrigatorio(campo, true);
    });

});

const formulariosComValidacao = document.querySelectorAll("form");

formulariosComValidacao.forEach(function(formulario){

    formulario.addEventListener("submit", function(){

        const camposDoFormulario = formulario.querySelectorAll("input[required], textarea[required], select[required]");

        camposDoFormulario.forEach(function(campo){
            marcarCampoObrigatorio(campo, true);
        });

    });

});
