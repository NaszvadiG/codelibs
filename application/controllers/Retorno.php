<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Retorno extends CI_Controller {

	public function __construct(){
		parent::__construct();

		$this->load->library(array('CnabPHP'=>'cnabphp'));
	}

	public function index(){
		// $this->cnabphp->testContemOsBancosEsperados();
		// $this->cnabphp->retorno();
		$this->cnabphp->remessa();
	}

}