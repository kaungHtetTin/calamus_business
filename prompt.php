The partner have to promote the product of calamus education to get new vip subscribers.
You can learn the calamus education product from the following link:
https://www.calamuseducation.com/app-portfolio/easy-korean.php
https://www.calamuseducation.com/app-portfolio/easy-english.php
https://www.calamuseducation.com/calamus/index.php
https://www.calamuseducation.com/calamus/term.php
https://www.calamuseducation.com/calamus/privacy.php
https://play.google.com/store/apps/details?id=com.qanda.learnroom
https://play.google.com/store/apps/details?id=com.calamus.easykorean
https://www.calamuseducation.com/calamus/about_us.php
https://www.calamuseducation.com/calamus/contact_us.php


for admin  dashboard, add new section in drawer menu 'Earning Logs'
earning logs should have the following features:
get all data from the table 'partner_earnings'
add filtering feature with status and date range
add pagination feature

for admin dashboard, add new section in drawer menu 'Payout Logs'
the logs should have the following features:
get all data from the table 'partner_earnings' with group by partner_id and sum the total amount_received
add filtering feature with status and date range
order by total amount_received in descending order and status pending first and paid second
add pagination feature
add action button to payout the amount to the partner.

for process_payout.php, you have to handle as a page.
This is page show the following information:
partner Information:
payment methods
amount to payout
action button to payout the amount to the partner. (only add button, the implementation will be in the next step)

for admin dashboard, add new section in drawer menu 'Payout history'
the history should have the following features:
fetch the data from the table 'partner_payment_histories'
construct the table with the following columns:
partner name
payment method
payment account
amount
status
date
add pagination feature
add filtering feature with status and date range
add action button to view the payout details.

for partners.php add a button 'create new partner' to create a new partner.
this button will redirect to the create_partner.php page.
create_partner.php should have the following features:
form to create a new partner
form fields:
company name
contact name
email
phone
website
description
commission rate (default 10%)
password
confirm password
make private code generation
make status verification
make created at and updated at
submit button