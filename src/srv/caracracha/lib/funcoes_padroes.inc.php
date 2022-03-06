<?php

function erro_login($id_erro){
	global $url_do_sistema;
	
	$erros=array (
		"USUARIO_N_EXISTE" => "O usu&#225;rio solicitado n&#227;o existe, tente novamente.",
		"USUARIO_BLQ" => "Seu usu&#225;rio est&#225; bloqueado, contacte-nos para esclarescimentos pelos telefones (74)8802-8341 e (74)8813-5019.",
		"SENHA_ERRADA" => "A senha digitada est&#225; errada.",
		"USUARIO_INVALIDO" => "O usu&#225;rio digitado n&#227;o existe. Tente novamente.",
		"SENHA_VAZIA" => "Voc&#234; deixou o campo senha vazio, tende novamente."
	);
	
	$tela = new tela_html('/srv/caracracha/templates/login.html');
	$tela->add_substituicao("mensagem",$erros[$id_erro]);
	$tela->exibir_tela();
	exit;
}


function erro_fatal($id_erro) {
	$erros=array(
		"ERRO_ARQ_TELA" => "O programa de autentica&#231;&#225;o n&#227;o p&#244;de ler o arquivo de tela.",
		"ERRO_ARQ_CRIA_SESSAO" => "O arquivo de sessao n&#227;o existe ou est&#225; em um diret&#243;rio diferente do configurado.",
		"ERRO_ARQ_CRIA_SESSAO_EXEC" => "N&#227;o h&#225; permiss&#227;o para executar o script de autentica&#227;o.",
		"ERRO_EXECUCAO_CRIA_SESSAO" => "Falha na execu&#227;o do script de autentica&#227;o."
	);
	
	$tela = new tela_html('/srv/caracracha/templates/autenticado.html');
	$tela->add_substituicao("mensagem","ERRO FATAL: ".$erros[$id_erro]);
	
	$tela->exibir_tela();
	exit;
}

function lista_ordenada_de_clientes($ordem="ip"){
	global $dir_db_usuarios;
	
	$lista = array();
	
	$dir  = opendir($dir_db_usuarios);
	while (false !== ($arquivo = readdir($dir))) {

		if($arquivo==="." or $arquivo==="..") continue;
		
		$lista[$arquivo] = file_get_contents($dir_db_usuarios."/".$arquivo."/".$ordem, "r+");	
	}
	
	$lista = asort($lista);
	
	foreach ($lista as $login=>$ordem) {
		$lista[$login] = array(
			"ip" => file_get_contents($dir_db_usuarios."/".$login."/ip", "r+"), 
			"nome" => file_get_contents($dir_db_usuarios."/".$login."/nome", "r+"), 
			"mac" => file_get_contents($dir_db_usuarios."/".$login."/mac", "r+"),
			"banda" => file_get_contents($dir_db_usuarios."/".$login."/banda", "r+"),
			"bloqueado" => file_exists($dir_db_usuarios."/".$login."/bloqueado")
			);
	}
	
	return $lista;
}

function usuarioEstaAutenticado(){
	global $dir_sessoes;
	
	$ip_cliente = $_SERVER['REMOTE_ADDR'];	
	$dir  = opendir($dir_sessoes);
	while ($usuario = readdir($dir)) {

		if($usuario=="." or $usuario=="..") continue;

		if($ip_cliente == file_get_contents($dir_sessoes.'/'.$usuario.'/ip', 'r+')) {
			return $usuario;
		}
	}
	
	return false;
}

function lerConfiguracao($configuracao) {
	static $caracracha_conf;
	
	if(is_null($caracracha_conf)) {
		$arq = fopen("/srv/caracracha/etc/caracracha.conf","r");
		while(!feof($arq)) {
			$linha = trim(fgets($arq));
			if ($linha=="" or $linha[0]=='#') continue; //linhas vazias ou comentadas são ignoradas
		
			$linha = explode('=',$linha,2);
			$caracracha_conf[trim($linha[0])]=trim($linha[1]);
		}
		
		fclose($arq);
	}

	return $caracracha_conf[$configuracao];
}
?>
