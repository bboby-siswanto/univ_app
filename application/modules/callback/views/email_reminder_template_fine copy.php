<!DOCTYPE html>
<html>
	<body>
		<p>A Greetings from IULI.</p>
		<p>Kindly remind you for your monthly instalment:</p>
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
		<p>Please select one of the available payment type (if any)</p>
		<?php
		foreach($sub_invoice_data as $sub_invoice){
			if($sub_invoice->sub_invoice_details_data){
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
					<th>Fine</th>
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
					<td><?="Rp. ".number_format($sub_invoice_details->sub_invoice_details_amount_fined, 0, ',', '.').",-"?></td>
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
			<li>If payment for the <strong>Full Payment Method</strong> past due date, the system will automatically close the virtual account and automatically apply the installment payment method.</li>
			<li>If payment for the <strong>Installment Method</strong> past due date, for each month late the system will automatically applied penalty worth <strong>Rp 500.000 on the next day</strong>.</li>
			<li>When the due date payment is past, penalty of Rp 500.000/month will be applied and added to the amount immediately.</li>
			<li>If students making a payment without using a virtual account, then we assume no payment has been made and will be subject to fines in accordance with the applicable provisions.</li>
			<li>Please ignore this email if you have already paid.</li>
		</ol>
		
		<h3>Illustration example for Installment Method:</h3>
		<h4>Amount Rp 4.200.000</h4>
		<p>Due date: 10 July 2018</p>
		<p>On 11 July 2018, will change to:</p>
		<p>Amount = Rp 4.200.000 + Rp 500.000 (penalty) = Rp 4.700.000</p>
		<p>On 11 August 2018, will change to:</p>
		<p>Amount = Rp 4.200.000 + Rp 1.000.000 (2 months penalty) = Rp 5.200.000</p>
		<p>And so on.</p>
		<br><br><br><br>
		<p>Best regards,</p>
		<p><strong>IULI Finance Dept.</strong></p>
	</body>
</html>