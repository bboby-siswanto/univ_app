<!DOCTYPE html>
<html>
	<body>
	<style>
		table {
		border-collapse: collapse;
		}

		table, td, th {
		border: 1px solid black;
		}
	</style>
		<p>Greetings from IULI.<br></p>
		<p>Kindly remind you for this payment:</p>
		<h2>Invoice Number: <?=$invoice_data->invoice_number?></h2>
		<p>Student Name: <strong><?=$invoice_data->personal_data_name?></strong></p>
        <p><br></p>
        <p><?=$invoice_details[0]->fee_description;?> Fee IDR <?="".number_format($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_amount_total, 0, '.', ',').""?></p>
		<p><strong>Fee Details:</strong></p>
		<ul>
			<li>ADMINISTRATION FEE IDR 5,000,000</li>
			<li>TELEX VISA and e-VISA FEE IDR 6,050,000</li>
		</ul>
		<p>
			Payment Type: Full Amount 
			<ul>
				<li>
					IDR <?=number_format($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_amount_total, 0, '.', ',');?> OR Euro 690 <br>
					(you may transfer in Euro to BNINIDJA<?=$sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_va_number;?>)
				</li>
			</ul>
		</p>
        <br>

		<p>Please transfer payment to:</p>
		<p>
            Account Number: BNINIDJA<?=$sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_va_number;?> <br>
            (swift code: BNINIDJA follow by BNI Virtual Account Number)
        </p>
		<p>Beneficiary Name: <?=$invoice_data->personal_data_name?></p>
		<p>Bank: Bank Negara Indonesia (BNI) BSD Tangerang Branch – Indonesia</p>
		<p>Due Date: <?=date('j F Y', strtotime($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_deadline))?></p>
		
		<h3>Notes:</h3>
		<ol>
            <li>BNI will reject payment which is not at the exact amount and account as stated above.</li>
			<li>If payment for the Full Payment Method past due date, the system will automatically reject the virtual account.</li>
			<li>If student makes a payment without using BNI Virtual Account, then we assume no payment has been made.</li>
            <li>Please ignore this email if you have already paid.</li>
            <li><strong>BNI Call Officer (BCO) and Interactive Voice Response (IVR) : 1500046 (Open 24/7)</strong></li>
		</ol>
		
		<br><br><br><br>
		<p>Best regards,</p>
		<p>IULI ANF Department</p>
	</body>
</html>