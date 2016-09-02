INSERT INTO menu_modules VALUES(NULL, "Payulatam", "payulatam", "payulatam/user_payment/", "Payment", "ListAccounts.png", 0, 5.3);

UPDATE userlevels SET module_permissions = CONCAT(module_permissions, ",", (SELECT id FROM menu_modules WHERE module_url =  "payulatam/user_payment/")) where userlevelid = 0;

INSERT INTO menu_modules VALUES(NULL, 'Payulatam', 'payulatam', 'systems/configuration/payulatam', 'Configuration', '', '0', 80.12);
UPDATE userlevels SET module_permissions = CONCAT(module_permissions, ",", (SELECT id FROM menu_modules WHERE module_url =  'systems/configuration/payulatam')) where userlevelid = -1;
INSERT INTO system VALUES(NULL,'payulatam_mode', 'Payulatam Mode', '0', 'enable_disable_option', 'Payulatam Mode you must set Merchant Id and Account Id', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_merchantid', 'Merchant Id', '508029', 'default_system_input', 'Set Merchant ID', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_accountid', 'AccountId Id', '512321', 'default_system_input', 'Set Account ID', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_url', 'Url', 'https://gateway.payulatam.com/ppp-web-gateway/', 'default_system_input', 'Url payulatam', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_apikey', 'Api Key', '4Vj8eK4rloUd272L48hsrarnUA', 'default_system_input', 'Url payulatam', NULL, 0, 0, 'payulatam');

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

