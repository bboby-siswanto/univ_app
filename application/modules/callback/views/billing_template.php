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
		<p>A Greetings from IULI.</p>
		<p>Kindly remind you for your next payment:</p>
		<h2>Invoice Number: <?=$invoice_data->invoice_number?></h2>
		
		<p>Student Name: <strong><?=$invoice_data->personal_data_name?></strong></p>
		<p><strong>Fee Details:</strong></p>
		<ul>
			<?php
			foreach($invoice_details as $details){
				$s_operator = '';
				switch($details->invoice_details_amount_sign_type)
				{
					case "negative":
						$s_operator = "-";
						break;
				}
				
				$s_details = '';
				$s_amount = '';
				switch($details->invoice_details_amount_number_type)
				{
					case "number":
						$s_amount .= $s_operator." Rp. ".number_format($details->invoice_details_amount, 0, ",", ".").",-";
						break;
					
					case "percentage":
						$s_amount .= $s_operator." ".$details->invoice_details_amount."%";
						break;
				}
				$s_details .= $details->fee_description." - ($s_amount)";
			?>
			<li><?=$s_details?></li>
			<?php
			}	
			?>
		</ul>
		
		<?php
		foreach($sub_invoice_data as $sub_invoice){
			if((isset($sub_invoice->sub_invoice_details_data)) AND ($sub_invoice->sub_invoice_details_data)){
		?>
		<p>Payment Type: <?=ucfirst($sub_invoice->sub_invoice_type)?></p>
		<p>Amount: <?="Rp. ".number_format($sub_invoice->sub_invoice_amount, 0, ',', '.').",-"?></p>
		<p>Paid: <?="Rp. ".number_format($sub_invoice->sub_invoice_amount_paid, 0, ',', '.').",-"?></p>
		
		<table border="1">
			<thead>
				<tr>
					<th>No</th>
					<th>Description</th>
					<th>Account Number</th>
					<th>Due Date</th>
					<th>Amount</th>
					<th>Total</th>
					<th>Amount Paid</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 1;
				foreach($sub_invoice->sub_invoice_details_data as $sub_invoice_details){
				?>
				<tr>
					<td><?=$i?></td>
					<td><?=$sub_invoice_details->sub_invoice_details_description?></td>
					<td><?=implode(' ', str_split($sub_invoice_details->sub_invoice_details_va_number, 4))?></td>
					<td><?=date('j F Y', strtotime($sub_invoice_details->sub_invoice_details_deadline))?></td>
					<td><?="Rp. ".number_format($sub_invoice_details->sub_invoice_details_amount, 0, ',', '.').",-"?></td>
					<td><?="Rp. ".number_format($sub_invoice_details->sub_invoice_details_amount_total, 0, ',', '.').",-"?></td>
					<td><?="Rp. ".number_format($sub_invoice_details->sub_invoice_details_amount_paid, 0, ',', '.').",-"?></td>
					<td><?=($sub_invoice_details->sub_invoice_details_status == 'paid') ? 'paid' : '-'?></td>
				</tr>
				<?php
				$i++;
				}
				?>
			</tbody>
		</table>
		<?php
			}
		}
		?>
		
		<p>Please transfer payment to:</p>
		<p>Beneficiary Name: <?=$invoice_data->personal_data_name?></p>
		<p>Bank: Bank Negara Indonesia 46 (BNI 46)</p>
		<p>Note: BNI will reject payment which is not at the exact amount and account as stated above</p>
		
		<h3>Notes:</h3>
		<ol>
			<li>If students making a payment without using a virtual account, then we assume no payment has been made and will be subject to fines in accordance with the applicable provisions.</li>
			<li>Please ignore this email if you have already paid.</li>
		</ol>
		
		<br><br><br><br>
		<p>Best regards,</p>
		<p><strong>IULI Finance Dept.</strong></p>
	</body>
</html>