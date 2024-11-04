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
        <hr>
		<h2>Invoice Number: <?=$invoice_data->invoice_number?></h2>
		<p>Student Name: <strong><?=$invoice_data->personal_data_name?></strong></p>
		<p><strong>Fee Details:</strong>
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

				if ((!is_null($details->fee_alt_description)) AND (!empty($details->fee_alt_description))) {
					$s_details .= '<br><small>('.$details->fee_alt_description.')</small>';
				}
			?>
			<li><?=$s_details?></li>
			<?php
			}	
			?>
		</ul>
        </p>
		<?php
		foreach($sub_invoice_data as $sub_invoice){
			if($sub_invoice->sub_invoice_details_data){
		?>
		Payment Type: <?=ucfirst($sub_invoice->sub_invoice_type)?><br>
		Amount: <?="Rp. ".number_format($sub_invoice->sub_invoice_amount, 0, ',', '.').",-"?><br>
		Paid: <?="Rp. ".number_format($sub_invoice->sub_invoice_amount_paid, 0, ',', '.').",-"?><br>
        <br>
		
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
		Beneficiary Name: <?=$invoice_data->personal_data_name?><br>
		Bank: Bank Negara Indonesia 46 (BNI 46) <br>
		<strong>Notes:</strong>
		<ol>
            <li>For late payment after the <?=($invoice_data->personal_data_id == '7e761824-d5b7-4609-a526-6f76cabff99f' ? '1' : '10')?><sup>th</sup> of each month will be fined IDR 500,000</li>
            <li>BNI will reject payment which is not at the exact amount and account as stated above</li>
			<li>If students making a payment without using a virtual account, then we assume no payment has been made.</li>
			<li>Please ignore this email if you have already paid.</li>
		</ol>
		<p>Best regards,</p>
		<!-- <p><strong>IULI Finance Dept.</strong></p> -->
		<p><br><br></p>
        <table style="border: none;" width="100%">
            <tr>
                <td style="border: none;" >
                    <nav style="width: 200px; display: table;">
                        <span style="display: table-cell; border-bottom: 3px solid black;"></span>
                    </nav>
                    <span><strong>IULI ANF Department</strong></span><br>
                </td>
                <td style="border: none;" >
                    <nav style="width: 200px; display: table;">
                        <span style="display: table-cell; border-bottom: 3px solid black;"></span>
                    </nav>
                    <span><strong>IULI Vice-Rector</strong></span><br>
                </td>
            </tr>
        </table>
	</body>
</html>