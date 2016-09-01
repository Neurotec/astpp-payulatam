INSERT INTO menu_modules VALUES(NULL, "Payulatam", "payulatam", "payulatam/user_payment/", "Payment", "ListAccounts.png", 0, 5.3);

UPDATE userlevels SET module_permissions = CONCAT(module_permissions, ",", (SELECT id FROM menu_modules WHERE module_url =  "payulatam/user_payment/")) where userlevelid = 0;

INSERT INTO menu_modules VALUES(NULL, 'Payulatam', 'payulatam', 'systems/configuration/payulatam', 'Configuration', '', '0', 80.12);
UPDATE userlevels SET module_permissions = CONCAT(module_permissions, ",", (SELECT id FROM menu_modules WHERE module_url =  'systems/configuration/payulatam')) where userlevelid = -1;
INSERT INTO system VALUES(NULL,'payulatam_mode', 'Payulatam Mode', '0', 'enable_disable_option', 'Payulatam Mode you must set Merchant Id and Account Id', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_merchantid', 'Merchant Id', '508029', 'default_system_input', 'Set Merchant ID', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_accountid', 'AccountId Id', '512321', 'default_system_input', 'Set Account ID', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_url', 'Url', 'https://gateway.payulatam.com/ppp-web-gateway/', 'default_system_input', 'Url payulatam', NULL, 0, 0, 'payulatam');
INSERT INTO system VALUES(NULL,'payulatam_apikey', 'Api Key', '4Vj8eK4rloUd272L48hsrarnUA', 'default_system_input', 'Url payulatam', NULL, 0, 0, 'payulatam');
