<?php


class PayUTransactionResponseCode {

	/** Error transaction code */
	const ERROR = 'ERROR';
	/** Approved transaction code */
	const APPROVED = 1;
	/** Transaction declined by the entity code */
	const ENTITY_DECLINED = 5;
	/** Transaction rejected by anti fraud system code */
	const ANTIFRAUD_REJECTED = 23;
	/** Transaction expired code */
	const EXPIRED_TRANSACTION = 20;
	const DIGITAL_CERTIFICATE_NOT_FOUND = 9995;
	/** Transaction rejected by payment network */
	const PAYMENT_NETWORK_REJECTED = 4;
	/** Invalid data code */
	const INVALID_EXPIRATION_DATE_OR_SECURITY_CODE = 12;
	/** Insufficient funds code */
	const INSUFFICIENT_FUNDS = 6;
	/** Credit card not authorized code */
	const CREDIT_CARD_NOT_AUTHORIZED_FOR_INTERNET_TRANSACTIONS = 22;
	/** Transaction is not valid code */
	const INVALID_TRANSACTION = 14;
	/** Credit card is not valid code */
	const INVALID_CARD = 7;
	/** Credit card is restricted code */
	const RESTRICTED_CARD = 9;
	/** Need to contact the entity code */
	const CONTACT_THE_ENTITY = 8;
	/** Need to repeat transaction code */
	const REPEAT_TRANSACTION = 13;
	const BANK_UNREACHABLE = 9996;
	/** Amount not valid code */
	const EXCEEDED_AMOUNT = 17;
    const ABANDONED_TRANSACTION = 18;
}


class Response extends MX_Controller {

    function Payment() {
        parent::__construct();
        $this->load->helper('template_inheritance');

        $this->load->library('encrypt');
        $this->load->helper('form');
        $this->load->model('db_model');

        $this->load->library('session');
    }

    function index() {
        openlog("payulatam", LOG_PID | LOG_PERROR | LOG_DEBUG, LOG_LOCAL0);
        syslog(LOG_DEBUG, json_encode($_POST));
        syslog(LOG_DEBUG, json_encode($_GET));
        closelog();
        $this->process();

    }

