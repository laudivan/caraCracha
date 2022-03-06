<?php
$lista_de_ips_cadastrados = file($dir_db_net."/ips_clientes.lst");
$lista_de_macs_cadastrados = file($dir_db_net."/macs_clientes.lst");

$tempo_sessao = 60*5;

function obter_sessao() {
    global $url_de_adm, $dir_db_admin,$empresa,$admin_senha;

    if(isset($_SESSION['login'])) {
        if ($_GET['desconectar']) {
            unset($_SESSION['login']);
            header("Location: $url_de_adm");
            exit;
        }else {
            exibir_tabelas();
            exit;
        }
    }elseif($_POST['login_adm']) {
        include "$dir_db_admin/${_POST['login_adm']}.php";

        if($_POST['senha']===$admin_senha) {
            $login_adm=$_POST['login_adm'];
            $_SESSION['login']=$_POST['login_adm'];

            exibir_tabelas();

            exit;
        }
    }

    $tela = new tela_html('/srv/caracracha/templates/login_adm.html');
    $tela->add_substituicao("url_autenticacao",$url_de_adm);
    $tela->add_substituicao("titulo", $empresa);
    $tela->exibir_tela();
    exit;
}

function exibir_tabelas() {
    global $url_de_adm,$opcoes_de_banda,$dir_db_usuarios,$lista_de_ips_cadastrados,$inicio_ip;

    $cor_ativo="#CECECE";
    $cor_desativo="#EAEAEA";

    $lista_ativa=0;
    $lista_desativa=1;

    if(isset($_GET['cadastrar'])) {
        $tela = new tela_html('/srv/caracracha/templates/form_cadastro.html');


        $tela->add_substituicao("url_tabela_0","$url_de_adm?tbativa=0");
        $tela->add_substituicao("inicio_ip","$inicio_ip.");

        if(strlen($_GET['cadastrar'])>2) {
            global $dir_db_usuarios;

            $tela->add_substituicao("disabled","disabled");
            $tela->add_substituicao("btsalvar","Salvar");
            $tela->add_substituicao("url_cadastrar","$url_de_adm?cadastrar=".$_GET['cadastrar']);

            if(isset($_POST['senha'])) {
                $tela->add_substituicao("mensagem",alterar_usuario());
            }else {
                $tela->add_substituicao("mensagem","");
            }

            $arq_usuario=$dir_db_usuarios."/".$_GET['cadastrar']."/";
            $senha = trim( fgets($arq_aux=fopen($arq_usuario."senha","r+")) );
            fclose($arq_aux);
            $nome = trim ( fgets($arq_aux=fopen($arq_usuario."nome","r+")) );
            fclose($arq_aux);
            $ip = trim ( fgets($arq_aux=fopen($arq_usuario."ip","r+")) );
            fclose($arq_aux);
            $mac = trim ( fgets($arq_aux=fopen($arq_usuario."mac","r+")) );
            fclose($arq_aux);
            $banda = trim ( fgets($arq_aux=fopen($arq_usuario."banda","r+")) );
            fclose($arq_aux);
            $bloqueado = file_exists($arq_usuario."bloqueado") ? 'checked' : '';
            $compartilha = file_exists($arq_usuario."compartilha") ? 'checked' : '';

        }else {
            $tela->add_substituicao("disabled","");
            $tela->add_substituicao("btsalvar","Incluir");
            $tela->add_substituicao("url_cadastrar","$url_de_adm?cadastrar");

            if(isset($_POST['usuario'])) {
                $tela->add_substituicao("mensagem",incluir_usuario());
            }else {
                $tela->add_substituicao("mensagem","");
						/*sugerindo ip de usurio*/
                for($i=5;$i<=255 and in_array("$inicio_ip.$i\n",$lista_de_ips_cadastrados);$i++);

                $ip = $i==254?"Sem mais ips livres.":"$inicio_ip.$i";
            }

            $compartilha = 'checked';
        }

        $tela->add_substituicao("login",$_GET['cadastrar']);
        $tela->add_substituicao("senha",$senha);
        $tela->add_substituicao("nome",$nome);
        $tela->add_substituicao("ip",$ip);
        $tela->add_substituicao("mac",$mac);
        $tela->add_substituicao("banda",$banda);
        $tela->add_substituicao("bloqueado",$bloqueado);
        $tela->add_substituicao("compartilha",$compartilha);

        foreach ($opcoes_de_banda as $chave => $banda_op) {
            if ($banda_op === $banda)
                $select_bandas .= "<option selected>".$banda_op."</option>\n";
            else
                $select_bandas .= "<option>".$banda_op."</option>\n";
        }
        $tela->add_substituicao("bandas",$select_bandas);
    }elseif(isset($_GET['cadadmin'])) {
        global $dir_db_admin,$admin,$admin_nome,$admin_senha,$admin_email;

        $login = $_GET['cadadmin'];

        $tela = new tela_html('/srv/caracracha/templates/form_cadadmin.html');
        $tela->add_substituicao("url_tabela_2","$url_de_adm?tbativa=2");

        if(isset($_POST['senha'])) {
            $login = strlen($_GET['cadadmin'])>2 ? $_GET['cadadmin'] : $_POST['usuario'];
            $conteudo = "<?php\n \$admin='$login';\n \$admin_nome='${_POST['nome']}';\n \$admin_senha='${_POST['senha']}';\n \$admin_email='${_POST['email']}';\n?>";

            $arq = fopen("$dir_db_admin/$login.php",'w');
            fwrite($arq, $conteudo);
            fclose($arq);

            $mensagem = "Administrador ".(strlen($_GET['cadadmin'])>2?"alterado":"inclu&iacute;do")." com sucesso.";
        }

        if($login) {
            $tela->add_substituicao("url_cadastrar","$url_de_adm?cadadmin=$login");
            $tela->add_substituicao("disabled","disabled");
            $tela->add_substituicao("btsalvar", "Salvar");
            include "$dir_db_admin/$login.php";
        }else {
            $tela->add_substituicao("url_cadastrar","$url_de_adm?cadadmin");
            $tela->add_substituicao("disabled","");
            $tela->add_substituicao("btsalvar", "Incluir");
        }

        $tela->add_substituicao("login",$admin);
        $tela->add_substituicao("nome",$admin_nome);
        $tela->add_substituicao("email",$admin_email);
        $tela->add_substituicao("senha",$admin_senha);
        $tela->add_substituicao("mensagem",$mensagem);
    }else {
        $tela = new tela_html('/srv/caracracha/templates/adm_menu.html');

        $lista_ativa=$_GET['tbativa']*1;

        $tela->add_substituicao("url_de_adm","$url_de_adm");
        $tela->add_substituicao("url_desconectar","$url_de_adm?&desconectar=1");

        $tela->add_substituicao("url_cadastrar","$url_de_adm?cadastrar");
        $tela->add_substituicao("url_cadastrar_admin","$url_de_adm?cadadmin");
        $tela->add_substituicao("url_tabela_0","$url_de_adm?tbativa=0");
        $tela->add_substituicao("url_tabela_1","$url_de_adm?tbativa=1");
        $tela->add_substituicao("url_tabela_2","$url_de_adm?tbativa=2");

        $tela->add_substituicao("url_tabela","$url_de_adm?tbativa=$lista_ativa");

        switch($lista_ativa) {
            case 2:
                $tela->add_substituicao("cor_lista_0",$cor_desativo);
                $tela->add_substituicao("cor_lista_1",$cor_desativo);
                $tela->add_substituicao("cor_lista_2",$cor_ativo);
                $tela->add_substituicao("conteudo_tabela",listar_administradores());
                break;
            case 1:
                $tela->add_substituicao("cor_lista_0",$cor_desativo);
                $tela->add_substituicao("cor_lista_1",$cor_ativo);
                $tela->add_substituicao("cor_lista_2",$cor_desativo);
                $tela->add_substituicao("conteudo_tabela",listar_usuarios_on_line());
                break;
            default:
                $tela->add_substituicao("cor_lista_0",$cor_ativo);
                $tela->add_substituicao("cor_lista_1",$cor_desativo);
                $tela->add_substituicao("cor_lista_2",$cor_desativo);
                $tela->add_substituicao("conteudo_tabela",listar_usuarios());
                break;
        }



    }

    $tela->exibir_tela();
}

