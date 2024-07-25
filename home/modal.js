async function atualizar_user(id){
//console.log(id);
  const dados=  await fetch('modificar_usuario.php?id='+id);
  const resposta = await dados.json();
  
  //console.log(resposta);
  
  if(!resposta['status']){
    document.getElementById('msgAlert').innerHTML = resposta['msg'];
  }else{
    var dado = resposta['dados'];
    document.getElementById('modal_atualizar').innerHTML = dado;
  }

}












