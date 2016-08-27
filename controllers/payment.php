<?php

require_once "payu-php-sdk-4.5.6/lib/PayU.php";

class Payment extends MX_Controller {

    function Payment() {
        parent::__construct();
        $this->load->helper('template_inheritance');
        $this->load->library('session');
        $this->load->library('encrypt');
        $this->load->helper('form');
    }

    function index() {
        $account_data = $this->session->userdata("accountinfo");

        $data = $this->data_base($account_data);
        $system_config = common_model::$global_config['system_config'];
        if($system_config['payulatam_mode'] == 0) {
            $data['apiKey'] = $system_config['payulatam_apikey'];
            $data['payulatam_url'] = $system_config['payulatam_url'];
        }else{
            $data = array_merge($data, $this->data_test());
            $data['apiKey'] = '4Vj8eK4rloUd272L48hsrarnUA';
            $data['payulatam_url'] = 'https://sandbox.gateway.payulatam.com/ppp-web-gateway/';
        }
        $data["merchantId"] = $system_config['payulatam_merchantid'];
        $data["accountId"] = $system_config['payulatam_accountid'];
        
        $data["checkValue"] = $this->encrypt->encode($data['merchantId']);
        $data["gateway_tax"] = 0;
        $data["from_currency"] = $this->common->get_field_name('currency', 'currency', $account_data["currency_id"]);
        $data["to_currency"] = Common_model::$global_config['system_config']['base_currency'];
        $this->load->view("user_payment",$data);
    }

    private function data_base($account_data) {
        return array(
            'customerId' => $account_data['id'],
            'buyerEmail' => 'dirindesa@neurotec.c',
            'buyerFullName' => 'Neurotec Tecnologia S.A.S',
            'description' => 'Refill account ' . $account_data['number']
        );
    }
    
    private function data_test() {
        $data["merchantId"] = "508029";
        $data["accountId"] = "512321";
        $data["apiKey"] = "4Vj8eK4rloUd272L48hsrarnUA";
        $data["checkValue"] = $this->encrypt->encode($data['merchantId']);
        $data["referenceCode"] = "TestPayU";
        return $data;
    }
            
    function convert_amount($amount){
        $amount = $this->common_model->add_calculate_currency($amount,"","",true,false);
        echo number_format($amount,2);
    }

}
?>