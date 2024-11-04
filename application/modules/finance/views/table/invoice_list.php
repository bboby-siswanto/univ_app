<div class="modal" tabindex="-1" role="dialog" id="new_invoice_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">New Invoice Form</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?=modules::run('finance/invoice/invoice_form')?>
			</div>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="new_open_invoice_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">New Open Invoice Form</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_bni_detail">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Modal title</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table class="table">
					<tr>
						<th>TRX ID</th>
						<td>: <span id="trx_id"></span></td>
					</tr>
					<tr>
						<th>Virtual Account</th>
						<td>: <span id="virtual_account"></span></td>
					</tr>
					<tr>
						<th>Billing Amount</th>
						<td>: <span id="trx_amount"></span></td>
					</tr>
					<tr>
						<th>Customer Name</th>
						<td>: <span id="customer_name"></span></td>
					</tr>
					<tr>
						<th>Datetime Created</th>
						<td>: <span id="datetime_created"></span></td>
					</tr>
					<tr>
						<th>Datetime Expired</th>
						<td>: <span id="datetime_expired"></span></td>
					</tr>
					<tr>
						<th>Datetime Last Update</th>
						<td>: <span id="datetime_last_updated"></span></td>
					</tr>
					<tr>
						<th>VA Status</th>
						<td>: <span id="va_status"></span></td>
					</tr>
					<tr>
						<th>Billing Type</th>
						<td>: <span id="billing_type"></span></td>
					</tr>
					<tr>
						<th>Description</th>
						<td>: <span id="description"></span></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php
if ((isset($o_student_data)) AND ($o_student_data)) {
	if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
		echo modules::run('student/show_name', $o_student_data->student_id);
	}
	else {
?>
<div class="card">
	<div class="card-header"><?=$o_student_data->personal_data_name.' ('.$o_student_data->study_program_abbreviation.'/'.$o_student_data->finance_year_id.')';?></div>
</div>
<?php
	}
}
else if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
?>
<!-- <div class="row">
	<div class="col-12">
		<div class="btn-group float-right mb-2" role="group" aria-label="Basic example">
			<button type="button" class="btn btn-primary" id="btn_new_student_invoice"><i class="fa fa-plus"></i> Student Invoice</button>
			<button type="button" class="btn btn-primary" id="btn_new_open_invoice"><i class="fa fa-plus"></i> Open Invoice</button>
		</div>
	</div>
</div> -->
<?php
}
?>

<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="outstanding-tab" data-toggle="tab" data-target="#outstanding" type="button" role="tab" aria-controls="outstanding" aria-selected="true">Outstanding</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="invoice-tab" data-toggle="tab" data-target="#invoice" type="button" role="tab" aria-controls="invoice" aria-selected="false">Invoice</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="payment-tab" data-toggle="tab" data-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false">Payment</button>
	</li>
</ul>
<!-- <div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="outstanding" role="tabpanel" aria-labelledby="outstanding-tab">...</div>
	<div class="tab-pane fade" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">...</div>
</div> -->