function listar_administradores() {
    global $dir_db_admin,$url_de_adm,$admin,$admin_nome, $admin_email;

    header("Refresh: 352; url=$url_de_adm?tbativa=2");

    if(isset($_GET['excluir'])) {
        unlink("$dir_db_admin/${_GET['excluir']}.php");
    }

    $lista_out= <<<SAIDA
        <tr>
		<TD width="100px"  style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">LOGIN</TD>
		<TD width="*" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">NOME DO ADMINISTRADOR</TD>
		<TD width="200px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">EMAIL</TD>
		<TD width="20px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">EXCLUIR</TD>
	</TR>
SAIDA;

    $dir  = opendir($dir_db_admin);

    while (false !== ($arquivo = readdir($dir))) {
        if($arquivo==="." or $arquivo==="..") continue;

        include "$dir_db_admin/$arquivo";

        $url_alterar = "$url_de_adm?cadadmin=$admin";

        $cor_linha = $cor_linha !== "ffffff" ? "ffffff" : "eaeaea";

        $excluir = $_SESSION['login']==$admin ? "" : "<a href=\"javascript:excluirUsuario('$admin')\"><IMG src=\"imagens/excluir.png\" width=\"22\" height=\"22\" border=\"0\"></a>";

        $lista_out .= <<<SAIDA

    	<TR style="background-color : $cor_linha">
		<TD width="100px"  style="text-decoration : none;">
			<a href="$url_alterar">$admin</a>
		</TD>
		<TD width="*" style="text-decoration :none;">
            $admin_nome
		</TD>
		<TD width="50px" style="text-decoration : none;">
            $admin_email
		<TD width="20px" style="text-decoration : none;">
            $excluir
		</TD>
	</TR>
SAIDA;
    }

    return $lista_out;

}

