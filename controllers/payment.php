<?php
//@author Jovany Leandro G.C <dirindesa@neurotec.c>

require_once "payu-php-sdk-4.5.6/lib/PayU.php";
require_once "config.php";

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
        $system_config = common_model::$global_config['system_config'];
        $data = $this->data_base($account_data, $system_config);

        if($system_config['payulatam_mode'] == 0) {
            $data['testMode'] = false;
            $data['apiKey'] = $system_config['payulatam_apikey'];
            $data['payulatam_url'] = $system_config['payulatam_url'];
            $data['referenceCode'] = 'Recharge_' . $account_data['number'] .'_' . strftime("%F%T");
        }else{
            $data['testMode'] = true;
            $data = array_merge($data, $this->data_test());
            $data['apiKey'] = '4Vj8eK4rloUd272L48hsrarnUA';
            $data['payulatam_url'] = 'https://sandbox.gateway.payulatam.com/ppp-web-gateway/';
        }

        $data['payerPhone'] = $account_data['telephone_1'];
        $data['payerMobilePhone'] = $account_data['telephone_2'];
        $data['payerEmail'] = $account_data['email'];
 
        //params need for transaction
        $data["merchantId"] = $system_config['payulatam_merchantid'];
        $data["accountId"] = $system_config['payulatam_accountid'];
        $data["checkValue"] = md5($data['merchantId'].PAYULATAM_SALT);
        $data["gateway_tax"] = floatval($system_config['payulatam_gatewayTax']);
        $data["from_currency"] = $this->common->get_field_name('currency', 'currency', $account_data["currency_id"]);
        $data["to_currency"] = Common_model::$global_config['system_config']['base_currency'];
        $this->load->view("user_payment",$data);
    }

    private function data_base($account_data, $system_config) {

        return array(
            'customerId' => $account_data['id'],
            'buyerEmail' => $account_data['email'],
            'buyerFullName' => $account_data['company_name'],
            'description' => 'Refill account ' . $account_data['number']
        );
    }
    
    private function data_test() {
        $data["merchantId"] = "508029";
        $data["accountId"] = "512321";
        $data["apiKey"] = "4Vj8eK4rloUd272L48hsrarnUA";
        $data["referenceCode"] = "TestPayU";
        return $data;
    }
            
    function convert_amount($amount){
        $amount = $this->common_model->add_calculate_currency($amount,"","",true,false);
        echo number_format($amount,2);
    }

}
?>
