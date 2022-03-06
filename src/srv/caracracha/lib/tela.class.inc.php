<?PHP

class tela_html {
	var $tela;
	var $array_substituicoes;
	
	function tela_html ($arquivo) {
		
		global $url_do_sistema;
		
		if(file_exists($arquivo) and is_readable($arquivo)){
			$this->tela = file_get_contents($arquivo);
		}else{
			erro_login("ERRO_ARQ_TELA");
			exit ();
		}
				
		$this->add_substituicao("url_autenticacao",$url_do_sistema);
		
		if($_POST['url_destino']){
			$this->add_substituicao("url_destino",$_POST['url_destino']);
		}else{
			$this->add_substituicao("url_destino",$_SERVER['HTTP_HOST']);
		}
	}	

	function add_substituicao ($id, $texto) {
		if(!$this->array_substituicoes) {
			$this->array_substituicoes = array ();
		}

		$this->array_substituicoes[$id] = $texto;
	}

	function gerar_tela () {		
		$tela_out = $this->tela;
				
		if($this->array_substituicoes){
			foreach ($this->array_substituicoes as $id => $texto) {
				$tela_out = str_replace("%".$id."%", $texto, $tela_out);
			}
		}
		
		return $tela_out;
	}
	
	function exibir_tela () {
		global $empresa;
		//Evitando que o Navegador faa cache da pagina
		
		$this->add_substituicao("titulo", $empresa);
		echo $this->gerar_tela ();
	}
}

?>