function listar_usuarios() {
    global $dir_db_usuarios,$url_de_adm;

    header("Refresh: 352; url=$url_de_adm?tbativa=0");

    if(isset($_GET['desblq'])) {
        desbloquear_usuario($_GET['desblq']);
    }elseif(isset($_GET['blq'])) {
        bloquear_usuario($_GET['blq']);
    }elseif(isset($_GET['excluir'])) {
        excluir_usuario($_GET['excluir']);
    }

    $lista_usuarios_out= <<<SAIDA
        <tr>
		<TD width="100px"  style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">LOGIN</TD>
		<TD width="*" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">NOME DO CLIENTE</TD>
		<TD width="100px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">IP</TD>
		<TD width="100px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">MAC</TD>
		<TD width="20px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">STATUS</TD>
		<TD width="20px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">EXCLUIR</TD>
	</TR>
SAIDA;

    $dir  = opendir($dir_db_usuarios);

    while (false !== ($arquivo = readdir($dir))) {
        if($arquivo==="." or $arquivo==="..") continue;

        if(file_exists($dir_db_usuarios."/".$arquivo."/bloqueado")) {
            $bloqueado='<IMG src="imagens/blq.png" width="22" height="22" border="0">';
            $url_bloquear="$url_de_adm?tbativa=0&desblq=$arquivo";
        }else {
            $bloqueado='<IMG src="imagens/livre.png" width="22" height="22" border="0">';
            $url_bloquear="$url_de_adm?tbativa=0&blq=$arquivo";
        }


        $arq_aux = fopen($dir_db_usuarios."/".$arquivo."/nome", "r+");

        $nome = fgets($arq_aux);

        fclose($arq_aux);

        $arq_aux = fopen($dir_db_usuarios."/".$arquivo."/ip", "r+");

        $ip = fgets($arq_aux);

        fclose($arq_aux);

        $arq_aux = fopen($dir_db_usuarios."/".$arquivo."/mac", "r+");

        $mac = fgets($arq_aux);

        fclose($arq_aux);

        $url_alterar = "$url_de_adm?cadastrar=$arquivo";

        $url_excluir="$url_de_adm?tbativa=0&excluir=$arquivo";

        $cor_linha = $cor_linha !== "ffffff" ? "ffffff" : "eaeaea";

        $lista_usuarios_out .= <<<SAIDA

    	<TR style="background-color : $cor_linha">
		<TD width="100px"  style="text-decoration : none;">
			<a href="$url_alterar">$arquivo</a>
		</TD>
		<TD width="*" style="text-decoration :none;">
            $nome
		</TD>
		<TD width="50px" style="text-decoration : none;">
            $ip
		</TD>
		<TD width="50px" style="text-decoration : none;">
            $mac
		</TD>
		<TD width="20px" style="text-decoration : none;">
			<a href="$url_bloquear">$bloqueado</a>
		</TD>
		<TD width="20px" style="text-decoration : none;">
			<a href="javascript:excluirUsuario('$arquivo')"><IMG src="imagens/excluir.png" width="22" height="22" border="0"></a>
		</TD>
	</TR>
SAIDA;
    }

    return $lista_usuarios_out;

}

