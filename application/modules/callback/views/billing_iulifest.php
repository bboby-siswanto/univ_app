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
		<p>Kindly inform you for this payment:</p>
		<h2>Invoice Number: <?=$invoice_data->invoice_number?></h2>
		<p>Sponsor Name: <strong><?=$invoice_data->invoice_customer?></strong></p>
        <p><br></p>
        <!-- <p>IULIFest IDR <?="".number_format($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_amount, 0, '.', ',').""?></p> -->
		<?php
		$s_amount = $sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_amount;
		if ($invoice_data->invoice_number == 'INV-F2206190009') {
			$s_amount = $s_amount - 400000;
		?>
		<p><strong>Details:</strong></p>
		<ul>
			<li>Sponsor IULIFest IDR <?="".number_format($s_amount, 0, '.', ',').""?></li>
			<li>Tax 2% IDR 400,000</li>
		</ul>
		<?php
		}
		?>
		<p>Payment Type: Full Amount IDR <?="".number_format($s_amount, 0, '.', ',').""?></p>
        <br>

		<p>Please transfer payment to:</p>
		<p>
            Account Number: <strong><?=implode(' ', str_split($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_va_number, 4))?></strong>
        </p>
		<p>Beneficiary Name: <strong>Sponsorship IULIFest</strong> </p>
		<p>Bank: Bank Negara Indonesia (BNI) BSD Tangerang Branch – Indonesia</p>
		<p>Due Date: <?=date('j F Y', strtotime($sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_deadline))?></p>
		
		<h3>Notes:</h3>
		<ol>
            <li>IULIFest Call Officer : Ms. Pikat Arafah (+62 821 2534 2775) / Ms. Ainun (+62 823 5466 2017).</li>
			<li>Information for non Domestic transfer swift code: BNINIDJA follow by BNI Virtual Account Number (BNINIDJA<?=$sub_invoice_data[0]->sub_invoice_details_data[0]->sub_invoice_details_va_number;?>).</li>
			<li>BNI Call Officer (BCO) and Interactive Voice Response (IVR) : 1500046 (Open 24/7).</li>
			<?php
			if ($invoice_data->invoice_number == 'INV-F2206190009') {
			?>
			<li>Tax will be paid by pertamina and iuli will be given proof of tax deduction</li>
			<?php
			}
			?>
		</ol>
		
		<br><br><br><br>
		<p>Best regards,</p><br>
		<p>IULIFest</p>
	</body>
</html>