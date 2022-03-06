<?PHP

class sessao {
	var $usuario;
	var $senha;
	var $ip;
	var $mac;
	var $mensagem;
	
	function sessao($usuario,$senha) {
		global $dir_db_usuarios;
		$this->senha = FALSE;
		
		if(file_exists($dir_db_usuarios."/".$usuario)){
			$this->usuario = $usuario;
			$senha_aux = trim(file_get_contents($dir_db_usuarios."/".$usuario."/senha"));
			$this->ip = file_get_contents($dir_db_usuarios."/".$usuario."/ip");
			$this->mac = file_get_contents($dir_db_usuarios."/".$usuario."/mac");
			
			if(!strcmp($senha,$senha_aux)) {
				if(file_exists($dir_db_usuarios."/".$usuario."/bloqueado")){
					erro_login("USUARIO_BLQ");
				}else{
					$this->senha = $senha;
				}					
			}else{
				erro_login("SENHA_ERRADA");
			}
			
		}else{
			erro_login("USUARIO_INVALIDO");	
		}
	}
	
	function iniciar_sessao () {
		global $dir_script_auth, $dir_sessoes, $url_do_sistema;
		
		if(file_exists($dir_script_auth)){
			if(is_executable($dir_script_auth)){
				
				system('sudo '.$dir_script_auth.' '.$this->usuario);
				
				if(file_exists($dir_sessoes."/".$this->usuario)){					
					$tela = new tela_html('/srv/caracracha/templates/autenticado.html');
					$tela->add_substituicao('url_mudar_senha',$url_do_sistema."/mudar_senha.php?login=".$this->usuario);
					$tela->add_substituicao('mensagem','Voc&#234; est&#225; autenticado.');
					$tela->add_substituicao('desconectar',"$url_do_sistema?desconectar=".$this->usuario);
					$tela->exibir_tela();
					exit();
					
				}else{
					erro_fatal("ERRO_EXECUCAO_CRIA_SESSAO");
				}
			}else{
				erro_fatal("ERRO_ARQ_CRIA_SESSAO_EXEC");
			}
		}else{
			erro_fatal("ERRO_ARQ_CRIA_SESSAO");
		}
	}
}

?>
