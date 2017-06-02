<?php
//@author Jovany Leandro G.C <dirindesa@neurotec.c>

require_once "payu-php-sdk-4.5.6/lib/PayU.php";

//para registrar en el menu un modulo
//se inserta en *menu_modules*.
//se inserta en *userleves*, el id del module en *menu_modules*

//el module *user* utiliza el module *login* en el metodo *paypal_response*
//ya que este es el controlador por defecto y puede ser ejecutado sin necesitada
//de autenticacion.

/*
array(47) {
  ["id"]=>
  string(1) "2"
  ["number"]=>
  string(10) "2457848300"
  ["reseller_id"]=>
  string(1) "0"
  ["pricelist_id"]=>
  string(1) "1"
  ["status"]=>
  string(1) "0"
  ["sweep_id"]=>
  string(1) "2"
  ["creation"]=>
  string(19) "2016-07-25 11:26:24"
  ["credit_limit"]=>
  string(7) "0.00000"
  ["posttoexternal"]=>
  string(1) "0"
  ["balance"]=>
  string(7) "1.00000"
  ["password"]=>
  string(43) "GpMl9v2b32xNILRXMxHxrStFNd4I26bTNDAEG2eYQDM"
  ["first_name"]=>
  string(7) "default"
  ["last_name"]=>
  string(8) "customer"
  ["company_name"]=>
  string(5) "ASTPP"
  ["address_1"]=>
  string(6) "adress"
  ["address_2"]=>
  string(0) ""
  ["postal_code"]=>
  string(0) ""
  ["province"]=>
  string(0) ""
  ["city"]=>
  string(0) ""
  ["country_id"]=>
  string(2) "85"
  ["telephone_1"]=>
  string(0) ""
  ["telephone_2"]=>
  string(0) ""
  ["email"]=>
  string(21) "yourcustomer@test.com"
  ["language_id"]=>
  string(1) "0"
  ["currency_id"]=>
  string(2) "59"
  ["maxchannels"]=>
  string(1) "1"
  ["interval"]=>
  string(1) "0"
  ["dialed_modify"]=>
  string(0) ""
  ["type"]=>
  string(1) "0"
  ["timezone_id"]=>
  string(2) "49"
  ["inuse"]=>
  string(1) "0"
  ["deleted"]=>
  string(1) "0"
  ["notify_credit_limit"]=>
  string(1) "0"
  ["notify_flag"]=>
  string(1) "1"
  ["notify_email"]=>
  string(0) ""
  ["commission_rate"]=>
  string(1) "0"
  ["invoice_day"]=>
  string(1) "1"
  ["pin"]=>
  string(10) "2457848300"
  ["first_used"]=>
  string(19) "2016-07-26 11:26:24"
  ["expiry"]=>
  string(19) "2046-07-25 11:26:24"
  ["validfordays"]=>
  string(4) "3652"
  ["local_call_cost"]=>
  string(7) "0.00000"
  ["pass_link_status"]=>
  string(1) "0"
  ["local_call"]=>
  string(1) "0"
  ["charge_per_min"]=>
  string(1) "1"
  ["is_recording"]=>
  string(1) "0"
  ["allow_ip_management"]=>
  string(1) "0"
}
*/

class Payulatam extends MX_Controller {
    
    function Payulatam() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->helper('form');
        $this->load->library('astpp/permission');
        $this->load->library("astpp/form");
        $this->load->model('Auth_model');
        $this->load->model('Astpp_common');
        $this->load->model('user/user_model');
        $this->load->helper('captcha');
        $this->load->helper('template_inheritance');
        $this->load->library('astpp/common');
        $this->load->library('astpp/email_lib');
        $this->load->model('db_model');
        $this->load->model('common_model');
        $this->load->library('session');
        $this->load->library('encrypt');
        $this->load->model('Astpp_common');

        error_reporting(-1);
        ini_set('display_errors', 'On');

    }

    function index() {
        if ($this->session->userdata('user_login') == FALSE)
            redirect(base_url() . 'login/login');
        $data['page_title'] = 'Dashboard';
        $this->load->view('view_user_dashboard', $data);
    }

    function user_payment($action="") {
        $this->load->module("payulatam/payment");
        if($action=="GET_AMT"){
            $amount = $this->input->post("value",true);
            $this->payment->convert_amount($amount);
        }else{
            $this->payment->index();
        }
    }

}
