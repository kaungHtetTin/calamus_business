create a partner_payment_histories table in the database with the following columns:
id, 
partner_id (foreign key to partners table),
payment_method (varchar), 
account_number (varchar), 
account_name (varchar), 
amount (decimal), 
status (enum: pending, received, rejected), 
transaction_screenshot (varchar), 
created_at (timestamp), 
updated_at (timestamp)

create new page or section with name partner_payment_histories in partner portal.
fetch the payment histories from the partner_payment_histories table.
all filters should be applied to the payment histories. for example: status, period, etc.
the UI should be attractive and user friendly.
for the action, the partner can change the status of the payment history. received or rejected.

