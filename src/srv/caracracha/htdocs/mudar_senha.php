<?php
require_once '/srv/caracracha/lib/conf.php';

$tela = new tela_html("/srv/caracracha/templates/form_mudar_senha.html");
$tela->add_substituicao("url_mudar_senha",$url_do_sistema."/mudar_senha.php?login=".$_GET['login']);

if(isset($_POST['senha'])){
	if ($_POST['senha'] != file_get_contents($dir_db_usuarios.'/'.$_GET['login'].'/senha')) {
		$tela->add_substituicao("mensagem","<b>Senha anterior</b> n&atilde;o confere.");
	}else if($_POST['senha1']!=$_POST['senha2']) {
		$tela->add_substituicao("mensagem","Os campos <b>Nova senha</b> e <b>Repetir a nova senha</b> n&atilde;o conferem.");
	}else if($_POST['senha1'] == "") {
		$tela->add_substituicao("mensagem","A senha n&atilde;o pode ser vazia.");
	}else{
			$resultado = file_put_contents($dir_db_usuarios.'/'.$_GET['login'].'/senha', $_POST['senha1']);
			echo <<<RESPOSTA
<HMTL>
<BODY bgcolor="#fffff">
<TABLE width="100%" cellspacing="2" border="0" cellpadding="2" align="center" height="100%">
<TR><TD  height="100%" align="center" valign="middle">Mudan&#231;a de senha efetuada com sucesso.</TD></TR>
<TR><TD>&nbsp;</TD></TR>
<TR><TD><INPUT type="button" name="fechar" value="Fechar janela" onclick="window.close()"></TD></TR>
</TABLE>
</BODY>
</HTML>
RESPOSTA;
			exit;
	}
	$tela->add_substituicao("login",$_GET['login']);
}else{
    $tela->add_substituicao("mensagem","");
	$tela->add_substituicao("login",$_GET['login']);
}

$tela->exibir_tela();

?>
