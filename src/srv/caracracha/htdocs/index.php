<?php
require_once "../lib/conf.php";

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if($_GET['desconectar']) {
	global $dir_sessoes,$dir_script_excluir;
	if(file_exists($dir_sessoes."/".$_GET['desconectar'])){
		echo system('sudo '.$dir_script_excluir.' '.$_GET['desconectar']);
	}
	
	header("Location: http://".$url_do_sistema);
}

if($_SERVER["SERVER_NAME"]!==$url_do_sistema){
	
	header("Location: http://".$url_do_sistema);
} else if($_POST["login"]){
		if($_POST['senha']){
			$sessao = new sessao($_POST['login'],$_POST['senha']);
			$sessao->iniciar_sessao();			
		}else{
			erro_login("SENHA_VAZIA");
		}
}if($usuario = usuarioEstaAutenticado()){
	$tela = new tela_html("/srv/caracracha/templates/autenticado.html");
	$tela->add_substituicao("url_mudar_senha",$url_do_sistema."/mudar_senha.php?login=".$usuario);
	$tela->add_substituicao("mensagem","Voc&#234; est&#225; autenticado.");
	$tela->add_substituicao("desconectar","$url_do_sistema?desconectar=".$usuario);
	$tela->exibir_tela();
}else{
	$tela = new tela_html("/srv/caracracha/templates/login.html");
	$tela->add_substituicao("mensagem","Digite seu login e senha para navegar.");		
	$tela->exibir_tela();
}


?>