function listar_usuarios_on_line() {
    global $dir_db_sessoes,$url_de_adm,$dir_script_excluir;

    header("Refresh: 352; url=$url_de_adm?tbativa=1");

    if(isset($_GET['excluirsessao'])) {
        echo system('sudo '.$dir_script_excluir.' '.$_GET['excluirsessao']);
    }

    $dir  = opendir($dir_db_sessoes);

    $lista_usuarios_out= <<<SAIDA
        <tr>
		<TD style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">LOGIN</TD>
		<TD style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">IP</TD>
		<TD style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">MAC</TD>
		<TD width="22px" style="background-color : #cecece; color : #ffffff; font-weight : bold; text-decoration : none;">Desconectar</TD>
	</TR>
SAIDA;

    while (false !== ($arquivo = readdir($dir))) {
        if($arquivo==="." or $arquivo==="..") continue;

        $arq_aux = fopen($dir_db_sessoes."/".$arquivo."/ip", "r+");

        $ip = fgets($arq_aux);

        fclose($arq_aux);

        $arq_aux = fopen($dir_db_sessoes."/".$arquivo."/mac", "r+");

        $mac = fgets($arq_aux);

        fclose($arq_aux);

        $url_desconectar="$url_de_adm?tbativa=1&excluirsessao=$arquivo";

        $cor_linha=($cor_linha==="ffffff")?"eaeaea":"ffffff";

        $lista_usuarios_out .= <<<SAIDA

    	<TR style="background-color : #$cor_linha">
		<TD  style="text-decoration : none;">
            $arquivo
		</TD>
		<TD style="text-decoration :none;">
            $ip
		</TD>
		<TD style="text-decoration :none;">
            $mac
		</TD>
		<TD width="20px" style="text-decoration : none;">
			<a href="$url_desconectar"><IMG src="imagens/stop.png" width="22" height="22" border="0"></a>
		</TD>
	</TR>
SAIDA;
    }

    return $lista_usuarios_out;
}

/**
 *
 * @global <type> $dir_db_usuarios
 * @global <type> $dir_script_aplicar
 * @global <type> $rede
 * @global <type> $lista_de_ips_cadastrados
 * @global <type> $lista_de_macs_cadastrados
 * @return String Mensagem em caso de login existente
 */
function incluir_usuario() {
    global $dir_db_usuarios,$dir_script_aplicar,$rede,$lista_de_ips_cadastrados,$lista_de_macs_cadastrados;

    if(file_exists($dir_db_usuarios."/".$_POST['usuario'])) {
        return "J&#225; existe um usu&#225;rio com este login.";
    }else {
        mkdir($dir_db_usuarios."/".$_POST['usuario']);
        fwrite($arq_aux=fopen($dir_db_usuarios."/".$_POST['usuario']."/nome","w+"),$_POST['nome']);
        fclose($arq_aux);

        fwrite($arq_aux=fopen($dir_db_usuarios."/".$_POST['usuario']."/senha","w+"),$_POST['senha']);
        fclose($arq_aux);

        fwrite($arq_aux=fopen($dir_db_usuarios."/".$_POST['usuario']."/ip","w+"),$_POST['ip']);
        fclose($arq_aux);

        fwrite($arq_aux=fopen($dir_db_usuarios."/".$_POST['usuario']."/mac","w+"),$_POST['mac']);
        fclose($arq_aux);
		/*banda*/
        fwrite($arq_aux=fopen($dir_db_usuarios."/".$_POST['usuario']."/banda","w+"),$_POST['banda']);
        fclose($arq_aux);

        if($_POST['bloqueado'] and !file_exists($dir_db_usuarios."/".$_POST['usuario']."/bloqueado")) {
            bloquear_usuario($_POST['usuario']);
        }else {
            desbloquear_usuario($_POST['usuario']);
        }

        if($_POST['compartilha']) {
            libera_compartilha_usuario($_POST['usuario']);
        }else {
            bloqueia_compartilha_usuario($_POST['usuario']);
        }

        exec('sudo '.$dir_script_aplicar);

        return "Usu&#225;rio inclu&#237;do com sucesso.";

    }
}

