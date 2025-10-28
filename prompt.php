I added some columns to partners table:
(Address Information)
address
city
state
(National ID Card Information)
national_id_card_number
national_id_card_front_image
national_id_card_back_image

make a form for the user to fill out the address information and the national id card information and save it to the database 
in profile settings page

if a new partner is created, the address information and the national id card information should be saved to the database
or show the warning message to the user to fill out the address information and the national id card information in the dashboard
'Please fill out the address information and the national id card information. Otherwise, you will not be able to receive payments.'

I added a new column to partners table: account_verified
account_verified is a boolean column with default value 0
when the user fills out the address information and the national id card information, the admin will checkout the information 
and set the account_verified to 1

make a new page for partners 'account status' to show the account status of the partner
the status:
email_verified: 0 or 1
payment_method added or not 
personal_information added or not
account_verified: 0 or 1
status: active or inactive

tell the user that only these 5 status are required to receive payments.
for admin dashboard, don't make any implementation for this page. It will be next step.


It's time to implement from the admin dashboard to check the account status of the partner
in the page of partners.php, add new table when a partner is present to check the account status of the partner
a partner will be listed in the table only when
email_verified is 1
payment_method added
personal_information added. 

the table is placed before the table of partners.
if no partner is listed in the table, hide the table.   