    function process(){

      if(count($_POST)>0)
          {
              $response_arr=$_POST;
              //$fp=fopen("/var/log/astpp/astpp_payment.log","w+");
              $fp=fopen("/tmp/astpp_payment.log","w+");
              $date = date("Y-m-d H:i:s");
              fwrite($fp,"====================".$date."===============================\n");
              foreach($response_arr as $key => $value){	  
                  fwrite($fp,$key.":::>".$value."\n");
              }
              $this->save_transaction($response_arr);
              //$payment_check = $this->encrypt->decode($response_arr['checkValue']);
              if( ($response_arr["state_pol"] == PayUTransactionResponseCode::APPROVED /*&& $payment_check == $response_arr["merchant_id"]*/) ){

                  $balance_amt = $response_arr["value"]; //es custom
                  $account_data = (array)$this->db->get_where("accounts", array("id" => $response_arr["extra1"]))->first_row();
                  $currency = (array)$this->db->get_where('currency', array("id"=>$account_data["currency_id"]))->first_row();
                  $date = date('Y-m-d H:i:s');
                  $parent_id =$account_data['reseller_id'] > 0 ? $account_data['reseller_id'] : '-1';
                  $payment_arr = array(
                      "accountid"=> $response_arr["extra1"],
                      "payment_mode"=>"1","credit"=>$balance_amt,
                      "type"=>"PAYULATAM",
                      "payment_by"=>$parent_id,
                      "notes"=>"Payment Made by Payulatam on date:-".$date,
                      "txn_id"=>$response_arr["transaction_id"],
                      'payment_date'=>gmdate('Y-m-d H:i:s',strtotime($response_arr['transaction_date'])));
                  $this->db->insert('payments', $payment_arr);
                  $this->db->select('invoiceid');
                  $this->db->order_by('id','desc');
                  $this->db->limit(1);
                  $last_invoice_result=(array)$this->db->get('invoices')->first_row();
                  $last_invoice_ID=isset($last_invoice_result['invoiceid'] ) && $last_invoice_result['invoiceid'] > 0 ?$last_invoice_result['invoiceid'] : 1;
                  $reseller_id=$account_data['reseller_id'] > 0 ? $account_data['reseller_id'] : 0;
                  $where="accountid IN ('".$reseller_id."','1')";
                  $this->db->where($where);
                  $this->db->select('*');
                  $this->db->order_by('accountid', 'desc');
                  $this->db->limit(1);
                  $invoiceconf = $this->db->get('invoice_conf');
                  $invoiceconf = (array)$invoiceconf->first_row();
                  $invoice_prefix=$invoiceconf['invoice_prefix'];

                  $due_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s")." +".$invoiceconf['interval']." days"));  
                  $invoice_id=$this->generate_receipt($account_data['id'],$balance_amt,$account_data,$last_invoice_ID+1,$invoice_prefix,$due_date);
                  $details_insert=array(
                      'created_date'=>$date,
                      'credit'=>$balance_amt,
                      'debit'=>'-',
                      'accountid'=>$account_data["id"],
                      'reseller_id'=>$account_data['reseller_id'],
                      'invoiceid'=>$invoice_id,
                      'description'=>"Payment Made by Payulatam on date:-".$date,
                      'item_type'=>'Refill',
                      'before_balance'=>$account_data['balance'],
                      'after_balance'=>$account_data['balance']+$balance_amt,
                  );
                  $this->db->insert("invoice_details", $details_insert); 
                  $this->db_model->update_balance($balance_amt,$account_data["id"],"credit");            
                  if($parent_id > 0){
                      $reseller_ids=$this->common->get_parent_info($parent_id,0);
                      $reseller_ids=rtrim($reseller_ids,",");
                      $reseller_arr=explode(",",$reseller_ids);
                      if(!empty($reseller_arr)){
                          foreach($reseller_arr as $key=>$reseller_id){
                              $account_data = (array)$this->db->get_where("accounts", array("id" => $reseller_id))->first_row();
                              $this->db->select('invoiceid');
                              $this->db->order_by('id','desc');
                              $this->db->limit(1);
                              $last_invoice_result=(array)$this->db->get('invoices')->first_row();
                              $last_invoice_ID=$last_invoice_result['invoiceid'];
                              $reseller_id=$account_data['reseller_id'] > 0 ? $account_data['reseller_id'] : 0;
                              $where="accountid IN ('".$reseller_id."','1')";
                              $this->db->where($where);
                              $this->db->select('*');
                              $this->db->order_by('accountid', 'desc');
                              $this->db->limit(1);
                              $invoiceconf = $this->db->get('invoice_conf');
                              $invoiceconf = (array)$invoiceconf->first_row();
                              $invoice_prefix=$invoiceconf['invoice_prefix'];
                              $due_date = gmdate("Y-m-d H:i:s",strtotime(gmdate("Y-m-d H:i:s")." +".$invoiceconf['interval']." days"));
                              $invoice_id=$this->generate_receipt($account_data['id'],$balance_amt,$account_data,$last_invoice_ID+1,$invoice_prefix,$due_date);
                              $parent_id=$account_data['reseller_id'] > 0 ? $account_data['reseller_id'] : -1;
                              $payment_arr = array("accountid"=> $account_data["id"],
                              "payment_mode"=>"1",
                              "credit"=>$balance_amt,
                              "type"=>"PAYULATAM",
                              "payment_by"=>$parent_id,
                              "notes"=>"Your account has been credited due to your customer account recharge done by payulatam",
                              "txn_id"=>$response_arr["transaction_id"],
                              'payment_date'=>gmdate('Y-m-d H:i:s',strtotime($response_arr['transaction_date'])),
                              );
                              $this->db->insert('payments', $payment_arr);
                              $details_insert=array(
                                  'created_date'=>$date,
                                  'credit'=>$balance_amt,
                                  'debit'=>'-',
                                  'accountid'=>$account_data['id'],
                                  'reseller_id'=>$parent_id,
                                  'invoiceid'=>$invoice_id,
                                  'description'=>"Your account has been credited due to your customer account recharge done by payulatam",
                                  'item_type'=>'Refill',
                                  'before_balance'=>$account_data['balance'],
                                  'after_balance'=>$account_data['balance']+$balance_amt,
                              );
                              $this->db->insert("invoice_details", $details_insert); 
                              $this->db_model->update_balance($balance_amt,$account_data["id"],"credit");  			         
                          }
                      }
                  }
                  redirect(base_url() . 'user/user/');
              }
          }
      redirect(base_url() . 'user/user/');
    }

    private function save_transaction($response_arr) {
        $txn_data = array(
            'account_id' => $response_arr['extra1'],
            'state_pol' => $response_arr['state_pol'],
            'value' => floatval($response_arr['value']),
            'tax' => $response_arr['tax'],
            'exchange_rate' => floatval($response_arr['exchange_rate']),
            'currency' => $response_arr['currency'],
            'transaction_id' => $response_arr['transaction_id'],
            'transaction_date' => $response_arr['transaction_date'],
            'date' => $response_arr['date'],
            'extra' => json_encode($response_arr)
        );
        $this->db->insert('payulatam_transactions', $txn_data);
    }

    private function generate_receipt($accountid,$amount,$accountinfo,$last_invoice_ID,$invoice_prefix,$due_date){
		$invoice_data = array(
            "accountid"=>$accountid,
            "invoice_prefix" =>$invoice_prefix,
            "invoiceid"=>'0000'.$last_invoice_ID,
            "reseller_id"=>$accountinfo['reseller_id'],
            "invoice_date"=>gmdate("Y-m-d H:i:s"),
            "from_date"=>  gmdate("Y-m-d H:i:s"),
            "to_date"=>gmdate("Y-m-d H:i:s"),
            "due_date"=>$due_date,
            "status"=>1,
            "balance"=>$accountinfo['balance'],
            "amount"=>$amount,"type"=>'R',"confirm"=>'1');            
        $this->db->insert("invoices",$invoice_data);
        $invoiceid = $this->db->insert_id();    
        return  $invoiceid;  
    }


}