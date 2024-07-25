kfja klfa 
adf a
df ad
sfad

<address><fieldset><datagrid>afafa</datagrid></fieldset></address>

<script>
$(document).ready(function()
{
	$('#form-enviar_link_whats #img-loading').hide();
    $('#btn-enviar_link_whats').on('click', function()
    {
    	var id_confirmacao = $('#form-enviar_link_whats #id_confirmacao').val();
		// var id_eleicao = $(this).attr('id_eleicao');
		// var id_pessoa_fisica = $(this).attr('id_pessoa_fisica');

    	$.ajax({
            type: 'POST',
            url: '<?php echo URLBASE;?>eleicoes/enviar_link_whats/<?php echo $pessoa->id_pessoa;?>',
            data: {
            	id_confirmacao: id_confirmacao,
            	//id_eleicao: id_eleicao,
            	//id_pessoa_fisica: id_pessoa_fisica
            },
            beforeSend: function(){
            	//$('#btn-enviar_link_whats').prop('disabled', true);
            	$('#form-enviar_link_whats #img-loading').show();
            },
            success: function(response)
            {
                var dados = jQuery.parseJSON(response);
                console.log(dados);
                if(dados.ok == 'true')
                {
                }
                else{
                	$.smallBox({
			            title : "Atenção",
			            content : dados.mensagem,
			            color : "#C46A69",
			            timeout: "3000",
			            iconSmall : "fa fa-thumbs-up bounce animated"
			        	});
                }
                $('#form-enviar_link_whats #img-loading').hide();
                $('#btn-enviar_link_whats').prop('disabled', false);
            },
            error: function (request, status, error){
            	$('#form-enviar_link_whats #img-loading').hide();
                $('#btn-enviar_link_whats').prop('disabled', false);

                console.log(request.responseText);
                $.smallBox({
		            title : "Atenção",
		            content : 'Ocorreu um erro, favor entrar em contato com o suporte.',
		            color : "#C46A69",
		            timeout: "3000",
		            iconSmall : "fa fa-thumbs-up bounce animated"
		        	});
            }
        });
	});


});

$(document).ready(function()
{
	$('#form-enviar_link_whats #img-loading').hide();
    $('#btn-enviar_link_whats').on('click', function()
    {
    	var id_confirmacao = $('#form-enviar_link_whats #id_confirmacao').val();
		// var id_eleicao = $(this).attr('id_eleicao');
		// var id_pessoa_fisica = $(this).attr('id_pessoa_fisica');
        if(id_priorida == true)
    	$.ajax({
            type: 'POST',
            url: '<?php echo URLBASE;?>eleicoes/enviar_link_whats/<?php echo $pessoa->id_pessoa;?>',
            data: {
            	id_confirmacao: id_confirmacao,
            	//id_eleicao: id_eleicao,
            	//id_pessoa_fisica: id_pessoa_fisica
            },
            beforeSend: function(){
            	//$('#btn-enviar_link_whats').prop('disabled', true);
            	$('#form-enviar_link_whats #img-loading').show();
            },
            success: function(response)
            {
                var dados = jQuery.parseJSON(response);
                console.log(dados);
                if(dados.ok == 'true')
                {
                }
                else{
                	$.smallBox({
			            title : "Atenção",
			            content : dados.mensagem,
			            color : "#C46A69",
			            timeout: "3000",
			            iconSmall : "fa fa-thumbs-up bounce animated"
			        	});
                }
                $('#form-enviar_link_whats #img-loading').hide();
                $('#btn-enviar_link_whats').prop('disabled', false);
            },
            error: function (request, status, error){
            	$('#form-enviar_link_whats #img-loading').hide();
                $('#btn-enviar_link_whats').prop('disabled', false);

                console.log(request.responseText);
                $.smallBox({
		            title : "Atenção",
		            content : 'Ocorreu um erro, favor entrar em contato com o suporte.',
		            color : "#C46A69",
		            timeout: "3000",
		            iconSmall : "fa fa-thumbs-up bounce animated"
		        	});
            }
        });
	});


});
</script>