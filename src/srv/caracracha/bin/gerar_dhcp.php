#!/usr/bin/php
<?php
//TODO: criar rota para mascara 255.255.255.255
require_once '/srv/caracracha/lib/conf.php';

exec ("rm -f $dhcpdconf");

$dir  = opendir($dir_db_usuarios);


$dhcpd_conteudo = "
#Arquivo gerado pelo sistema de autenticacao do CaraCracha
#Criado por Laudivan Freire de Almeida - laudivan@gmail.com

deny unknown-clients;

option domain-name \"$dominio\";
option domain-name-servers $servidores_dns;

default-lease-time 600;
max-lease-time 7200;

subnet $rede netmask $mascara_de_rede {
    option static-routes 127.0.0.1 $roteador;
    option subnet-mask $mascara_clientes;
    option broadcast-address $broadcast;
    option routers $roteador;
    authoritative;
";

$lista_de_ips = "";

$lista_de_macs = "";

while (false !== ($arquivo = readdir($dir))) {

	if($arquivo==="." or $arquivo==="..") continue;
		
	$ip = file_get_contents($dir_db_usuarios."/".$arquivo."/ip", "r+");
	
	$lista_de_ips .= $ip."\n";
	
	$mac = file_get_contents($dir_db_usuarios."/".$arquivo."/mac", "r+");

	$lista_de_macs .= $mac."\n";

	$dhcpd_conteudo .= "host ".$arquivo." {\n"."\thardware ethernet ".$mac.";\n"."\tfixed-address ".$ip.";\n}\n\n";
	
}


$lista_de_ips_arq = fopen($dir_db_net."/ips_clientes.lst","w+");
fwrite($lista_de_ips_arq, $lista_de_ips);
fclose($lista_de_ips_arq);

$lista_de_macs_arq = fopen($dir_db_net."/macs_clientes.lst","w+");
fwrite($lista_de_macs_arq, $lista_de_macs);
fclose($lista_de_macs_arq);

$dhcpd_conteudo .= "}\n\n";

$dhcpd = fopen($dhcpdconf,"w+");
fwrite($dhcpd, $dhcpd_conteudo);
fclose($dhcpd);

?>