<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="outstanding" role="tabpanel" aria-labelledby="outstanding-tab">
<?php
if ((isset($billing_list)) AND ($billing_list)) {
    foreach ($billing_list as $o_billing) {
        if ($o_billing->payment_type_code != '05') {
            $billing_detail = $o_billing->billing_detail;
			$bni_data = $billing_detail['check_va'];
?>
        <div class="accordion" id="accordion<?=$o_billing->payment_type_code;?>">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?=$o_billing->payment_type_code;?>" aria-expanded="true" aria-controls="collapse<?=$o_billing->payment_type_code;?>">
                <?=$o_billing->payment_type_name;?>
                </button>
            </h2>
            <div id="collapse<?=$o_billing->payment_type_code;?>" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion<?=$o_billing->payment_type_code;?>">
                <table class="table table-sm mb-4">
                    <tr>
                        <td>Amount Billed</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_amount'], 0, ',', '.');?></td>
                    </tr>
                    <tr>
                        <td>Total Fined</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_fined'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Total Paid</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_paid'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Total Amount Billed</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['total_billed'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Minimum Payment</td>
                        <td>:</td>
                        <td><?=number_format($billing_detail['billing']['min_payment'], 0, ',','.');?></td>
                    </tr>
                    <tr>
                        <td>Beneficiary Name</td>
                        <td>:</td>
                        <td><?=$o_student_data->personal_data_name;?></td>
                    </tr>
                    <tr>
                        <td>Virtual Account</td>
                        <td>:</td>
                        <td>
                            <?=implode(' ', str_split($billing_detail['va_number'], 4));?>
                            <?php
							$btn_action = '';
                            if ($billing_detail['va_status'] == 1) {
                                echo '<span class="badge badge-success">active</span>';
                            }
                            else if ($billing_detail['va_status'] == 2) {
                                echo '<span class="badge badge-danger">inactive</span>';
								// echo '<span class="badge badge-pill badge-warning ml-2 activate_billing" title="Activated" data-va="'.$billing_detail['va_number'].'"><i class="fas fa-check-circle "></i></span>';
								$btn_action .= '<button class="btn btn-sm btn-warning activate_billing" type="button" title="Activated" data-va="'.$billing_detail['va_number'].'"><i class="fas fa-check-circle "></i> Activate</button>';
                            }
                            else if ($billing_detail['va_status'] == 3) {
                                echo '<span class="badge badge-danger">inactive</span>';
								$btn_action .= '<button type="button" class="btn btn-sm btn-warning update_billing" title="Update Billing" data-va="'.$billing_detail['va_number'].'"><i class="fas fa-check-circle "></i> Update</button>';
                            }
                            else if ($billing_detail['va_status'] == 4) {
                                echo '<span class="badge badge-danger">inactive</span>';
								$btn_action .= '<button type="button" class="btn btn-sm btn-warning update_billing" title="Update Billing" data-va="'.$billing_detail['va_number'].'"><i class="fas fa-check-circle "></i> Update</button>';
                            }
                            else {
                                echo '<span class="badge badge-danger">inactive</span>';
                            }
							$btn_action .= '<button type="button" class="btn btn-sm btn-info cek_billing" title="BNI Detail" data-va="'.$bni_data['virtual_account'].'" data-trx="'.$bni_data['trx_id'].'" data-amount="'.$bni_data['trx_amount'].'" data-customer="'.$bni_data['customer_name'].'" data-created="'.$bni_data['datetime_created'].'" data-expired="'.$bni_data['datetime_expired'].'" data-update="'.$bni_data['datetime_last_updated'].'" data-status="'.$bni_data['va_status'].'" data-type="'.$bni_data['billing_type'].'" data-description="'.$bni_data['description'].'"><i class="fas fa-info-circle"></i> BNI Detail</button>';
							$btn_action .= '<a class="btn btn-success btn-sm" href="'.base_url().'callback/api/generate_invoice_file/'.((isset($o_student_data)) ? $o_student_data->student_id : $this->session->userdata('student_id')).'/'.$o_billing->payment_type_code.'" target="blank"><i class=" fa fa-download "></i> Download</a>';
							if (in_array($o_billing->payment_type_code, ['02', '05'])) {
								$btn_action .= '<button type="button" class="btn btn-sm btn-info set_min_payment" title="Set Minimum Payment"><i class="fa fa-file-invoice-dollar"></i> Set Min Payment</button>';
							}
							$btn_action .= '<a class="btn btn-info btn-sm" href="'.base_url().'finance/invoice/get_reminder_teks/'.((isset($o_student_data)) ? $o_student_data->student_id : '').'/'.$o_billing->payment_type_code.'/true" target="blank"><i class=" fa fa-paper-plane "></i> Send Billing</a>';
                            ?>
							<!-- <span class="badge badge-info"><i class="fas fa-info-circle"></i> BNI Detail</span> -->
							<div class="btn-group btn-group-sm" role="group" aria-label="..."><?=$btn_action;?></div>
                        </td>
                    </tr>
                    <tr>
                        <td>Bank</td>
                        <td>:</td>
                        <td>Bank Negara Indonesia 46 (BNI 46)</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            Fee Details:
                            <ul>
                        <?php
                        if (count($billing_detail['fee_detail']) > 0) {
                            foreach ($billing_detail['fee_detail'] as $s_detail) {
                        ?>
                                <li><?=$s_detail;?></li>
                        <?php
                            }
                        }
                        ?>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
        }
    }
}
else {
?>
		<div class="accordion" id="accordion_none">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse_none" aria-expanded="true" aria-controls="collapse_none">
                	-
                </button>
            </h2>
            <div id="collapse_none" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion_none">
                <table class="table table-sm mb-4">
                    <tr>
                        <td>Amount Billed</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Fined</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Paid</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Total Amount Billed</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Minimum Payment</td>
                        <td>:</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Beneficiary Name</td>
                        <td>:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Virtual Account</td>
                        <td>:</td>
                        <td>
                            -
                        </td>
                    </tr>
                    <tr>
                        <td>Bank</td>
                        <td>:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            Fee Details:
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<?php
}
?>
	</div>
	<div class="card tab-pane fade" id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
		<div class="accordion" id="accordion_invoice">
			<div class="card-header bg-white">
				Filter Data
				<div class="card-header-actions">
					<button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#form_filter_invoice_collapse" aria-expanded="true" aria-expanded="form_filter_invoice_collapse">
						<i class="fas fa-caret-square-down"></i>
					</button>
				</div>
			</div>
			<div class="card-body border-bottom collapse show" id="form_filter_invoice_collapse" data-parent="#accordion_invoice">
				<div class="accordion-body">
					<form class="form">
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label>Payment Type</label>
									<select class="form-control" id="payment_type_id" name="payment_type_id">
										<option value="x">Please select...</option>
										<?=(isset($personal_data_id)) ? '<option value="all" selected="true">All</option>' : '';?>
										<?php
										foreach($payment_type as $payment){
										?>
										<option value="<?=$payment->payment_type_code?>"><?=$payment->payment_type_name?></option>
										<?php
										}  
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Invoice Type</label>
									<select name="student_invoice_type" id="student_invoice_type" class="form-control">
										<option value="internal">Student IULI</option>
										<option value="partner">Student Partner</option>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<div class="form-group">
									<label>Invoice Academic Semester</label>
									<div class="input-group">
										<select name="invoice_academic_year_id" id="invoice_academic_year_id" class="form-control">
											<option value="all">All</option>
									<?php
									if ($academic_year_lists) {
										foreach ($academic_year_lists as $o_year) {
									?>
											<option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?></option>
									<?php
										}
									}
									?>
										</select>
										<select name="invoice_semester_type_id" id="invoice_semester_type_id" class="form-control">
											<option value="all">All</option>
									<?php
									if ($semester_type_lists) {
										foreach ($semester_type_lists as $o_semester) {
									?>
											<option value="<?=$o_semester->semester_type_id;?>"><?=$o_semester->semester_type_name;?></option>
									<?php
										}
									}
									?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-12">
								<button id="submit_result_filter" class="btn btn-info float-right" type="button">Filter</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="card-header bg-white">
			Invoice List
	<?php
		// if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
	?>
			<div class="card-header-actions">
				<a class="card-header-action" href="#" data-toggle="modal" data-target="#new_invoice_modal" aria-expanded="true">
					<i class="fa fa-plus"></i> Invoice
				</a>
			</div>
	<?php
		// }
	?>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="invoice_table" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Invoice Number</th>
							<th>Name</th>
							<th>Paid Amount</th>
							<th>Description</th>
							<th>Allow fine</th>
							<th>Paid off date</th>
							<th>Invoice Status</th>
							<th>Date Created</th>
							<th>Notes</th>
							<th>Actions</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
		<div class="card-header bg-white">
			Payment History
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="payment_table" class="table table-striped table-bordered">
					<thead>
						<tr>
							<th>Virtual Account</th>
							<th>Payment Date</th>
							<th>Payment Amount</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- <div class="card">
    
</div> -->
<div class="modal" tabindex="-1" role="dialog" id="invoice_settings">
	<div class="modal-dialog" role="document">
		<div class="modal-content col-md-12">
			<div class="modal-header">
				<h5 class="modal-title">Invoice Settings</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="form" id="invoice_settings_form" onsubmit="return false">
					<input type="hidden" id="settings_invoice_id" name="invoice_id">
					<div class="form-group">
						<div class="col-md-12">
							<label>Invoice Name :</label>
								<input type="text" name="personal_data_name" id="personal_data_name" class="form-control" readonly="true" style="border-color:darkslategrey;">
						</div>	
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label>Invoice Number : </label>
								<input type="text" name="invoice_number" id="invoice_number" class="form-control" readonly="true" style="border-color:darkslategrey;">
						</div>	
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label>Invoice Allow Fine : </label>
							<div class="pull-right">
								<div class="custom-control custom-switch">
									<input type="checkbox" class="custom-control-input" id="invoice_allow_fine" name="invoice_allow_fine">
									<label class="custom-control-label" for="invoice_allow_fine"></label>
								</div>
							</div>
						</div>	
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label>Invoice Allow Reminder : </label>
							<div class="pull-right">
								<div class="custom-control custom-switch">
									<input type="checkbox" class="custom-control-input" id="invoice_allow_reminder" name="invoice_allow_reminder" >
									<label class="custom-control-label" for="invoice_allow_reminder"></label>
								</div>
							</div>
						</div>	
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label>Invoice Status :</label>
							<select name="invoice_status" id="invoice_status" class="form-control" style="border-color:darkslategrey;">
								<option value="">Please select...</option>
								<?php
								foreach($invoice_status as $status){
								?>
								<option value="<?=$status?>"><?=strtoupper($status)?></option>
								<?php
								}  
								?>
							</select>
						</div>	
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label for="invoice_note">Invoice Notes :</label>
							<textarea id="invoice_note" class="form-control" name="invoice_note" rows="5" cols="50" style="border-color:darkslategrey;"></textarea>
						</div>	
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_submit_invoice_settings">Save changes</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reminder_teks_template">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Send Reminder</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="reminder_teks_form" onsubmit="return false">
					<input type="hidden" id="reminder_invoice_id" name="invoice_id">
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<label>To</label>
							</div>
							<div class="col-md-9">
								<input type="text" name="student_email" id="reminder_student_email" class="form-control" readonly="true">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<label>CC</label>
							</div>
							<div class="col-md-9">
								<div class="input-group">
									<input type="text" class="form-control" name="student_cc_email" id="student_cc_email" readonly="true">
									<button type="button" class="btn bg-transparent" style="margin-left: -40px; z-index: 100;" id="btn_clear_cc_email">
										<i class="fa fa-times"></i>
									</button>
									<div class="input-group-append">
										<button class="btn btn-info" type="button" id="btn_add_cc_email"><i class="fas fa-plus"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<label>Subject</label>
							</div>
							<div class="col-md-9">
								<input type="text" name="subject_email" id="reminder_subject_email" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Message</label>
						<textarea name="reminder_message_email" id="reminder_message_email"></textarea>
						<input type="hidden" name="message_body" id="reminder_message_body">
					</div>
				</form>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary float-right" id="button_send_reminder">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
		</div>
	</div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_payment_invoice">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body-detail">
				<div class="row">
                    <div class="col-sm-6">
                        Beneficiary Name: <span id="payment_beneficiary_name"></span>
                    </div>
                    <div class="col-sm-6">
                        Payment Date: <span id="payment_payment_date"></span>
                    </div>
                </div>
                <div class="table-responsive">
                    <input type="hidden" id="payment_bni_id" name="payment_bni_id">
                    <table id="payment_table_details" class="table table-hover">
                        <thead>
                            <tr>
                                <th>VA. No</th>
                                <th>Invoice Number</th>
                                <th>Payment Description</th>
                                <th>Payment Amount</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_min_payment">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Set Minimum Payment</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="min_payment_input">Min Payment</label>
					<input type="text" id="min_payment_input" name="min_payment_input" class="form-control form-number" value="<?=$o_student_data->finance_min_payment;?>">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="submit_min_payment">Save changes</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	let payment_type = 'all';
	let s_personal_data_id = '<?= (isset($personal_data_id)) ? $personal_data_id : "" ?>';
    CKEDITOR.replace('reminder_message_email');

	invoice_table = $('table#invoice_table').DataTable({
		ajax: {
			url: '<?=site_url('finance/invoice/get_invoice_list')?>',
			data: function(d){
				d.payment_type = $('select#payment_type_id').val();
				if (s_personal_data_id != '') {
					d.personal_data_id = s_personal_data_id;
				}
				d.student_invoice_type = $('select#student_invoice_type').val();
				d.invoice_academic_year_id = $('select#invoice_academic_year_id').val();
				d.invoice_semester_type_id = $('select#invoice_semester_type_id').val();
			},
			method: 'POST'
		},
		columns: [
			{ data: 'invoice_number' },
			{ data: 'personal_data_name' },
			{
				data: 'invoice_amount_paid',
				render: function(data, type, row){
					return formatter.format(data);
				}
			},
			{
				data: 'invoice_description',
				render: function(data, type, row){
					return data + ' - ' + row['study_program_name'] + ' Finance Batch ' + row['academic_year_id']
				}
			},
			{ data: 'invoice_allow_fine' },
			{ data: 'invoice_datetime_paid_off' },
			{
				data: 'invoice_status',
				render: function(data, type, row) {
					return data.toUpperCase();
				}
			},
			{ data: 'date_added' },
			{ data: 'invoice_note' },
			{
				data: 'invoice_id',
				render: function(data, type, row){
					var html = '<div class="btn-group">';
					html += '<a class="btn btn-sm btn-info" href="<?=site_url('finance/invoice/sub_invoice/')?>' + data + '"><i class="fa fa-eye"></i></a>';
			<?php
			// if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
			?>
					// html += '<button type="button" class="btn btn-sm btn-danger" title="remove invoice"><i class="fa fa-trash"></i></button>';
					if (row.invoice_status != 'paid') {
						if (row.invoice_status != 'cancelled') {
							html += '<a href="<?=base_url()?>devs/force_repair_invoice/' + row.invoice_id + '" target="blank" class="btn btn-sm btn-warning" title="set virtual account"><i class="fa fa-wine-glass"></i></a>';
						}
					}
			<?php
			// }
			?>
					html += '<button type="button" class="btn btn-sm btn-info" id="btn_invoice_settings" title="Invoice Settings"><i class="fas fa-cogs"></i></button>';
					
					if (row.invoice_status != 'paid') {
						// html += '<button type="button" class="btn btn-sm btn-info" id="btn_send_reminder" title="Send Reminder"><i class="fas fa-envelope"></i></button>';
					}
					var partner_student = ($('#student_invoice_type').val() == 'partner') ? '/srh' : '';
					// html += '<a href="<?=base_url()?>download/pdf_download/generate_invoice_billing/' + data + partner_student + '" class="btn btn-sm btn-info" title="Download Invoice"><i class="fas fa-file-download"></i></a>';
					html += '<a href="<?=base_url()?>download/pdf_download/generate_single_invoice/' + row.student_id + '/' + data + '" class="btn btn-sm btn-info" target="blank" title="Download Invoice"><i class="fas fa-file-download"></i></a>';
					html += '</div>';
					return html;
				}
			}
		],
		order: [
			[0, 'desc']
		]
	});

<?php
if ((isset($o_student_data)) AND ($o_student_data)) {
?>
	let payment_table = $('#payment_table').DataTable({
		paging: false,
		searching: false,
		info: false,
		ordering: false,
		ajax: {
			url: '<?=site_url('student/finance/get_payment_student')?>',
			data: function(d){
				d.payment_type = 'all';
				d.personal_data_id = '<?=$o_student_data->personal_data_id?>';
			},
			method: 'POST'
		},
		columns: [
			{data: 'sub_invoice_details_va_number'},
			{data: 'datetime_payment'},
			{
				data: 'total_payment_amount',
				render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'
			},
			{
				data: 'payment_id',
				render: function(data, type, row) {
					var receipt = row.receipt_no;
					receipt = (receipt !== null) ? receipt.replaceAll('/', '-') : '';
					var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
					html += '<button type="button" class="btn btn-primary" id="view_payment_details">View Payment</button>';
					html += (receipt !== '') ? '<a href="<?=base_url()?>finance/invoice/get_receipt/' + row.bni_transactions_id + '/Receipt-' + receipt + '" class="btn btn-success" id="download_receipt" target="_blank">Receipt</a>' : '';
					html += '</div>';
					return html;
				}
			},
		]
	});

	let payment_table_details = $('#payment_table_details').DataTable({
		paging: false,
		searching: false,
		info: false,
		ordering: false,
		ajax: {
			url: '<?=site_url('student/finance/get_payment_detail')?>',
			data: function(d){
				d.bni_id = $('#payment_bni_id').val();
			},
			method: 'POST'
		},
		columns: [
			{data: 'sub_invoice_details_va_number'},
			{data: 'invoice_number'},
			{data: 'sub_invoice_details_description'},
			{
				data: 'payment_amount',
				render: $.fn.dataTable.render.number('.', ',', 0, ''), className: 'text-right'
			},
		]
	});
<?php
}
?>

	$.fn.modal.Constructor.prototype._enforceFocus = function () {}
	$('.form-number').number(true, 0);
	$('button#btn_add_cc_email').on('click', function(e) {
		e.preventDefault();

		Swal.fire({
			title: 'Add Email',
			input: 'email',
			inputAttributes: {
				autocapitalize: 'on'
			},
			showCancelButton: true,
			confirmButtonText: 'Add',
			showLoaderOnConfirm: true,
			allowOutsideClick: () => !Swal.isLoading()
		}).then((result) => {
			if ((result.value) && ((result.value != ''))) {
				console.log(result);
				var value_cc = $('#student_cc_email').val();
				var value_cc = (value_cc == '') ? result.value : value_cc + ';' + result.value;
				$('#student_cc_email').val(value_cc);
			}
		})
	});

	$('#btn_clear_cc_email').on('click', function(e) {
		e.preventDefault();

		$('#student_cc_email').val('');
	})

	$('button#submit_min_payment').on('click', function(e) {
		let data = {
			personal_data_id: '<?=$o_student_data->personal_data_id;?>',
			student_id: '<?=$o_student_data->student_id;?>',
			min_payment: $('#min_payment_input').val()
		};

		$.post('<?=base_url()?>finance/invoice/save_min_payment', data, function(result) {
			if (result.code == 0) {
				toastr.success('Success!');
				$('#modal_min_payment').modal('hide');
			}
			else {
				toastr.warning(result.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			e.preventDefault();
		});
	})

	$('button#btn_new_student_invoice').on('click', function(e) {
		e.preventDefault();

		$('#new_invoice_modal').modal('show');
	});

	$('button#btn_new_open_invoice').on('click', function(e) {
		e.preventDefault();

		$('#new_open_invoice_modal').modal('show');
	});

	$(".activate_billing").on('click', function(e) {
		e.preventDefault();
		var virtualaccount = $(this).attr('data-va');

		$.blockUI();
		let data = {
			virtual_account: virtualaccount
		}
		$.post('<?=base_url()?>finance/invoice/activate_billing/billing', data, function(result) {
			$.unblockUI();
			if (result.code == 0) {
				toastr.success('Success');
				window.location.reload();
			}
			else {
				toastr.warning(result.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			$.unblockUI();
			toastr.error('Error processing data!', 'Error');
		})
	});

	$('.set_min_payment').on('click', function(e) {
		e.preventDefault();

		var virtualaccount = $(this).attr('data-va');
		$('#modal_min_payment').modal('show');
	});

	$('.cek_billing').on('click', function(e) {
		e.preventDefault();

		$('span#trx_id').html($(this).attr('data-trx'));
		$('span#virtual_account').html($(this).attr('data-va'));
		$('span#trx_amount').html($(this).attr('data-amount'));
		$('span#customer_name').html($(this).attr('data-customer'));
		$('span#datetime_created').html($(this).attr('data-created'));
		$('span#datetime_expired').html($(this).attr('data-expired'));
		$('span#datetime_last_updated').html($(this).attr('data-update'));
		$('span#va_status').html($(this).attr('data-status'));
		$('span#description').html($(this).attr('data-description'));
		$('span#billing_type').html($(this).attr('data-type'));

		$('#modal_bni_detail').modal('show');
	});

	$('.update_billing').on('click', function(e) {
		e.preventDefault();
		var virtualaccount = $(this).attr('data-va');

		$.blockUI();
		let data = {
			virtual_account: virtualaccount
		}
		$.post('<?=base_url()?>finance/invoice/update_billing/billing', data, function(result) {
			$.unblockUI();
			if (result.code == 0) {
				toastr.success('Success');
				window.location.reload();
			}
			else {
				toastr.warning(result.message, 'Warning!');
			}
		}, 'json').fail(function(params) {
			$.unblockUI();
			toastr.error('Error processing data!', 'Error');
		})
	})

	$('#payment_table tbody').on('click', 'button#view_payment_details', function(e) {
        e.preventDefault();

        var table_data = payment_table.row($(this).parents('tr')).data();
        $('#payment_bni_id').val(table_data.bni_transactions_id);
        
        payment_table_details.ajax.reload();
		$('#payment_beneficiary_name').text(table_data.customer_name);
        $('#payment_payment_date').text(table_data.datetime_payment);
        $('#modal_payment_invoice').modal('show');
    })

	$('table#invoice_table tbody').on('click', 'button#btn_invoice_settings',function(e) {
		e.preventDefault();

		var o_invoice_data = invoice_table.row($(this).parents('tr')).data();
		$('input#settings_invoice_id').val(o_invoice_data.invoice_id);
		
		$('select#invoice_status').val(o_invoice_data.invoice_status)
		$('input#personal_data_name').val(o_invoice_data.personal_data_name)
		$('input#invoice_number').val(o_invoice_data.invoice_number)
		$('input#invoice_allow_fine').val(o_invoice_data.invoice_allow_fine)
		$('input#invoice_allow_reminder').val(o_invoice_data.invoice_allow_reminder)
		$('textarea#invoice_note').val(o_invoice_data.invoice_note)
	
		if(o_invoice_data.invoice_allow_fine == "yes"){
			$("#invoice_allow_fine").prop( "checked", true );
		} else {
			$("#invoice_allow_fine").prop( "checked", false );
		}

		if(o_invoice_data.invoice_allow_reminder == "yes"){
			$("#invoice_allow_reminder").prop( "checked", true );
		} else {
			$("#invoice_allow_reminder").prop( "checked", false );
		}
		
		$('#invoice_settings').modal('show');
	});

	$('button#btn_submit_invoice_settings').on('click', function(e) {
		e.preventDefault();

		var data = $('form#invoice_settings_form').serialize();
		if (confirm('Are you sure to make change with this invoice data?')) {
			$.blockUI({ baseZ: 2000 });

			$.post('<?=base_url()?>finance/invoice/save_invoice_setting', data, function(result) {
				$.unblockUI();

				if (result.code == 0) {
					toastr.success('Success!');
					$('#invoice_settings').modal('hide');
					invoice_table.ajax.reload(null, false);
				}else{
					toastr.warning(result.message, 'Warning!');
				}
			}, 'json').fail(function(params) {
				$.unblockUI();
				toastr.error('Error processing data!', 'Error System!');
				console.log(params.responseText);
			});
		}
	});

    $('button#button_send_reminder').on('click', function(e) {
        e.preventDefault();

        var email_message = CKEDITOR.instances.reminder_message_email.getData();
        $('input#reminder_message_body').val(email_message);
        var data = $('#reminder_teks_form').serialize();
		if ($('#student_invoice_type').val() == 'partner') {
			data += '&partner=srh';
		}

        if ($('#reminder_subject_email').val() == '') {
            toastr.warning('Subject email is empty!', 'Warning');
        }else if ($('#reminder_message_body').val() == '') {
            toastr.warning('Body message is empty!', 'Warning');
        }else{
            $.blockUI({ baseZ: 2000 });
            
            $.post('<?=base_url()?>finance/invoice/send_billing', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success(result.message, 'Success!');
                    $('div#reminder_teks_template').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
                
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error send billing!', 'Error!');
            });
        }
    });
	
	$('table#invoice_table tbody').on('click', 'button#btn_send_reminder', function(e) {
		e.preventDefault();
        $.blockUI();
		var o_invoice_data = invoice_table.row($(this).parents('tr')).data();

        $('input#reminder_student_email').val(o_invoice_data.student_email);
        $('input#reminder_invoice_id').val(o_invoice_data.invoice_id);

		var data = {
			invoice_id: o_invoice_data.invoice_id
		}

		if ($('#student_invoice_type').val() == 'partner') {
			var data = {
				invoice_id: o_invoice_data.invoice_id,
				partner: 'srh'
			}
		}

		$.post('<?=base_url()?>finance/invoice/get_invoice_reminder_teks', data, function(result) {
            $.unblockUI();
			if (result.code != 0) {
                toastr.warning(result.message, 'Warning!');
            }else{
                var oEditor =  CKEDITOR.instances.reminder_message_email;
                oEditor.setData(result.message);

                $('input#reminder_subject_email').val(result.payment_type);
				var text_ccmail = '';
				var list_cc = result.email_cc;
				if (result.email_cc) {
					$.each(list_cc, function(i, v) {
						if (v !== null) {
							text_ccmail = (text_ccmail == '') ? v : text_ccmail + ';' + v;
						}
					})
				}
				
                $('input#student_cc_email').val(text_ccmail);
            }
		}, 'json').fail(function(params) {
            $.unblockUI();
			toastr.error('Error retrieve invoice data!', 'Error!');
		});

        $('div#reminder_teks_template').modal('show');
	});
	
	// $('select#payment_type_id').on('change', function(e){
	// 	e.preventDefault();
	// 	payment_type = $(this).val();
	// 	invoice_table.ajax.reload();
	// });

	// $('select#student_invoice_type').on('change', function(e){
	// 	e.preventDefault();
	// 	invoice_table.ajax.reload();
	// });

	$('button#submit_result_filter').on('click', function(e) {
		e.preventDefault();

		invoice_table.ajax.reload();
	});
</script>