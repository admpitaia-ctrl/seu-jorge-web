/*
    FICHEIRO: address-autocomplete.js

    OBJETIVO:
    Melhorar a experiência de preenchimento da morada.

    Enquanto o utilizador escreve o endereço, este script procura sugestões
    reais usando o serviço público Nominatim/OpenStreetMap.

    IMPORTANTE:
    - Não precisa de chave API.
    - Funciona como melhoria progressiva: se não houver internet, o formulário
      continua a funcionar normalmente com escrita manual e datalist.
    - Ao escolher uma sugestão, tentamos preencher cidade, código postal e país.
*/

(function(){

    const camposEndereco = document.querySelectorAll('[data-address-autocomplete="1"]');

    if(!camposEndereco.length){
        return;
    }

    function criarCaixaSugestoes(campo){
        const caixa = document.createElement('div');
        caixa.className = 'address-suggestions-box';
        caixa.setAttribute('role', 'listbox');

        campo.parentNode.insertBefore(caixa, campo.nextSibling);
        return caixa;
    }

    function limparCaixa(caixa){
        caixa.innerHTML = '';
        caixa.classList.remove('address-suggestions-active');
    }

    function obterCampo(formulario, nome){
        return formulario.querySelector('[data-address-field="' + nome + '"]') || formulario.querySelector('[name="' + nome + '"]');
    }

    function cidadeDoResultado(address){
        return address.city || address.town || address.village || address.municipality || address.county || '';
    }

    function preencherCampos(campo, resultado){
        const formulario = campo.closest('form');

        if(!formulario){
            campo.value = resultado.display_name || campo.value;
            return;
        }

        const address = resultado.address || {};
        const campoCidade = obterCampo(formulario, 'city');
        const campoPostal = obterCampo(formulario, 'postal_code');
        const campoPais = obterCampo(formulario, 'country');

        campo.value = resultado.display_name || campo.value;

        if(campoCidade && cidadeDoResultado(address)){
            campoCidade.value = cidadeDoResultado(address);
        }

        if(campoPostal && address.postcode){
            campoPostal.value = address.postcode;
        }

        if(campoPais && address.country){
            campoPais.value = address.country;
        }
    }

    function mostrarSugestoes(campo, caixa, resultados){
        limparCaixa(caixa);

        if(!resultados || resultados.length === 0){
            return;
        }

        resultados.slice(0, 6).forEach(function(resultado){
            const botao = document.createElement('button');
            botao.type = 'button';
            botao.className = 'address-suggestion-item';
            botao.textContent = resultado.display_name;

            botao.addEventListener('click', function(){
                preencherCampos(campo, resultado);
                limparCaixa(caixa);
                campo.classList.remove('campo-invalido');
            });

            caixa.appendChild(botao);
        });

        caixa.classList.add('address-suggestions-active');
    }

    function procurarEndereco(campo, caixa){
        const texto = campo.value.trim();

        if(texto.length < 4){
            limparCaixa(caixa);
            return;
        }

        const url = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=6&countrycodes=pt&q=' + encodeURIComponent(texto);

        fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function(resposta){
            return resposta.json();
        })
        .then(function(resultados){
            mostrarSugestoes(campo, caixa, resultados);
        })
        .catch(function(){
            /*
                Se estiver sem internet ou se o serviço externo falhar,
                não bloqueamos o utilizador. Ele continua podendo escrever manualmente.
            */
            limparCaixa(caixa);
        });
    }

    camposEndereco.forEach(function(campo){
        const caixa = criarCaixaSugestoes(campo);
        let temporizador = null;

        campo.setAttribute('autocomplete', 'street-address');

        campo.addEventListener('input', function(){
            clearTimeout(temporizador);
            temporizador = setTimeout(function(){
                procurarEndereco(campo, caixa);
            }, 450);
        });

        campo.addEventListener('blur', function(){
            setTimeout(function(){
                limparCaixa(caixa);
            }, 220);
        });
    });

})();
