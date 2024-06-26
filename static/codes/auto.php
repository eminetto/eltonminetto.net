<?
?>
<!-- css-->
<link href="script.aculo.us.css" media="screen" rel="Stylesheet" type="text/css" /> 
<!-- javascripts -->
<script src="prototype.js" type="text/javascript"></script> 
<script src="effects.js" type="text/javascript"></script>
<script src="dragdrop.js" type="text/javascript"></script>
<script src="controls.js" type="text/javascript"></script>
<!-- div q delimita o conteudo. obrigatorio-->
<div id="content">
    <h1>AJAX autocompletion demo</h1>
<p>Digite as letras iniciais dos nomes para ver o resultado</p>
Nomes (separar com virgula):
<style>
	div.auto_complete {
		width: 350px;
		background: #fff;
	}
	div.auto_complete ul {
		border:1px solid #888;
		margin:0;
		padding:0;
		width:100%;
		list-style-type:none;
	}
	div.auto_complete ul li {
		margin:0;
		padding:3px;
	}
	div.auto_complete ul li.selected { 
		background-color: #ffb; 
	}
	div.auto_complete ul strong.highlight { 
		color: #800; 
		margin:0;
		padding:0;
	}
</style>
<!-- input onde ser� digitado o nome -->
<input autocomplete="off" id="contact_name" name="contact_name" size="80" type="text" value="" />
<!-- div onde ser� mostrado o resultado -->
<div class="auto_complete" id="contact_name_auto_complete"></div>
	<script type="text/javascript">
		/*
		aqui � onde toda a m�gica acontece! 
		contact_name � o nome do input onde � digitado as letras iniciais
		contact_name_auto_complete � o nome da div onde ser� mostrado o resultado
		busca_nomes_ajax.php � o php q ser� executado a cada letra digitada e retonar� a lista de nomes
		*/
		new Ajax.Autocompleter('contact_name', 'contact_name_auto_complete', 'busca_nomes_ajax.php', { tokens: [',', '\n'] })
	</script>

	<p>
		<!--  efeito de mostrar um bloco de c�gido-->
		<a href="#" onclick="new Effect.SlideDown('source'); return false;">Mostrar</a>
	</p>
	<p>
		<!--  efeito de esconder um bloco de c�gido-->
		<a href="#" onclick="new Effect.SlideUp('source'); return false;">Esconder</a>
	</p>
	<!--  
		bloco de c�gido q ser� escondido/mostrado pelas linhas acima. ele usa o id para identificar o bloco
		este bloco pode conter qualquer html. no exemplo abaixo cont�m um formul�rio
	-->
	<pre id="source" style="display:none;">
		<form name="lixo" action="lixo.php">
		<input type="text" name="contact_name"><br>
		<input type="submit" value="Enviar">
		</form>
	</pre>

</div>
<!--  texto sem sentido, somente para demonstrar que todo o conte�do da p�gina � movido
	  quando um bloco de c�digo � mostrado/ocultado pelas linhas acima
-->
  fiosdfjiosdf
  sdoisdjfoisdjfsd
  siodfjiosdjfiosd
  sdofijsdfiojsdio
<!--  espa�o em branco somente usado aqui para testar o rodap� flutuante -->  
<pre>

















































</pre>
Teste
  <!-- cabe�alho flutuante  -->
  <div id="footer">
    <p>&copy; 2005 Thomas Fuchs
      <a href="#" onclick="new Effect.BlindDown('license'); return false;">License</a> 
      <a href="http://validator.w3.org/check?uri=referer">validate</a> 
      <a href="http://mir.aculo.us/">mir.aculo.us</a>
    </p>
    <p class="info">
      <a href="http://script.aculo.us">script.aculo.us</a> demo site version <em>2005/07/04</em>
    </p>
    <div id="license" style="display:none;">
      <pre>
      script.aculo.us:
      
      Copyright (c) 2005 Thomas Fuchs (http://script.aculo.us, http://mir.aculo.us)
      
      Permission is hereby granted, free of charge, to any person obtaining
      a copy of this software and associated documentation files (the
      "Software"), to deal in the Software without restriction, including
      without limitation the rights to use, copy, modify, merge, publish,
      distribute, sublicense, and/or sell copies of the Software, and to
      permit persons to whom the Software is furnished to do so, subject to
      the following conditions:
      
      The above copyright notice and this permission notice shall be
      included in all copies or substantial portions of the Software.
      
      THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
      EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
      MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
      NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
      LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
      OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
      WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
      
      <a href="#" onclick="new Effect.BlindUp('license'); return false;">Hide</a>
      </pre>
    </div>
  </div>