<?php
// $a_student_number_late = ['11201607008','11201602002','11201602031','11201711007','11201710005','11201710002','11201702012','11201705006','11201803007','11201802006','11201908003','11201907004','11201907013','11201901010','11202001004','11201906003','11201912009','11201902005','11202009003','11202008012','11202007005','11202007010','11202007013','11202007020','11202007021','11202001003','11202101010','11202102012','11202106006','11202208009','11202201006','11202201008','11202201009','11202202001','11202202009','11202202011','11202202013','11202205001','11202205002','11202204002','11202204004','11202202007'];
$a_student_number_late = [];
$a_student_graduate_late = ['11201502022','11201607006','11201704006','11201608008','11201607017','11201701014'];
?>
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

				if ((!is_null($details->fee_alt_description)) AND (!empty($details->fee_alt_description))) {
					$s_details .= '<br><small>('.$details->fee_alt_description.')</small>';
				}
			?>
			<li><?=$s_details?></li>
			<?php
			}	
			?>
		</ul>
		<!-- <p>Please select one of the available payment type (if any)</p> -->
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
					<!-- <th>Status</th> -->
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
					<!-- <td><?=($sub_invoice_details->sub_invoice_details_status == 'paid') ? 'paid' : '-'?></td> -->
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

		<h3>Notes:</h3>
		<ol>
	<?php
	if ((isset($student_data)) AND (!is_null($student_data->student_number))) {
		if (in_array($student_data->student_number, $a_student_number_late)) {
			if (($invoice_data->academic_year_id == '2022') AND ($invoice_data->semester_type_id == '1')) {
				print("<li>For payments, it must be paid off immediately until the 5th installment with a due date of June 1 2023</li>");
			}
	?>
			<li>
				<strong>If by June 10 2023 students do not make payments, then the portal and wifi facilities will be disabled, and later they will not be able to take part in the final exam which will be held on June 26 2023.</strong>
			</li>
			<li>
				<strong>For students who are working on a thesis, if all administration is not completed, they are not allowed to take part in the thesis defense in August 2023</strong>
			</li>
	<?php
		}
		else if (in_array($student_data->student_number, $a_student_graduate_late)) {
	?>
			<li>
				<strong>If by June 10 2023 students do not make payments, then for all administrative matters not served</strong>
			</li>
	<?php
		}
	}
	?>
			<li>Students who still have payment pending,  they will not be able to access student portal (see attachment), and will not be able to join Final exam.</li>
			<li>For students who are working on their thesis, if all the administration has not been completed, access to student portal is blocked and they cannot submit thesis work</li>
			<li>Fail to pay Tuition Fee on time, will be fined with penalty Rp. 500,000 flat per month.</li>
			<li>BNI will reject payment which is not at the exact amount and account as stated above</li>
			<li>If payment for the <strong>Full Payment Method</strong> past due date, the system will automatically close the virtual account and automatically apply the installment payment method.</li>
			<li>If students making a payment without using a virtual account, then we assume no payment has been made.</li>
			<li>Please ignore this email if you have already paid.</li>
		</ol>

		<br><br><br><br>
		<p>Best regards,</p>
		<p><strong>IULI Finance Dept.</strong></p>
	</body>
</html>