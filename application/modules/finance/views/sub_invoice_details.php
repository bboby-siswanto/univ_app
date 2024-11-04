<?php
if ((isset($o_student_data)) AND ($o_student_data)) {
?>
<div class="card">
	<div class="card-header"><?=$o_student_data->personal_data_name.' ('.$o_student_data->study_program_abbreviation.'/'.$o_student_data->finance_year_id.')';?></div>
</div>
<?php
}
?>


<div class="card">
    <div class="card-header">
        Sub Invoice Details
    </div>
    <div class="card-body">
	    <div class="table-responsive">
			<table id="sub_invoice_details" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Virtual Account Nr.</th>
						<th>Billed Amount</th>
						<th>Fine Amount</th>
						<th>Total Amount</th>
						<th>Paid Amount</th>
						<th>Payment Status</th>
						<th>Payment Deadline</th>
						<th>Paid Off Date</th>
						<th>VA Status</th>
						<th>VA Expired Date</th>
						<th>Actions</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="edit_sub_invoice_details">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Sub Invoice Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="form" id="edit_sub_invoice_details_form" action="<?=site_url('finance/invoice/update_sub_invoice_details')?>">
					<input type="hidden" id="sub_invoice_details_id" name="sub_invoice_details_id">
					<input type="hidden" id="sub_invoice_id" name="sub_invoice_id">
					<input type="hidden" id="invoice_id" name="invoice_id">
					<input type="hidden" id="trx_id" name="trx_id">
					<input type="hidden" id="num_installment" name="num_installment">
					<div class="form-group">
						<label>Payment Deadline</label>
						<input type="text" id="payment_deadline" name="payment_deadline" class="form-control">
					</div>
					<div class="form-group">
						<label>Billed Amount</label>
						<input type="text" id="billed_amount" name="billed_amount" class="form-control calculate">
					</div>
					<div class="form-group">
						<label>Fine Amount</label>
						<input type="text" id="fined_amount" name="fined_amount" class="form-control calculate">
					</div>
					<div class="form-group">
						<label>Description</label>
						<textarea id="description" name="description" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label>Edit Remarks</label>
						<textarea id="remarks" name="remarks" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label>Total Amount</label>
						<input type="text" id="total_amount" name="total_amount" class="form-control" readonly="true">
					</div>
					<button class="btn btn-info float-right" id="save_sub_invoice_details">Save <i class="fa fa-save"></i></button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	let sub_invoice_id = '<?=$invoice_data->sub_invoice_id?>';
	let date_options = {
		singleDatePicker: true,
		showDropDowns: true,
		minDate: new Date(),
		locale: {
			format: 'YYYY-MM-DD',
			firstDay: 1
		},
		autoclose: true,
		disableTouchKeyboard: true
	};

	$('#billed_amount').number( true, 0 );
	$('#fined_amount').number( true, 0 );
	$('#total_amount').number( true, 0 );
	
	$('.calculate').on('keyup', function(e){
		let billed_amount = parseFloat($('input#billed_amount').val());
		let fined_amount = parseFloat($('input#fined_amount').val());
		var total_amount = billed_amount + fined_amount;
		$('input#total_amount').val(total_amount);
	});

	sub_invoice_table = $('table#sub_invoice_details').DataTable({
		ajax: {
			url: '<?=site_url('finance/invoice/get_sub_invoice_details')?>',
			data: function(d){
				d.sub_invoice_id = sub_invoice_id;
			},
			method: 'POST'
		},
		columns: [
			{ data: 'sub_invoice_details_va_number' },
			{ data: 'sub_invoice_details_amount' },
			{ data: 'sub_invoice_details_amount_fined' },
			{ data: 'sub_invoice_details_amount_total' },
			{ data: 'sub_invoice_details_amount_paid' },
			{
				data: 'sub_invoice_details_status',
				render: function(data, type, row){
					return data.toUpperCase();
				}
			},
			{ data: 'sub_invoice_details_real_datetime_deadline' },
			{
				data: 'sub_invoice_details_datetime_paid_off',
				// render: function(data, type, row) {
				// 	return (data !== null) ? data : 'N/A';
				// }
				defaultContent: 'N/A'
			},
			{
				data: 'va_bni_status',
				render: function(data, type, row) {
					switch (data) {
						case '1':
							return '<span class="badge badge-success">Active</span>'
							break;

						case '2':
							return '<span class="badge badge-danger" id="activate_va_badge">Inactive</span>'
							break;

						case '99':
							return '<span class="badge badge-warning" id="validate_va_badge">Invalid Amount</span>'
							break;
					
						default:
							return '';
							break;
					}
					// return data.toUpperCase();
				}
			},
			{ data: 'sub_invoice_details_deadline' },
			{
				data: 'sub_invoice_details_id',
				render: function(data, type, row){
					var html = '<div class="btn-group">';
					if(row['sub_invoice_details_status'] != 'paid'){
						html += '<button class="btn btn-sm btn-info" id="edit_sub_invoice_details"><i class="fa fa-edit"></i></button>';
						if (row['sub_invoice_type'] == 'installment') {
							html += '<button class="btn btn-sm btn-danger" id="delete_sub_invoice_details"><i class="fa fa-trash"></i></button>';
						}
					}
					html += '</div>';
					return html;
				}
			}
		],
		dom: 'Bfrltip',
		buttons: [
			{
				text: 'Add New',
				action: function(e, dt, node, config){
					let payment_type_selected = '<?=$invoice_data->sub_invoice_type;?>';
					let total_amount = <?=$invoice_data->sub_invoice_amount?>;
					let num_installment = 1;
					var amount_total = 0;
					var total_paid = 0;
					var installment_unpaid = 1;

					if (payment_type_selected == 'full') {
						alert('Can not proceed the action, payment type is full payment!');
						return false;
					}
					
					if(dt.data().length >= 1){
						$.each(dt.data(), function(k, v){
							total_amount -= v.sub_invoice_details_amount;
							amount_total += parseFloat(v.sub_invoice_details_amount_total);
							total_paid += parseFloat(v.sub_invoice_details_amount_paid);
							num_installment++;
							installment_unpaid += (v.sub_invoice_details_status != 'paid') ? 1 : 0;
						});
						$('input#billed_amount').val(total_amount);
					}
					
					// if(total_amount <= 0){
					// 	alert('Can not proceed the action');
					// 	return false;
					// }
					if (total_paid >= amount_total) {
						alert('Can not proceed the action');
						return false;
					}
					console.log(amount_total);
					var amount_unpaid = amount_total - total_paid;
					var amount_billed = amount_unpaid / installment_unpaid;
					
					$('input#billed_amount').val(amount_billed);
					$('input#fined_amount').val(0);
					$('input#total_amount').val(amount_billed);
					$('input#num_installment').val(num_installment);
					$('#description').text('');
					$('#remarks').text('');
					
					$('input#sub_invoice_details_id').val('');
					$('input#sub_invoice_id').val('<?=$invoice_data->sub_invoice_id?>');
					$('input#invoice_id').val('<?=$invoice_data->invoice_id?>');
					$('input#trx_id').val('');
					var tfx = $('input#payment_deadline');
					
					tfx.daterangepicker(date_options);
					$('div#edit_sub_invoice_details').modal('toggle');
				}
			},
			{
				text: 'Download Excel',
				extend: 'excel',
				title: 'Rincian_Cicilan_<?= (((isset($o_invoice_data)) AND ($o_invoice_data)) ? str_replace("'", "", str_replace(' ', '_', $o_invoice_data->invoice_description)) : '') ?>_<?= (((isset($o_student_data)) AND ($o_student_data)) ? str_replace("'", "", str_replace(' ', '_', $o_student_data->personal_data_name)) : '') ?>',
				exportOptions: {
					format: {
						body: function(data, row, column, node) {
							if (column == 0) {
								var array_data = data.split('');
								var va_number = '';
								$.each(array_data, function(i, v) {
									temp = i +1;
									va_number += v;
									if ((temp > 0) && (temp % 4 == 0)) {
										va_number += ' ';
									}
								});
								return va_number;
							}else if (column == 8){
								return '';
							}else{
								return data;
							}
						}
					}
				}
			}
		],
		columnDefs: [
			{
				targets: [1, 2, 3, 4],
				render: function(data, type, row){
					return formatter.format(data);
				}
			}
		]
	});
	
	$('button#save_sub_invoice_details').on('click', function(e){
		e.preventDefault();
		let form = $('form#edit_sub_invoice_details_form');
		let data = form.serialize();
		$.blockUI({ baseZ: 2000 });
		
		$.post(form.attr('action'), data, function(rtn){
			$.unblockUI();
			if(rtn.code == 0){
				$('div#edit_sub_invoice_details').modal('hide');
				sub_invoice_table.ajax.reload(null, false);
			}else{
				toastr.warning(rtn.message);
			}
		}, 'json').fail(function(params) {
			$.unblockUI();
			toastr.error("Error processing data!");
		});
	});

	$('table#sub_invoice_details tbody').on('click', 'span#activate_va_badge', function(e) {
		var data = sub_invoice_table.row($(this).parents('tr')).data();
		var sdi = data.sub_invoice_details_id;
		$.blockUI();

		$.post('<?=base_url()?>finance/invoice/activate_virtual_account/' + sdi, function(result) {
			$.unblockUI();
			if (result.code == 1) {
				toastr.warning(result.message, 'Warning!');
			}else{
				toastr.success('Success', 'Success!');
				sub_invoice_table.ajax.reload(null, false);
			}
		}, 'json').fail(function(params) {
			$.unblockUI();
			toastr.error('Error processing data', 'Error!');
		});
	});
	
	$('table#sub_invoice_details tbody').on('click', 'button#edit_sub_invoice_details', function(e){
		e.preventDefault();
		var data = sub_invoice_table.row($(this).parents('tr')).data();
		var remarks_detail = data['sub_invoice_details_remarks'];
		remarks_detail = (remarks_detail == null) ? '-' : remarks_detail;

		$('input#sub_invoice_details_id').val(data['sub_invoice_details_id']);
		$('input#sub_invoice_id').val(data['sub_invoice_id']);
		$('input#invoice_id').val(data['invoice_id']);
		$('input#trx_id').val(data['trx_id']);
		$('input#payment_deadline').val(data['sub_invoice_details_deadline']);
		$('input#billed_amount').val(data['sub_invoice_details_amount']);
		$('input#fined_amount').val(data['sub_invoice_details_amount_fined']);
		$('input#total_amount').val(data['sub_invoice_details_amount_total']);
		$('textarea#description').val(data['sub_invoice_details_description']);
		$('textarea#remarks').val(data['sub_invoice_details_remarks']);
		
		var tfx = $('input#payment_deadline');
		var objDate = new Date(data['sub_invoice_details_deadline']);
		
		tfx.daterangepicker(date_options);
		tfx.data('daterangepicker').setStartDate(objDate);
		
		$('div#edit_sub_invoice_details').modal('show');
	});

	$('table#sub_invoice_details tbody').on('click', 'button#delete_sub_invoice_details', function(e){
		e.preventDefault();
		if (confirm('Are you sure you want to delete this virtual account?')) {
			$.blockUI();
			var data = sub_invoice_table.row($(this).parents('tr')).data();
			$.post('<?=base_url()?>finance/invoice/delete_sub_invoice_details', {sub_invoice_details_id: data.sub_invoice_details_id, invoice_id: data.invoice_id}, function(result) {
				$.unblockUI();
				if (result.code == 0) {
					sub_invoice_table.ajax.reload(null, false);
				}
				else {
					toastr.warning(result.message, 'Warning!');
				}
			}, 'json').fail(function(params) {
				$.unblockUI();
				toastr.error('Error processing data!');
			});
		}
	});
</script>