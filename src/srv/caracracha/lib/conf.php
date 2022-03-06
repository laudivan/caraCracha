<?php

	include("/srv/caracracha/lib/funcoes_padroes.inc.php");

	$empresa = htmlentities(lerConfiguracao('EMPRESA'), ENT_NOQUOTES,'UTF-8');

	$dir_sistema= lerConfiguracao('DIR_SISTEMA');

	$dir_db_usuarios=lerConfiguracao('DIR_DB_USUARIOS');
	$dir_db_sessoes=lerConfiguracao('DIR_DB_SESSAO');
	$dir_sessoes=lerConfiguracao('DIR_DB_SESSAO');
	$dir_db_admin=lerConfiguracao('DIR_DB_ADM');
	$dir_db_net=lerConfiguracao('DIR_DB_NET');
	
	$dir_script_auth="/srv/caracracha/bin/criar_sessao.sh";
	
	$dir_script_excluir="/srv/caracracha/bin/excluir_sessao.sh";
	
	$dir_script_excluir_cliente="/srv/caracracha/bin/excluir_cliente.sh";
	
	$dir_script_aplicar="/srv/caracracha/bin/aplicar_alteracoes.sh";
	
	$url_do_sistema=lerConfiguracao('URL_LOGIN');

	$dev_lan=lerConfiguracao('DEV_LAN');
	
	$inicio_ip = lerConfiguracao('ID_REDE');
	
	$rede = "$inicio_ip.0";
	
	$mascara_de_rede = lerConfiguracao('MASCARA_LAN');
	
	$dominio = lerConfiguracao('DOMINIO');
	
	$broadcast =  lerConfiguracao('BROADCAST');
	$roteador =  lerConfiguracao('ROTEADOR');
	
	$servidores_dns = lerConfiguracao('SERVIDOR_DNS');
	
	$dhcpdconf = lerConfiguracao('DHCPDCONF');
        $mascara_clientes = lerConfiguracao('MASCARA_CLIENTES');
    
	$opcoes_de_banda = array ("64", "128", "150", "256", "300", "384", "450", "512", "1024", "2048");

	if(isset($_SERVER['HTTP_HOST'])) {
		$url_de_adm="http://${_SERVER['HTTP_HOST']}/adm.php";

		include_once('/srv/caracracha/lib/tela.class.inc.php');		
		if($_POST['login']) {
			include_once('../lib/sessao.class.php');
		}else if(strcmp($_SERVER['PHP_SELF'],"adm.php")){
			include_once('../lib/sessao_adm.class.php');
		}
	}
?>
