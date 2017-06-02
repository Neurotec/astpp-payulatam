<?php
//@author Jovany Leandro G.C <dirindesa@neurotec.c>
//@deprecated responseUrl si se omite lo maneja directamente en payulatam y eso es el metodo que vamos a usar
/*
CREATE TABLE payulatam_response (
 id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 mechantId INT(12) NOT NULL,
 transactionState INT,
 transactionId CHAR(36) NOT NULL,
 signature VARCHAR(255) NOT NULL,
 referenceCode VARCHAR(255) NOT NULL,
 processingDate Date,
 TX_VALUE DECIMAL(14,2) NOT NULL,
 TX_TAX DECIMAL(14,2) NOT NULL,
 extra TEXT
) ENGINE = InnoDB;
 */
class payulatam_response_model extends CI_Model {

    function payulatam_response_model() {
        parent::_construct();
    }


    //http://developers.payulatam.com/es/web_checkout/variables.html
    function insert_from_json($data) {
    }
}