function alterar_usuario() {
    global $dir_db_usuarios, $dir_script_aplicar;

    fwrite($arq_aux=fopen($dir_db_usuarios."/".$_GET['cadastrar']."/nome","w+"),$_POST['nome']);
    fclose($arq_aux);

    fwrite($arq_aux=fopen($dir_db_usuarios."/".$_GET['cadastrar']."/senha","w+"),$_POST['senha']);
    fclose($arq_aux);

    fwrite($arq_aux=fopen($dir_db_usuarios."/".$_GET['cadastrar']."/ip","w+"),$_POST['ip']);
    fclose($arq_aux);

    fwrite($arq_aux=fopen($dir_db_usuarios."/".$_GET['cadastrar']."/mac","w+"),$_POST['mac']);
    fclose($arq_aux);

    fwrite($arq_aux=fopen($dir_db_usuarios."/".$_GET['cadastrar']."/banda","w+"),$_POST['banda']);
    fclose($arq_aux);

    if($_POST['bloqueado'] and !file_exists($dir_db_usuarios."/".$_GET['cadastrar']."/bloqueado")) {
        bloquear_usuario($_GET['cadastrar']);
    }else {
        desbloquear_usuario($_GET['cadastrar']);
    }

    if($_POST['compartilha']) {
        libera_compartilha_usuario($_GET['cadastrar']);
    }else {
        bloqueia_compartilha_usuario($_GET['cadastrar']);
    }

    exec('sudo '.$dir_script_aplicar);

    return "Altera&#231;&#227;o salva.";
}

function bloquear_usuario($usuario) {
    global $dir_db_usuarios;
    fclose(fopen($dir_db_usuarios."/".$usuario."/bloqueado","w"));
    desconectar_usuario($usuario);
}

function desbloquear_usuario($usuario) {
    global $dir_db_usuarios,$dir_db_usuarios;
    if(file_exists($dir_db_usuarios."/".$usuario."/bloqueado")) {
        unlink($dir_db_usuarios."/".$usuario."/bloqueado");
    }
}

function libera_compartilha_usuario($usuario) {
    global $dir_db_usuarios;
    if(!file_exists($dir_db_usuarios."/".$usuario."/compartilha")) {
        fclose(fopen($dir_db_usuarios."/".$usuario."/compartilha","w"));
    }
    desconectar_usuario($usuario);
}

function bloqueia_compartilha_usuario($usuario) {
    global $dir_db_usuarios,$dir_db_usuarios;
    if(file_exists($dir_db_usuarios."/".$usuario."/compartilha")) {
        unlink($dir_db_usuarios."/".$usuario."/compartilha");
    }
}

function excluir_usuario($usuario) {
    global $dir_script_excluir_cliente;

    echo system('sudo '.$dir_script_excluir_cliente.' '.$_GET['excluir']);

    desconectar_usuario($usuario);
}

function desconectar_usuario($usuario) {
    global $dir_sessoes,$dir_script_excluir;
    if(file_exists($dir_sessoes."/".$usuario)) {
        echo system('sudo '.$dir_script_excluir.' '.$usuario);
    }
}

function validar_cad_ip($ip) {
    global $rede,$lista_de_ips_cadastrados;
        /*Verificando formato*/
    $array_ip = explode(".",$ip);
    $array_rede = explode(".",$rede);
    if(ip2long($ip)===FALSE) return "Formato do IP est incorreto.";

    for($i=0;$i<4;$i++) if($array_ip[$i]!=$array_rede[$i]) return "O IP ".$ip." esta fora da rede.";

    if(array_search($ip,$lista_de_ips_cadastrados)!=FALSE) return "O IP ".$ip." j est em uso na rede.";

    return TRUE;
}

function validar_cad_mac($mac) {
    global $rede,$lista_de_macs_cadastrados;
	/*verificando formato*/
    $array_mac = explode(":",$mac);

    if(sizeof($array_mac!=6)) return "Formato do MAC est incorreto.";

    //Ver como validar os numeros

    if(array_search($mac,$lista_de_macs_cadastrados)!=FALSE) return "O MAC ".$mac." j est em uso na rede.";

    return TRUE;
}

?>
