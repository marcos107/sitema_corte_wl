<script>

  lista_temp = "";


  <?php $processo = "tag"; ?>
  function desativar(id) {
    //Função para desativar um item com base em seu ID.

    //Parâmetros:
    //- id: O ID do item a ser desativado.
    $.ajax({
      url: '<?= base_url('public/troca_status/' . $processo . '/desativado') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        lista();//Chama a função que atualiza-la.
      }
    });
  }

  function ativar(id) {
    //Função para ativa um item com base em seu ID.

    //Parâmetros:
    //- id: O ID do item a ser desativado.

    $.ajax({
      url: '<?= base_url('public/troca_status/' . $processo . '/ativo') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        lista();//Chama a função que atualiza-la.

      }
    });
  }





  function lista() {
    //Esta função é usada para atualizar uma lista na interface do usuário com base em seleções de checkboxes.

    //Obtém os valores dos checkboxes de ativos e desativados.
    var ativos = document.getElementById('checkbox_ativos').checked;
    var desativados = document.getElementById('checkbox_desativado').checked;
    $.ajax({
      url: '<?= base_url('public/desenho_tag') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { ativos: ativos, desativados: desativados },
      success: function (response) {
        //Função a ser executada em caso de sucesso da solicitação AJAX.

        //Verifica se a lista na resposta é diferente da lista temporária.
        if (response.lista != lista_temp) {
          //Destroi a tabela DataTable existente.
          $('#example1').DataTable().destroy();







          //Recarrega a tabela com a nova lista.
          var div = $('#minhaDiv');

          div.load(location.href + ' #minhaDiv');
          //Selecione o elemento <tbody> pelo seu ID
          var lista = document.getElementById('lista');
          //Substitua o conteúdo do elemento <tbody> com o novo HTML
          lista.innerHTML = response.lista;

          //Recria e configura a tabela DataTable com os novos dados.
          $(function () {
            $("#example1").DataTable({

              "responsive": true, "lengthChange": false, "autoWidth": false,
              "buttons": ["colvis"],
              "language": {
                "decimal": "",
                "emptyTable": "Sem dados disponíveis",
                "infoEmpty": "Mostrando de 0 até 0 de 0 registos",
                "infoFiltered": "(filtrado de MAX registos no total)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": " MENU",
                "loadingRecords": "A carregar dados...",
                "processing": "A processar...",
                "search": "Buscar:",
                "zeroRecords": "Não foram encontrados resultados",
                "paginate": {
                  "first": "Primeiro",
                  "last": "Último",
                  "next": "Seguinte",
                  "previous": "Anterior"

                },
                "aria": {
                  "sortAscending": ": clique para ordenar ascendente (ASC)",
                  "sortDescending": ": clique para ordenar descendente (DESC)"
                }
              }

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

          });



          lista_temp = response.lista;
        }
      }
    });
  }


  //Executar função ao abrir o site
  document.addEventListener('DOMContentLoaded', lista);

  //Repetir função a cada segundo
  //setInterval(lista, 1000);

  function cadastrar() {
    //Esta função é usada para cadastrar uma nova "tag".

    //Obtém o valor da tag a partir do elemento com o ID "nome_tag_novo".
    var tag = document.getElementById("nome_tag_novo").value;
    var empreendimento = document.getElementById("empreendimento_tag_novo").value;
    var finalidade = document.getElementById("finalidade_tag_novo").value;

    $.ajax({
      url: '<?= base_url('public/desenho_tag_cadastro') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { tag: tag , empreendimento: empreendimento , finalidade: finalidade},
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
          lista();
          //Se a resposta indica sucesso, exibe um alerta informando que a "tag" foi cadastrada com sucesso.
          alert_certo('Cadastrado', 'Tag cadastrado com sucesso.');
          //Limpa o valor do campo de entrada para que o usuário possa inserir outra "tag".
          document.getElementById("nome_tag_novo").value = '';
        }

      }
    });
  }


  function alert_certo(titulo, bory) {
    //Esta função exibe um alerta de sucesso na interface do usuário.

    //Utiliza a biblioteca "Toasts" para criar o alerta de sucesso.
    $(document).Toasts('create', {
      class: 'bg-success',//Classe CSS para estilizar o alerta (fundo verde para sucesso).
      title: titulo,//Título do alerta, que é fornecido como argumento para a função.
      subtitle: 'Subtitle',//Subtítulo do alerta (não usado neste exemplo).
      autohide: true,//Define se o alerta deve ser ocultado automaticamente após um período de tempo.
      delay: 5000,//Tempo em milissegundos antes do alerta desaparecer (neste caso, 5000ms ou 5 segundos).
      body: bory//Corpo do alerta, que é fornecido como argumento para a função.
    });
  }

  function alert_personalizado(titulo, bory) {
    //Esta função exibe um alerta personalizado na interface do usuário.

    //Utiliza a biblioteca "Toasts" para criar o alerta personalizado.
    $(document).Toasts('create', {
      class: 'bg-danger',//Classe CSS para estilizar o alerta (fundo vermelho para erro).
      title: titulo,//Título do alerta, que é fornecido como argumento para a função.
      subtitle: 'Subtitle',//Subtítulo do alerta (não usado neste exemplo).
      autohide: true,//Define se o alerta deve ser ocultado automaticamente após um período de tempo.
      delay: 13000,//Tempo em milissegundos antes do alerta desaparecer (neste caso, 13000ms ou 13 segundos).
      body: bory//Corpo do alerta, que é fornecido como argumento para a função.
    });
  }


  document.getElementById("nome_tag_novo").addEventListener("input", function () {
    //Dentro desta função, você pode limitar o tamanho máximo do texto no campo de entrada.

    //Pega a referência ao elemento de entrada (o "this" se refere ao elemento de entrada)
    var input = this;

    //Define o tamanho máximo desejado para o texto no campo de entrada
    var maxLength = 17;

    //Pega o valor atual no campo de entrada
    var valor = input.value;

    //Se for mais longo, trunca o valor para o tamanho máximo
    input.value = valor.slice(0, maxLength);//Trunca o valor para o tamanho máximo 

  });

  id_g = '';
  function modal_modificar(id) {
    //Remove o prefixo 'modal_' do ID para obter o ID real
    id = id.replace('modal_', '');
    id_g = id;
    $.ajax({
      url: '<?= base_url('public/tag_modal') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { id: id },
      success: function (response) {

        if (!response.desenho) {
          //Modificar o conteúdo do modal de acordo com a resposta do servidor

          //Altera o texto do botão de confirmação no modal
          var botao_confirmar_modal = document.getElementById('botao_confirmar_modal');
          botao_confirmar_modal.innerHTML = "Confirmar";

          //Obtém referências aos elementos do modal
          var modal_titulo = document.getElementById('modal_titulo');
          var modal_bory = document.getElementById('modal_bory');

          //Define o título do modal como "Modificar a tag: Nome da Tag"
          modal_titulo.textContent = "Modificar a tag: " + response.nome;

          // Limpa o conteúdo do modal_body
          modal_bory.innerHTML = '';

          // Cria e configura o primeiro grupo de form para "Subpasta"
          var divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");

          var labelElement = document.createElement("label");
          labelElement.textContent = "Subpasta";

          var inputElement = document.createElement("input");
          inputElement.type = 'text';
          inputElement.id = 'nome_tag_novo';
          inputElement.classList.add("form-control");
          inputElement.value = response.nome;

          // Adiciona um evento de input ao elemento para limitar o comprimento do valor
          inputElement.addEventListener("input", function () {
            var input = this;
            var maxLength = 17;
            input.value = input.value.slice(0, maxLength); // Trunca o valor para o tamanho máximo
          });

          // Adiciona os elementos ao divElemnt e depois ao modal_body
          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(inputElement);
          modal_bory.appendChild(divElemnt);

          // Cria e configura o segundo grupo de form para "Empreendimento"
          divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");

          labelElement = document.createElement("label");
          labelElement.textContent = "Empreendimento";

          inputElement = document.createElement("select");
          inputElement.id = 'empreendimento_tag_novo';
          inputElement.classList.add("form-control");

          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(inputElement);
          modal_bory.appendChild(divElemnt);

          // Cria e configura o terceiro grupo de form para "Finalidade"
          divElemnt = document.createElement("div");
          divElemnt.classList.add("form-group");

          labelElement = document.createElement("label");
          labelElement.textContent = "Finalidade";

          inputElement = document.createElement("select");
          inputElement.id = 'finalidade_tag_novo';
          inputElement.classList.add("form-control");

          divElemnt.appendChild(labelElement);
          divElemnt.appendChild(inputElement);
          modal_bory.appendChild(divElemnt);

          empreendimento_select(response.empreendimento);
          finalidade_select(response.finalidade);


          
          //Exibe o modal
          mostrarModal();

        } else {
          //Se a resposta indicar que a tag está em uso, exibe um alerta personalizado
          alert_personalizado('Modificar', 'Tag já está em uso.');
        }
      }
    });


  }
  function confirmarModal() {
    //Obtém o valor do campo de entrada com o ID "nome_tag_novo"
    var tag = document.getElementById("nome_tag_novo").value;
    var empreendimento = document.getElementById("empreendimento_tag_novo").value;
    var finalidade = document.getElementById("finalidade_tag_novo").value;

    $.ajax({
      url: '<?= base_url('public/tag_update') ?>',
      type: "POST",
      dataType: "json",//Indicar que o retorno é em formato JSON
      data: { id: id_g, tag: tag , empreendimento: empreendimento , finalidade: finalidade},
      success: function (response) {
        if (!response.ok) {
          const mensagens = response && response.msg ? response.msg : null;
          let exibiuMensagem = false;
          if (mensagens && typeof mensagens === 'object') {
            for (const chave in mensagens) {
              if (!Object.prototype.hasOwnProperty.call(mensagens, chave)) {
                continue;
              }
              const valor = mensagens[chave];
              alert_personalizado(chave, valor);
              exibiuMensagem = true;
            }
          } else if (typeof mensagens === 'string' && mensagens.trim() !== '') {
            alert_personalizado('Alteracao', mensagens);
            exibiuMensagem = true;
          }
          if (!exibiuMensagem) {
            alert_personalizado('Alteracao', 'Nenhum item foi modificado.');
          }
          return;
        } else {
          //Se a operação for bem-sucedida, exibe um alerta indicando o sucesso
          alert_certo('Alteração', 'Tag Modificado com sucesso.');
          //Chama a função lista() para atualizar a lista de tags
          lista();
        }

      }
    });

    //Fecha o modal após a conclusão da operação
    fecharModal();
  }

  function finalidade_select(id = null) {
    $.ajax({
      url: '<?= base_url('public/finalidade_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        // Limpa as opções atuais do select
        $('#finalidade_tag_novo').empty();

        // Adiciona uma opção padrão
        $('#finalidade_tag_novo').append('<option value="">Finalidade</option>');

        // Itera sobre o array de resposta e adiciona as opções ao select
        $.each(response.lista, function (index, item) {
          $('#finalidade_tag_novo').append('<option value="' + item.finalidade + '">' + item.finalidade + '</option>');
        });
        if(id != null)
        document.getElementById("finalidade_tag_novo").value = id; 
      },
      error: function (xhr, status, error) {
        console.error("Ocorreu um erro ao carregar os dados: ", error);
      }
    });

  }

  function empreendimento_select(id = null) {
    $.ajax({
      url: '<?= base_url('public/empreendimento_lista') ?>',
      type: "POST",
      dataType: "json", // Indicar que o retorno é em formato JSON
      success: function (response) {
        // Limpa as opções atuais do select
        $('#empreendimento_tag_novo').empty();

        // Adiciona uma opção padrão
        $('#empreendimento_tag_novo').append('<option value="">Empreendimento</option>');

        // Itera sobre o array de resposta e adiciona as opções ao select
        $.each(response.lista, function (index, item) {
          $('#empreendimento_tag_novo').append('<option value="' + item.empreendimento + '">' + item.empreendimento + '</option>');
        });
        if(id != null)
        document.getElementById("empreendimento_tag_novo").value = id; 
      
      },
      error: function (xhr, status, error) {
        console.error("Ocorreu um erro ao carregar os dados: ", error);
      }
    });

  }

  finalidade_select();
  value_empreendimento();
  empresa_select();



    function empresa_select() {
        document.getElementById("empreendimento_tag_novo").disabled = true;
        $.ajax({
            url: '<?= base_url('public/empresa_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            success: function (response) {
              

                            

                            // Obter referência ao elemento select
                            var funcao = document.getElementById("empresa_tag_novo");
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
                                value_empreendimento();
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
        });
    }


    function value_empreendimento() {
        $.ajax({
            url: '<?= base_url('public/empreendimento_lista') ?>',
            type: "POST",
            dataType: "json", // Indicar que o retorno é em formato JSON
            data: { empresa: document.getElementById("empresa_tag_novo").value },
            success: function (response) {

               
    
                        var funcao = document.getElementById("empreendimento_tag_novo");
                        if (document.getElementById("empresa_tag_novo").selectedIndex == 0) {
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
        });

    }



</script>
