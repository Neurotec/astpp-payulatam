<?php


/*
  CREATE TABLE payulatam_transactions (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  account_id INT NOT NULL,
  state_pol CHAR(36) NOT NULL,
  value DECIMAL(14,2) NOT NULL,
  tax DECIMAL(14,2) NOT NULL,
  exchange_rate DECIMAL(14,2) NOT NULL,
  currency VARCHAR(10) NOT NULL,
  transaction_date Date,
  transaction_id CHAR(36) NOT NULL,
  date Date NOT NULL,
  extra BLOB
) ENGINE = InnoDB;
 */
class payulatam_confirmation_model extends CI_Model {

    function payulatam_transactions_model() {
        parent::_construct();
    }

    //http://developers.payulatam.com/es/web_checkout/variables.html
    function insert_from_json($data) {
    }
}