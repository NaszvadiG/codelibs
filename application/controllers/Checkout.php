<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library(array('PagSeguro'=>'pagseguro'));
		$this->pagseguro->setCredentials('mateus460@gmail.com', 'F941B21FEF7E419C9CA8379025090314');
	}

	public function index(){
		$this->load->view('checkout/index');
	}

	public function payment(){
		/**
		 * Reference code
		 *
		 * 1 -> Week day (1-7)
		 * 2 -> Week number
		 * 3 -> Hour
		 * 4 -> Minute
		 * 5 -> Second
		 */
		$_POST['reference'] = date('NWHis');
		echo json_encode($this->pagseguro->doPayment($_POST));
	}

	public function sessionID(){
		echo $this->pagseguro->getSessionID();
	}

	public function abandoned(){
		$today = new DateTime();
		$today->modify('-3 days');

		$data['currentPage'] = isset($_GET['page']) ? $_GET['page'] : 1;
		
		$list = $this->pagseguro->transactionsAbandoned(array(
			'initialDate' => $today->format('Y-m-d\TH:i'),
			// 'finalDate' => date('Y-m-d\TH:i'),
			'page' => $data['currentPage'],
			'maxPageResults' => 5
			));

		$data['transactions'] = $list->transactionSearchResult->transactions->transaction;
		$data['resultsInThisPage'] = $list->transactionSearchResult->resultsInThisPage;
		$data['totalPages'] = $list->transactionSearchResult->totalPages;

		$this->load->view('checkout/transactionsAbandoned', $data);
	}

	public function transactions(){
		$today = new DateTime();
		$today->modify('-3 days');

		$data['currentPage'] = isset($_GET['page']) ? $_GET['page'] : 1;
		
		$list = $this->pagseguro->listTransactions(array(
			'initialDate' => $today->format('Y-m-d\TH:i'),
			// 'finalDate' => date('Y-m-d\TH:i'),
			'page' => $data['currentPage'],
			'maxPageResults' => 5
			));

		$data['transactions'] = $list->transactionSearchResult->transactions->transaction;
		$data['resultsInThisPage'] = $list->transactionSearchResult->resultsInThisPage;
		$data['totalPages'] = $list->transactionSearchResult->totalPages;

		$this->load->view('checkout/transactions', $data);
	}

	public function transaction(){
		if( isset($_GET['reference']) ){
			$transaction = $this->pagseguro->transactionByReference($_GET['reference']);
		}else if( isset($_GET['code']) ){
			$transaction = $this->pagseguro->transactionByCode($_GET['code']);
		}
		$data['transaction'] = $transaction->transaction;
		$this->load->view('checkout/transaction', $data);
	}

	public function cancelTransaction(){
		if( isset($_GET['code']) ){
			var_dump($this->pagseguro->cancelTransaction($_GET['code']));
		}
	}

	public function frete(){
		$this->load->view('checkout/frete');
	}

	public function calculafrete(){
		$this->load->library('frete');

		$dados = $this->frete->setCepOrigem('58.079-000')->setCepDestino($_POST['cepDestino'])->setFormato(1)->setDimensoes(0.3, 30, 20, 10)->setValorDeclarado(0)->consultar();

		echo json_encode($dados);
	}

}