create a partner_payment_transactions table in the database with the following columns:
id, 
partner_id (foreign key to partners table),
payment_method (varchar), 
account_number (varchar), 
account_name (varchar), 
amount (decimal), 
status (varchar), 
transaction_screenshot (varchar), 
created_at (timestamp), 
updated_at (timestamp)


<br />
<b>Fatal error</b>:  Uncaught Error: Call to undefined method Database::write() in C:\xampp\htdocs\business\classes\payment_methods_manager.php:76
Stack trace:
#0 C:\xampp\htdocs\business\api\payment_methods.php(101): PaymentMethodsManager-&gt;updatePaymentMethod('1', '9', 'KBZ pay', '09516547500', 'Kaung Htet Tin')
#1 {main}
  thrown in <b>C:\xampp\htdocs\business\classes\payment_methods_manager.php</b> on line <b>76</b><br />


