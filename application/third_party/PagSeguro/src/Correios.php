<?php

namespace PagSeguro;

use PagSeguro\Http;
use PagSeguro\XmlParser;

class Correios {
	
	// URL do WebService dos Correios
	private $_URL = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo';

	private $_params = array(
		// Código e senha da empresa, se você tiver contrato com os correios, se não tiver deixe vazio.
		'nCdEmpresa' => '',
		'sDsSenha'   => '',

		// CEP de origem e destino. Esse parametro precisa ser uma string apenas numérica.
		'sCepOrigem' => NULL,
		'sCepDestino' => NULL,

		// O peso do produto deverá ser enviado em quilogramas, leve em consideração que isso deverá incluir o peso da embalagem.
		'nVlPeso' => '',

		// O formato tem apenas três opções:
		// - 1 para caixa/pacote
		// - 2 para rolo/prisma
		// - 3 para envelope
		'nCdFormato' => 1,

		// O comprimento, altura, largura e diametro deverá ser informado em centímetros e somente números
		'nVlComprimento' => '',
		'nVlAltura' => '',
		'nVlLargura' => '',
		'nVlDiametro' => '',

		// Aqui você informa se quer que a encomenda deva ser entregue somente para uma determinada pessoa após confirmação por RG. Use "s" e "n".
		'sCdMaoPropria' => 'N',

		// O valor declarado serve para o caso de sua encomenda extraviar, então você poderá recuperar o valor dela. Vale lembrar que o valor da encomenda interfere no valor do frete. Se não quiser declarar pode passar 0 (zero).
		'nVlValorDeclarado' => '0',

		// Se você quer ser avisado sobre a entrega da encomenda. Para não avisar use "n", para avisar use "s".
		'sCdAvisoRecebimento' => 'S',

		// Formato no qual a consulta será retornada, podendo ser: Popup é mostra uma janela pop-up - URL é envia os dados via post para a URL informada - XML é Retorna a resposta em XML
		'StrRetorno' => 'xml',

		// Código do Serviço, pode ser apenas um ou mais. Para mais de um apenas separe por virgula.
		// Por padrão, Sedex e PAC
		'nCdServico' => '40010,41106'
	);

	private function parseXml($data) {
		// Creating an xml parser 
		$xmlParser = new XmlParser($data);
		// Verifying if is an XML
		if ($xml = $xmlParser->getResult()) {
			return $xml;
		} else {
			throw new \Exception("[$data] is not an XML");
		}
	}

	private function setError( $msg ){
		echo "<b>".get_class($this).":</b> {$msg}";
		die();
	}

	public function setCredenciais( $code, $senha ){
		$this->_params['nCdEmpresa'] = $code;
		$this->_params['sDsSenha'] = $senha;
		return $this;
	}

	public function setCepOrigem( $cep ){
		$this->_params['sCepOrigem'] = preg_replace('/\D/', '', $cep);
		return $this;
	}

	public function setCepDestino( $cep ){
		$this->_params['sCepDestino'] = preg_replace('/\D/', '', $cep);
		return $this;
	}

	public function setFormato( $formato ){
		if( in_array($formato, array(1,2,3)) ){
			$this->_params['nCdFormato'] = $formato;
			return $this;
		}
		$this->setError('Formato incorreto!');
	}

	public function setDimensoes($peso, $comp, $larg, $alt, $diam = 0){
		$this->_params['nVlPeso'] = $peso;
		$this->_params['nVlComprimento'] = $comp;
		$this->_params['nVlLargura'] = $larg;
		$this->_params['nVlAltura'] = $alt;
		$this->_params['nVlDiametro'] = $diam;
		return $this;
	}

	public function setValorDeclarado( $valor ){
		$this->_params['nVlValorDeclarado'] = $valor;
		return $this;
	}

	public function consultar(){
		// Verifica se os CEPs foram preenchidos
		if( is_null($this->_params['sCepDestino']) || strlen($this->_params['sCepDestino']) != 8 ){
			$this->setError('CEP de destino inválido!');
		}
		if( is_null($this->_params['sCepOrigem']) || strlen($this->_params['sCepOrigem']) != 8 ){
			$this->setError('CEP de origem inválido!');
		}

		// Verifica se as dimensões foram preenchidas
		if( empty($this->_params['nVlPeso']) || empty($this->_params['nVlDiametro']) || empty($this->_params['nVlAltura']) || empty($this->_params['nVlLargura']) || empty($this->_params['nVlComprimento']) ){
			$this->setError('Algo de errado com as dimensões deste produto!');
		}

		// Creating a http connection (CURL abstraction)
		$httpConnection = new Http();
		// Request to PagSeguro Session API using Credentials
		$httpConnection->post($this->_URL, $this->_params);
		// Request OK getting the result
		if ($httpConnection->getStatus() === 200) {
			$dados = $httpConnection->getResponse();
			$dados = preg_replace('/(<cResultado)(.*)?>/', '<cResultado>', $dados);
			$dados = simplexml_load_string($dados);
			$dados = (array)$dados->Servicos;
			return $dados['cServico'];
		} else {
			throw new \Exception("API Request Error: ".$httpConnection->getStatus());
		}
	}

}