<html>
<head>
<title>Nome </title>
</head>
<script src="include/cpaint/cpaint.inc.js" type="text/javascript"></script>
<script type="text/javascript">

//funcao q faz a conexao para buscar o nome da pessoa
function get_nome() {
  cpaint_call(
  'funcoes_ajax.php', //nome do arquivo php que ser� chamado
  'POST', //m�todo que ser� usado
  'busca_nome', //nome da fun��o que ser� executada no arquivo funcoes_ajax.php
  teste.codpes.value, //valor do parametro q ser� enviado
  atualiza, //funcao javascript que receber� o valor de retorno e atualizar� o campo do formulario
  'TEXT'); //modo que ser� recebido e enviado os dados 
}
//funcao que recebe o retorno com o nome e coloca no campo nompes
function atualiza(valorDeRetorno) {
	teste.nompes.value = valorDeRetorno;
}
</script>
<body>
<form name="teste">
<input type="text" name="codpes" onchange="get_nome()">
<input type="text" name="nompes">
</form>
</body>
</html>