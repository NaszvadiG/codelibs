<?php defined('BASEPATH') OR exit('No direct script access allowed');

define('DS', DIRECTORY_SEPARATOR);
require(APPPATH . 'third_party' . DS . 'Cnab' . DS . 'autoload.php');

class CnabPHP {

	private function assertEquals($param1, $param2){
		if( $param1 != $param2 ){
			throw new Exception("Is not equal!", 1);
		}
		return TRUE;
	}

	private function assertTrue($param){
		if( $param != TRUE ){
			throw new Exception("Is not true!", 1);
		}
		return TRUE;
	}

	private function assertNotNull($param){
		if( is_null($param) ){
			throw new Exception("Is null!", 1);
		}
		return TRUE;
	}

    public function testContemOsBancosEsperados()
    {
        $this->assertTrue(Cnab\Banco::existBanco(Cnab\Banco::ITAU));
        $this->assertTrue(Cnab\Banco::existBanco(Cnab\Banco::CEF));
        $this->assertTrue(Cnab\Banco::existBanco(Cnab\Banco::SANTANDER));
        $this->assertTrue(Cnab\Banco::existBanco(Cnab\Banco::BRADESCO));
    }

    public function retorno(){
        $cnabFactory = new Cnab\Factory();
        $arquivo = $cnabFactory->createRetorno(FCPATH . 'retorno.ret');
        $detalhes = $arquivo->listDetalhes();
        foreach($detalhes as $detalhe) {
            if($detalhe->getValorRecebido() > 0) {
                $nossoNumero   = $detalhe->getNossoNumero();
                $valorRecebido = $detalhe->getValorRecebido();
                $dataPagamento = $detalhe->getDataOcorrencia();
                $carteira      = $detalhe->getCarteira();
                echo $nossoNumero;
            }
        }
    }

    public function remessa(){
        $codigo_banco = Cnab\Banco::ITAU;
        $arquivo = new Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
        $arquivo->configure(array(
            'data_geracao'  => new DateTime(),
            'data_gravacao' => new DateTime(), 
            'nome_fantasia' => 'Nome Fantasia da sua empresa', // seu nome de empresa
            'razao_social'  => 'Razão social da sua empresa',  // sua razão social
            'cnpj'          => '111', // seu cnpj completo
            'banco'         => $codigo_banco, //código do banco
            'logradouro'    => 'Logradouro da Sua empresa',
            'numero'        => 'Número do endereço',
            'bairro'        => 'Bairro da sua empresa', 
            'cidade'        => 'Cidade da sua empresa',
            'uf'            => 'Sigla da cidade, ex SP',
            'cep'           => 'CEP do endereço da sua cidade',
            'agencia'       => '1111', 
            'conta'         => '22222', // número da conta
            'conta_dac'     => '2', // digito da conta
        ));

        // você pode adicionar vários boletos em uma remessa
        $arquivo->insertDetalhe(array(
            'codigo_ocorrencia' => 1, // 1 = Entrada de título, futuramente poderemos ter uma constante
            'nosso_numero'      => '1234567',
            'numero_documento'  => '1234567',
            'carteira'          => '109',
            'especie'           => Cnab\Especie::ITAU_DUPLICATA_DE_SERVICO, // Você pode consultar as especies Cnab\Especie
            'valor'             => 100.39, // Valor do boleto
            'instrucao1'        => 2, // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
            'instrucao2'        => 0, // preenchido com zeros
            'sacado_nome'       => 'Nome do cliente', // O Sacado é o cliente, preste atenção nos campos abaixo
            'sacado_tipo'       => 'cpf', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
            'sacado_cpf'        => '111.111.111-11',
            'sacado_logradouro' => 'Logradouro do cliente',
            'sacado_bairro'     => 'Bairro do cliente',
            'sacado_cep'        => '11111222', // sem hífem
            'sacado_cidade'     => 'Cidade do cliente',
            'sacado_uf'         => 'SP',
            'data_vencimento'   => new DateTime('2014-06-08'),
            'data_cadastro'     => new DateTime('2014-06-01'),
            'juros_de_um_dia'     => 0.10, // Valor do juros de 1 dia'
            'data_desconto'       => new DateTime('2014-06-01'),
            'valor_desconto'      => 10.0, // Valor do desconto
            'prazo'               => 10, // prazo de dias para o cliente pagar após o vencimento
            'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
            'mensagem'            => 'Descrição do boleto',
            'data_multa'          => new DateTime('2014-06-09'), // data da multa
            'valor_multa'         => 10.0, // valor da multa
        ));

        // para salvar
        $arquivo->save('meunomedearquivo.txt');
    }
}