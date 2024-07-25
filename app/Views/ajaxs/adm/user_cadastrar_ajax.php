<script>

    document.getElementById("nome_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 17;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });

    document.getElementById("senha_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 50;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });
    document.getElementById("email_usuario").addEventListener("input", function () {
        var input = this;
        var maxLength = 50;
        var valor = input.value;
        input.value = valor.slice(0, maxLength); // Trunca o valor para o tamanho máximo 

    });



    lista_temp = "";
    function value_funcao() {
        $.ajax({
            url: '<?= base_url('public/adm/lita_funcao') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
                if (response.lista.toString() != lista_temp) {
                    // Obter referência ao elemento select
                    var funcao = document.getElementById("funcao_usuario");
                    // Armazenar o valor da opção selecionada antes de limpar o select
                    var valorSelecionadoAntes = funcao.value;

                    // Limpar o select
                    funcao.innerHTML = '';

                    // Criar um novo elemento option
                    var novoOption = document.createElement("option");

                    // Definir o valor e texto do novo elemento option
                    novoOption.value = '';
                    novoOption.textContent = 'Função do novo usuario';

                    // Adicionar o novo elemento option ao select
                    funcao.appendChild(novoOption);

                    response.lista.forEach(element => {



                        // Criar um novo elemento option
                        var novoOption = document.createElement("option");

                        // Definir o valor e texto do novo elemento option
                        novoOption.value = element;
                        novoOption.textContent = element;

                        // Adicionar o novo elemento option ao select
                        funcao.appendChild(novoOption);
                    });
                    var opcoes = funcao.options;
                    for (var i = 0; i < opcoes.length; i++) {
                        if (opcoes[i].value === valorSelecionadoAntes) {
                            opcoes[i].selected = true;
                            break;
                        }
                    }


                    lista_temp = response.lista.toString();
                }
            }
        });
    }
    // Executar função ao abrir o site
    document.addEventListener('DOMContentLoaded', value_funcao);

    // Repetir função a cada segundo 
    setInterval(value_funcao, 1000);


    
    function cadastrar() {//faz o cadastro do furmalho no banco de dados e mostra a mensagem de retorno
        var nome = document.getElementById("nome_usuario").value;
        var senha = document.getElementById("senha_usuario").value;
        var funcao = document.getElementById("funcao_usuario").value;
        var email = document.getElementById("email_usuario").value;
        var whazapp = document.getElementById("whazapp_usuario").value;
        $.ajax({
            url: '<?= base_url('public/adm/user_cadastrar') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { nome: nome, senha: senha, funcao: funcao, email: email, whazapp: whazapp },
            success: function (response) {

                if (!response.ok) {
                    //response.msg
                    for (const chave in response.msg) {
                        const valor = response.msg[chave];
                        alert_personalizado(chave, valor);
                    }
                }else{
                    alert_certo('Cadastrado','Usuário cadastrado com sucesso.');
                    document.getElementById("nome_usuario").value = '';
                    document.getElementById("senha_usuario").value ='';
                    document.getElementById("funcao_usuario").value = '';
                    document.getElementById("email_usuario").value = '';
                    document.getElementById("whazapp_usuario").value = '';
                    
                }

            }
        });
    }











    function alert_certo(titulo, bory){
        $(document).Toasts('create', {
        class: 'bg-success',
        title: titulo,
        subtitle: 'Subtitle',
        autohide: true,
        delay: 5000,
        body: bory
      });
    }




    function alert_personalizado(titulo, bory) {
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: titulo,
            subtitle: 'Subtitle',
            autohide: true,
            delay: 13000,
            body: bory
        });
    }



</script>