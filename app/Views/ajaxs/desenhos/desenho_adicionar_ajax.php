<script>

    function alert_certo(titulo, bory) {//cria um alerte verde no canto superior direto
        $(document).Toasts('create', {
            class: 'bg-success',
            title: titulo,
            subtitle: 'Subtitle',
            autohide: true,
            delay: 5000,
            body: bory
        });
    }
    function alert_personalizado(titulo, bory) {//cria um alerte vermelho no canto superior direto 
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: titulo,
            subtitle: 'Subtitle',
            autohide: true,
            delay: 13000,
            body: bory,
            encodeHTML: false
        });
    }


    lista_temp1 = "";
    function value_filtro(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/lita_filtro') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (response.lista != lista_temp1 || efeturar) {
                    // Obter referência ao elemento select
                    var desenho = document.getElementById("desenhos_add");
                    // Armazenar o valor da opção selecionada antes de limpar o select

                    desenho.accept = response.lista;

                    lista_temp1 = response.lista;
                }
            }
        });
    }
    // Executar função ao abrir o site
    document.addEventListener('DOMContentLoaded', value_filtro);

    // Repetir função a cada segundo 
    //setInterval(value_filtro, 15000);

    function adicionar() {
        var fileInput = document.getElementById('desenhos_add');
        var files = fileInput.files;

        if (files.length > 0) {
            $.ajax({
                url: '<?= base_url('public/desenhista/criar_temp') ?>',
                type: "POST",
                dataType: "json", // Indicar que o retorno é em formato JSON
                success: function (response) {

                    if (response.ok == 'true') {
                        var formData = new FormData();

                        for (var i = 0; i < files.length; i++) {
                            var file = fileInput.files[i];

                            if (file) {
                                var formData = new FormData();
                                formData.append('file', file);

                                $.ajax({
                                    url: '<?= site_url('public/desenhista/desenho_adicionar_temp') ?>',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: formData,
                                    processData: false,
                                    async: false, // Torna a solicitação síncrona
                                    contentType: false,
                                    success: function (response) {


                                    },
                                    error: function () {
                                        alert_personalizado("Desenho", 'Erro ao enviar o arquivo.');


                                    }
                                });
                            }

                        }
                        desenho_modal();

                    } else {
                        alert_personalizado("Desenho", 'erro ao criar pasta temp');
                    }





                }
            });

        } else {
            alert_personalizado('Desenho', 'Selecione um arquivo antes de adicioná lo.');
        }

    }





    desenhos = [];
    lista_array = [];
    function desenho_modal() {
        $.ajax({
            url: '<?= site_url('public/desenhista/desenho_adicionar_modal') ?>',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                desenhos = response.desenhos;
                lista_array = response;
                var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
                botao_confirmar_modal.innerHTML = "Confirmar";
                var modal_titulo = document.getElementById('modal_titulo');
                var modal_bory = document.getElementById('modal_bory');
                modal_bory.innerHTML = '';
                modal_titulo.textContent = "Adicionar desenho";
                tabel_bory = document.createElement("table");

                tabel_bory.setAttribute('border', '1');



                tr = document.createElement('tr');
                th = document.createElement('th');
                th.innerHTML = 'Nome';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Prioridade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Emepresa/Cliente';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Empreendimento';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Finalidade';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-01';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-02';
                tr.appendChild(th);
                th = document.createElement('th');
                th.innerHTML = 'Subpasta-03';
                tr.appendChild(th);
                tr.classList.add()
                tabel_bory.appendChild(tr);
                ////////////////////////////////////// começo 
                tr = document.createElement('tr');
                th = document.createElement('th');

                th.style.fontWeight = 'normal';
                th.innerHTML = '*';
                tr.appendChild(th);
                //selecte prioridade
                selectElement = document.createElement("select");
                selectElement.id = 'prioridade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th);//coloca o input name no modal



                //selecte empresa

                selectElement = document.createElement("select");
                selectElement.id = 'empresa_cliente_novo_todos';
                selectElement.addEventListener("change", function () {
                    var selectedValue = this.value; // Valor da opção selecionada
                    var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                    // Chame a função que deseja executar quando uma opção é selecionada
                    value_empreendimento_c(selectedValue, selectedIndex1, desenhos.length);

                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("empresa_cliente_novo_" + j)) {
                            document.getElementById("empresa_cliente_novo_" + j).selectedIndex = selectedIndex1;
                        }
                    }

                });
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                th.appendChild(selectElement);
                tr.appendChild(th);


                //coloca o input name no modal




                //selecte empreendimento
                selectElement = document.createElement("select");
                selectElement.id = 'empreendimento_novo_todos';
                selectElement.classList.add("custom-select");

                var novoOption = document.createElement("option");
                selectElement.disabled = true;
                novoOption.value = '';
                novoOption.textContent = 'Empreendimento';
                selectElement.appendChild(novoOption);

                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th);//coloca o input name no modal



                //selecte finalidade
                selectElement = document.createElement("select");
                selectElement.id = 'finalidade_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                th.appendChild(selectElement);
                tr.appendChild(th);//coloca o input name no modal



                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag1_novo_todos';

                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta()">+</button>';

                th.appendChild(div);

                tr.appendChild(th);//coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag2_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');
                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta()">+</button>';

                th.appendChild(div);


                tr.appendChild(th);//coloca o input name no modal

                //selecte tag
                selectElement = document.createElement("select");
                selectElement.id = 'tag3_novo_todos';
                selectElement.classList.add("custom-select");



                th = document.createElement('th');

                div = document.createElement('div');
                div.classList.add("container-mesma-linha");
                div.appendChild(selectElement);
                div.innerHTML += '<button name="cadastarar" type="submit" class="btn btn-outline-primary" onclick="adicinar_subpasta()">+</button>';

                th.appendChild(div);

                tr.appendChild(th);//coloca o input name no modal

                tabel_bory.appendChild(tr);





                for (i = 0; i < desenhos.length; i++) {

                    tr = document.createElement('tr');
                    tr.id = "desenho_" + i;
                    if (i % 2 == 0) {
                        tr.classList.add('odd');
                    } else {
                        tr.classList.add('even');
                    }


                    //nome
                    th = document.createElement('th');
                    th.style.fontWeight = 'normal';
                    th.style.fontSize = '16px';
                    th.innerHTML = desenhos[i];


                    tr.appendChild(th);








                    //selecte prioridade
                    selectElement = document.createElement("select");
                    selectElement.id = 'prioridade_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal


                    //selecte empresa
                    selectElement = document.createElement("select");
                    selectElement.id = 'empresa_cliente_novo_' + i;
                    selectElement.classList.add("custom-select");

                    th = document.createElement('th');
                    th.appendChild(selectElement);
                    tr.appendChild(th);


                    //coloca o input name no modal




                    //selecte empreendimento
                    selectElement = document.createElement("select");
                    selectElement.id = 'empreendimento_novo_' + i;
                    selectElement.classList.add("custom-select");

                    var novoOption = document.createElement("option");
                    selectElement.disabled = true;
                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';
                    selectElement.appendChild(novoOption);

                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal



                    //selecte finalidade
                    selectElement = document.createElement("select");
                    selectElement.id = 'finalidade_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal





                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag1_novo_' + i;

                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal

                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag2_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal

                    //selecte tag
                    selectElement = document.createElement("select");
                    selectElement.id = 'tag3_novo_' + i;
                    selectElement.classList.add("custom-select");



                    th = document.createElement('th');

                    th.appendChild(selectElement);
                    tr.appendChild(th);//coloca o input name no modal

                    tabel_bory.appendChild(tr);










                }

                tabel_bory.classList.add('table', 'table-bordered', 'table-striped');
                modal_bory.appendChild(tabel_bory);
                selects();
                mostrarModal();

            },
            error: function () {
                alert_personalizado('Desenho', 'Erro ao enviar o arquivo.');

            }
        });

    }
    function selects() {
        value_prioridade(true);
        value_tags(true);
        value_finalidade(true);
        value_empresa(true);

        value_empresa_c(true);
        value_finalidade_c(true);
        value_prioridade_c(true);
        value_tags_c(true);
    }
    function tag_ordem(selectedValue, selectedIndex, coluna, id) {
        if (coluna == '1' && selectedIndex == 0) {
            select = document.getElementById("tag2_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
        } else if (coluna == '2' && selectedIndex == 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.options[0].selected = true;
            select.disabled = true;
        }
        if (coluna == '1' && selectedIndex != 0) {
            select = document.getElementById("tag2_novo_" + id);
            select.disabled = false;
            select.options.disabled = false;

        }
        if (coluna == '2' && selectedIndex != 0) {
            select = document.getElementById("tag3_novo_" + id);
            select.disabled = false;
        }
        select3 = document.getElementById("tag3_novo_" + id);

        select2 = document.getElementById("tag2_novo_" + id);

        select1 = document.getElementById("tag1_novo_" + id);
        for (let index = 0; index < select1.length; index++) {
            select2.options[index].disabled = false;
            select3.options[index].disabled = false;
            select1.options[index].disabled = false;
        }
        if (select1.selectedIndex != 0) {
            select2.options[select1.selectedIndex].disabled = true;
            select3.options[select1.selectedIndex].disabled = true;
        }
        if (select2.selectedIndex != 0) {
            select1.options[select2.selectedIndex].disabled = true;
            select3.options[select2.selectedIndex].disabled = true;
        } if (select3.selectedIndex != 0) {
            select1.options[select3.selectedIndex].disabled = true;
            select2.options[select3.selectedIndex].disabled = true;
        }



    }












    function inverterCor(hex) {
        // Verificar se a cor é válida (começa com # seguido por 6 caracteres hexadecimais)
        var regex = /^#[0-9A-F]{6}$/i;
        if (!regex.test(hex)) {
            throw new Error("Cor inválida. Use notação hexadecimal de 6 dígitos, começando com '#'.");
        }

        // Extrair os componentes de cor
        var red = parseInt(hex.substr(1, 2), 16);
        var green = parseInt(hex.substr(3, 2), 16);
        var blue = parseInt(hex.substr(5, 2), 16);

        // Inverter os componentes de cor
        red = 255 - red;
        green = 255 - green;
        blue = 255 - blue;

        // Converter os valores invertidos de volta para notação hexadecimal
        var invertedHex = "#" + ((1 << 24) | (red << 16) | (green << 8) | blue).toString(16).slice(1);

        return invertedHex;
    }



    lista_temp2 = "";
    function value_prioridade(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_prioridade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("prioridade_novo_0") != null && (response.toString() != lista_temp2 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        if (document.getElementById("prioridade_novo_" + j)) {


                            // Obter referência ao elemento select
                            var funcao = document.getElementById("prioridade_novo_" + j);
                            // Armazenar o valor da opção selecionada antes de limpar o select
                            var valorSelecionadoAntes = funcao.value;

                            // Limpar o select
                            funcao.innerHTML = '';

                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = '';
                            novoOption.textContent = 'Prioridade';

                            // Adicionar o novo elemento option ao select
                            funcao.appendChild(novoOption);

                            response.lista.forEach(element => {



                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = element.prioridade;
                                novoOption.textContent = element.prioridade;
                                novoOption.style.backgroundColor = element.cor;
                                novoOption.style.color = inverterCor(element.cor);
                                funcao.appendChild(novoOption);
                            });
                            var opcoes = funcao.options;
                            for (var i = 0; i < opcoes.length; i++) {
                                if (opcoes[i].value === valorSelecionadoAntes) {
                                    opcoes[i].selected = true;
                                    break;
                                }
                            }

                        }
                    }
                    lista_temp2 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade, 15000);


    lista_temp3 = "";
    function value_tags(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/desenho_tag_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {

                if (document.getElementById("tag1_novo_0") != null && (response.toString() != lista_temp3 || efeturar)) {
                    for (let j = 0; j < desenhos.length; j++) {
                        valorSelecionadoAntes = '';
                        for (let h = 1; h < 4; h++) {
                            if (document.getElementById("tag" + h + "_novo_" + j)) {
                                // Obter referência ao elemento select
                                var funcao = document.getElementById("tag" + h + "_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select

                                if (h != 1 && valorSelecionadoAntes == '') {
                                    funcao.disabled = true;
                                }// Limpar o select
                                valorSelecionadoAntes = funcao.value;
                                funcao.innerHTML = '';
                                funcao.addEventListener("change", function () {
                                    var selectedValue = this.value; // Valor da opção selecionada
                                    var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                    // Chame a função que deseja executar quando uma opção é selecionada
                                    tag_ordem(selectedValue, selectedIndex, h, j);
                                });

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Subpasta';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                response.lista.forEach(element => {


                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element;
                                    novoOption.textContent = element;

                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }
                            }
                        }

                    }
                    lista_temp3 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags);

    // Repetir função a cada segundo 
    //setInterval(value_tags, 15000);

    var lista_temp4 = '';
    function value_finalidade(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_finalidade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("finalidade_novo_0") != null || efeturar) {
                    if (response.toString() != lista_temp4 || efeturar) {

                        lista_temp4 = response.toString();
                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("finalidade_novo_" + j)) {


                                // Obter referência ao elemento select
                                var funcao = document.getElementById("finalidade_novo_" + j);
                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = '';
                                novoOption.textContent = 'Finalidade';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.finalidade;
                                    novoOption.textContent = element.finalidade;
                                    funcao.appendChild(novoOption);
                                });
                                var opcoes = funcao.options;
                                for (var i = 0; i < opcoes.length; i++) {
                                    if (opcoes[i].value === valorSelecionadoAntes) {
                                        opcoes[i].selected = true;
                                        break;
                                    }
                                }

                            }
                        }

                    }
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade, 15000);

    lista_temp5 = "";
    function value_empresa(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_empresa_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("empresa_cliente_novo_0") != null && (response.toString() != lista_temp5 || efeturar)) {

                    for (let j = 0; j < desenhos.length; j++) {

                        if (document.getElementById("empresa_cliente_novo_" + j)) {

                            // Obter referência ao elemento select
                            var funcao = document.getElementById("empresa_cliente_novo_" + j);
                            // Armazenar o valor da opção selecionada antes de limpar o select
                            var valorSelecionadoAntes = funcao.value;

                            // Limpar o select
                            funcao.innerHTML = '';

                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = '';
                            novoOption.textContent = 'Empresa';

                            // Adicionar o novo elemento option ao select
                            funcao.appendChild(novoOption);
                            funcao.addEventListener("change", function () {
                                var selectedValue = this.value; // Valor da opção selecionada
                                var selectedIndex = this.selectedIndex; // Índice da opção selecionada

                                // Chame a função que deseja executar quando uma opção é selecionada
                                value_empreendimento(selectedValue, selectedIndex, j);
                            });
                            response.lista.forEach(element => {



                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option
                                novoOption.value = element.empresa;
                                novoOption.textContent = element.empresa;
                                funcao.appendChild(novoOption);
                            });
                            var opcoes = funcao.options;
                            for (var i = 0; i < opcoes.length; i++) {
                                if (opcoes[i].value === valorSelecionadoAntes) {
                                    opcoes[i].selected = true;
                                    break;
                                }
                            }

                        }
                    }
                    lista_temp5 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa);

    // Repetir função a cada segundo 
    //setInterval(value_empresa, 15000);
    function value_empreendimento_lista(selectedValue, selectedIndex, id, response) {

        $.ajax({
            url: '<?= base_url('public/desenhista/config_empreendimento_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { empresa: selectedValue },
            success: function (response) {

                if (document.getElementById("empreendimento_novo_0") != null) {

                    var funcao = document.getElementById("empreendimento_novo_" + id);
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }



                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empreendimento;
                        novoOption.textContent = element.empreendimento;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp6 = response.toString();
                }

            }
        });

    }
    function value_empreendimento(selectedValue, selectedIndex, id) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_empreendimento_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { empresa: selectedValue },
            success: function (response) {

                if (document.getElementById("empreendimento_novo_0") != null) {
                    if (document.getElementById("empreendimento_novo_" + id)) {
                        var funcao = document.getElementById("empreendimento_novo_" + id);
                        if (selectedIndex == 0) {
                            funcao.disabled = true;
                        } else {
                            funcao.disabled = false;
                        }



                        // Obter referência ao elemento select

                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option

                        novoOption.value = '';
                        novoOption.textContent = 'Empreendimento';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        var novoOption = document.createElement("option");

                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.empreendimento;
                            novoOption.textContent = element.empreendimento;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }

                    lista_temp6 = response.toString();
                }

            }
        });

    }

    function confirmarModal() {
        desenhos_enviar = [];
        i = 0;
        for (let j = 0; j < desenhos.length; j++) {
            if (desenhos[j] != null) {
                data = {
                    empresa: document.getElementById("empresa_cliente_novo_" + j).value,
                    empreendimento: document.getElementById("empreendimento_novo_" + j).value,
                    finalidade: document.getElementById("finalidade_novo_" + j).value,
                    prioridade: document.getElementById("prioridade_novo_" + j).value,
                    tag1: document.getElementById("tag1_novo_" + j).value,
                    tag2: document.getElementById("tag2_novo_" + j).value,
                    tag3: document.getElementById("tag3_novo_" + j).value,
                    desenho: desenhos[j]

                };
                desenhos_enviar[i] = data;
                i++;
            }
        }
        $.ajax({
            url: '<?= base_url('public/desenhista/desenhos_add') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { desenhos: desenhos_enviar },
            success: function (response) {

                ok1 = true;
                cont = 0;
                cont_rep = 0;
                mgs_final = [];
                var index = [];
                var mgs = response.msg;
                var ok = response.ok;
                for (const chave in mgs) {
                    cont_rep = 0;
                    cont1 = 0;

                    const valor = mgs[chave];
                    if (valor != null) {
                        for (const chave1 in mgs) {
                            if (mgs[chave1] == valor && chave1 != chave) {
                                mgs[chave1] = null;
                                ok[cont1] = null;
                                cont_rep++;
                            }
                            cont1++;
                        }

                        if (cont_rep != 0) {
                            mgs_final["O core em " + (cont_rep + 1) + " desenhos"] = valor;
                            index[chave] = cont;
                        } else {
                            mgs_final[chave] = valor;
                            index[chave] = cont;
                        }
                    }



                    cont++;

                }

                ok = ok.filter(item => item !== null);

                ok = Array.from(ok);


                cont = 0;
                cont_certo = 0;
                for (const chave in mgs_final) {
                    if (ok[cont]) {
                        index1 = 0;
                        for (let j = 0; j < desenhos.length; j++) {
                            if (desenhos[j] == chave) {
                                index1 = j;
                                break;
                            }
                        }
                        desenhos[index1] = null;

                        var node = document.getElementById("desenho_" + index1);
                        if (node.parentNode) {
                            node.parentNode.removeChild(node);
                        }
                        cont_certo++;

                    } else {
                        const valor = mgs_final[chave];
                        alert_personalizado(chave, valor);

                        ok1 = false;
                    }

                    cont++;
                }
                if (cont_certo != 0) {
                    alert_certo("Desenhos adicionados", "Ao total " + cont_certo + " desenhos foram adicionados.");
                }
                if (ok1) {
                    fecharModal();
                    // Cria um novo elemento de entrada de arquivo vazio
                    var newFileInput = $('<input type="file" id="desenhos_add" multiple data-multiple-caption="{count} files selected" class="inputfile">');

                    // Substitui o elemento de entrada de arquivo original com o novo elemento
                    $('#desenhos_add').replaceWith(newFileInput);
                    value_filtro(true);

                }
            }
        });
    }



    //lista cabeçario




    lista_temp_c5 = "";
    function value_empresa_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_empresa_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("empresa_cliente_novo_todos") != null && (response.toString() != lista_temp_c5 || efeturar)) {





                    // Obter referência ao elemento select
                    var funcao = document.getElementById("empresa_cliente_novo_todos");
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Empresa';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);


                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empresa;
                        novoOption.textContent = element.empresa;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp_c5 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_empresa_c);

    // Repetir função a cada segundo 
    //setInterval(value_empresa_c, 15000);






    function value_empreendimento_c(selectedValue, selectedIndex, index) {

        $.ajax({
            url: '<?= base_url('public/desenhista/config_empreendimento_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { empresa: selectedValue },
            success: function (response) {

                if (document.getElementById("empreendimento_novo_todos") != null) {

                    var funcao = document.getElementById("empreendimento_novo_todos");
                    if (selectedIndex == 0) {
                        funcao.disabled = true;
                    } else {
                        funcao.disabled = false;
                    }

                    funcao.addEventListener("change", function () {
                        var selectedValue1 = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("empresa_cliente_novo_" + j)) {
                                if (document.getElementById("empresa_cliente_novo_" + j).value == selectedValue) {
                                    document.getElementById("empreendimento_novo_" + j).selectedIndex = selectedIndex1;
                                }
                            }
                        }

                    });

                    // Obter referência ao elemento select

                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option

                    novoOption.value = '';
                    novoOption.textContent = 'Empreendimento';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);
                    var novoOption = document.createElement("option");

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.empreendimento;
                        novoOption.textContent = element.empreendimento;
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }
                    var opcoes1 = opcoes;



                    for (let id = 0; id < index; id++) {
                        if (document.getElementById("empreendimento_novo_0") != null) {
                            if (document.getElementById("empreendimento_novo_" + id)) {
                                var funcao = document.getElementById("empreendimento_novo_" + id);
                                if (selectedIndex == 0) {
                                    funcao.disabled = true;
                                } else {
                                    funcao.disabled = false;
                                }



                                // Obter referência ao elemento select

                                // Armazenar o valor da opção selecionada antes de limpar o select
                                var valorSelecionadoAntes = funcao.value;

                                // Limpar o select
                                funcao.innerHTML = '';

                                // Criar um novo elemento option
                                var novoOption = document.createElement("option");

                                // Definir o valor e texto do novo elemento option

                                novoOption.value = '';
                                novoOption.textContent = 'Empreendimento';

                                // Adicionar o novo elemento option ao select
                                funcao.appendChild(novoOption);
                                var novoOption = document.createElement("option");

                                response.lista.forEach(element => {



                                    // Criar um novo elemento option
                                    var novoOption = document.createElement("option");

                                    // Definir o valor e texto do novo elemento option
                                    novoOption.value = element.empreendimento;
                                    novoOption.textContent = element.empreendimento;
                                    funcao.appendChild(novoOption);
                                });
                                funcao.options = opcoes1;


                            }
                            lista_temp6 = response.toString();
                        }
                    }
                }
            }
        });

    }




    var lista_temp_c4 = '';
    function value_finalidade_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_finalidade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("finalidade_novo_todos") != null || efeturar) {
                    if (response.toString() != lista_temp_c4 || efeturar) {

                        lista_temp_c4 = response.toString();




                        // Obter referência ao elemento select
                        var funcao = document.getElementById("finalidade_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select
                        var valorSelecionadoAntes = funcao.value;

                        // Limpar o select
                        funcao.innerHTML = '';

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Finalidade';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                        funcao.addEventListener("change", function () {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                            // Chame a função que deseja executar quando uma opção é selecionada

                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("finalidade_novo_" + j)) {
                                    document.getElementById("finalidade_novo_" + j).selectedIndex = selectedIndex1;
                                }
                            }

                        });
                        response.lista.forEach(element => {



                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element.finalidade;
                            novoOption.textContent = element.finalidade;
                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }



                    }
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_finalidade_c);

    // Repetir função a cada segundo 
    //setInterval(value_finalidade_c, 15000);

    lista_temp_c2 = "";
    function value_prioridade_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/config_prioridade_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (document.getElementById("prioridade_novo_todos") != null && (response.toString() != lista_temp_c2 || efeturar)) {




                    // Obter referência ao elemento select
                    var funcao = document.getElementById("prioridade_novo_todos");

                    funcao.addEventListener("change", function () {
                        var selectedValue = this.value; // Valor da opção selecionada
                        var selectedIndex1 = this.selectedIndex; // Índice da opção selecionada

                        // Chame a função que deseja executar quando uma opção é selecionada

                        for (let j = 0; j < desenhos.length; j++) {
                            if (document.getElementById("prioridade_novo_" + j)) {
                                document.getElementById("prioridade_novo_" + j).selectedIndex = selectedIndex1;
                            }

                        }

                    });










                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Prioridade';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element.prioridade;
                        novoOption.textContent = element.prioridade;
                        novoOption.style.backgroundColor = element.cor;
                        novoOption.style.color = inverterCor(element.cor);
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp_c2 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_prioridade_c);

    // Repetir função a cada segundo 
    //setInterval(value_prioridade_c, 15000);


    lista_temp_c3 = "";
    function value_tags_c(efeturar = false) {
        $.ajax({
            url: '<?= base_url('public/desenhista/desenho_tag_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {

                if (document.getElementById("tag1_novo_0") != null && (response.toString() != lista_temp_c3 || efeturar)) {

                    valorSelecionadoAntes = '';
                    for (let h = 1; h < 4; h++) {

                        // Obter referência ao elemento selecttag2_novo_todos
                        var funcao = document.getElementById("tag" + h + "_novo_todos");
                        // Armazenar o valor da opção selecionada antes de limpar o select

                        if (h != 1 && valorSelecionadoAntes == '') {
                            funcao.disabled = true;
                        }// Limpar o select
                        valorSelecionadoAntes = funcao.value;
                        funcao.innerHTML = '';
                        funcao.addEventListener("change", function () {
                            var selectedValue = this.value; // Valor da opção selecionada
                            var selectedIndex = this.selectedIndex; // Índice da opção selecionada
                            tag_ordem(selectedValue, selectedIndex, h, 'todos');
                            // Chame a função que deseja executar quando uma opção é selecionada
                            for (let j = 0; j < desenhos.length; j++) {
                                if (document.getElementById("tag" + h + "_novo_" + j)) {
                                    document.getElementById("tag" + h + "_novo_" + j).selectedIndex = selectedIndex;
                                    tag_ordem(selectedValue, selectedIndex, h, j);
                                }
                            }
                        });

                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = '';
                        novoOption.textContent = 'Subpasta';

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);

                        response.lista.forEach(element => {


                            // Criar um novo elemento option
                            var novoOption = document.createElement("option");

                            // Definir o valor e texto do novo elemento option
                            novoOption.value = element;
                            novoOption.textContent = element;

                            funcao.appendChild(novoOption);
                        });
                        var opcoes = funcao.options;
                        for (var i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === valorSelecionadoAntes) {
                                opcoes[i].selected = true;
                                break;
                            }
                        }
                    }


                    lista_temp_c3 = response.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    //  document.addEventListener('DOMContentLoaded', value_tags_c);

    // Repetir função a cada segundo
    //setInterval(value_tags_c, 15000);
    function adicinar_subpasta() {
        // A função prompt exibe uma caixa de diálogo com uma caixa de texto
        var erro = "";

            var userInput = prompt(erro + "Digite o nome da nova subpasta:");
            if (userInput !== null) {
                var tag = userInput;

                $.ajax({
                    url: '<?= base_url('public/desenhista/desenho_tag_cadastro') ?>',
                    type: "POST",
                    dataType: "json",//Indicar que o retorno é em formato JSON
                    async: false, // Torna a solicitação síncrona
                    data: { tag: tag },
                    success: function (response) {
                        //Função a ser executada em caso de sucesso da solicitação AJAX.

                        if (!response.ok) {
                            //Se a resposta não indica sucesso, isso significa que ocorreu um erro no cadastramento da tag.

                            //A resposta contém mensagens de erro no formato de um objeto. O loop for percorre essas mensagens.
                            for (const chave in response.msg) {
                                const valor = response.msg[chave];
                                //Para cada mensagem de erro, exibe um alerta personalizado com a chave (nome do campo) e o valor (mensagem de erro).
                                alert_personalizado(chave, valor);
                            }
                        } else {
                            //Se a resposta indica sucesso, exibe um alerta informando que a "tag" foi cadastrada com sucesso.
                            alert_certo('Cadastrado', 'Tag cadastrado com sucesso.');
                            //Limpa o valor do campo de entrada para que o usuário possa inserir outra "tag".
                            document.getElementById("nome_tag_novo").value = '';
                            
                        }
                        

                    }
                });
                value_tags(true);
                value_tags_c(true);
            }







    }










</script>