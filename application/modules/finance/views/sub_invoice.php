<?php
if ((isset($o_student_data)) AND ($o_student_data)) {
	$s_finance_year = (!empty($o_student_data->finance_year_id)) ? $o_student_data->finance_year_id : '';
?>
<div class="card">
	<div class="card-header"><?=$o_student_data->personal_data_name.' ('.$o_student_data->study_program_abbreviation.'/'.$s_finance_year.')';?></div>
</div>
<?php
}
?>

<div class="card">
    <div class="card-header">
        Invoice Details
		<div class="card-header-actions">
			<a class="card-header-action" href="<?=base_url()?>devs/cheat_billing/<?=$invoice_id?>" target="_blank">
				<i class="fas fa-retweet"></i> Force Activated Billing
			</a>
		</div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
			<table id="invoice_details" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Fee Description</th>
						<th>Amount</th>
						<th>Fee	Amount Type (Additional/Non-Additional)</th>
<?php
if (isset($has_payment) AND (!$has_payment)) {
?>
						<th>Actions</th>
<?php
}
?>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Payment Methods
    </div>
    <div class="card-body">
	    <div class="table-responsive">
			<table id="sub_invoice" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Billed Amount</th>
						<th>Fine Amount</th>
						<th>Total Amount</th>
						<th>Paid Amount</th>
						<th>Payment Method</th>
						<th>Payment Status</th>
						<th>Paid Off Date</th>
						<th>Actions</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<script>
	let invoice_id = '<?=$invoice_id?>';
	var i_key = 101;

	var invoice_details_table = $('table#invoice_details').DataTable({
		ordering: false,
		paging: false,
		searching: false,
		info: false,
<?php
if (isset($has_payment) AND (!$has_payment)) {
?>
		dom: 'Bfrltip',
		buttons: [
			{
				text: '<i class="fas fa-plus"></i> Add fee / discount',
				className: 'btn btn-info',
				action: function(e, dt, node, config) {
					// var hash_id = $.md5(new Date());
					invoice_details_table.row.add( {
						"fee_option":'<?=$option_fee;?>',"invoice_id":"<?=$invoice_id;?>","fee_id":"","invoice_details_amount":"","invoice_details_amount_number_type":"","invoice_details_amount_sign_type":"","date_added":"","timestamp":"","payment_type_code":"","program_id":"","scholarship_id":null,"study_program_id":"","academic_year_id":"","semester_id":"","fee_amount":"","fee_amount_type":"","fee_amount_number_type":"","fee_amount_sign_type":"","fee_nationality":null,"fee_description":"","portal_id":null,"key":i_key,"fee_alt_description":null
				 	} ).draw( false );
					$('button#edit_fee_' + i_key).click();
					i_key++;
				}
			}
		],
<?php
}
?>
		ajax: {
			url: '<?=site_url('finance/invoice/get_invoice_details')?>',
			data: {
				invoice_id: invoice_id
			},
			method: 'POST'
		},
		columns: [
			{ 
				data: 'fee_description',
				render: function(data, type, row) {
					var select = '<select id="select_fee_' + row.key + '" class="form-control d-none select2fee">' + row.fee_option + '</select>';
					var text_ = '<span id="fee_key_' + row.key + '" fee_id="' + row['fee_id'] + '">' + data + '</span>';
					if (row['fee_alt_description'] !== null) {
						text_ += '<br><small>(' + row['fee_alt_description'] + ')</small>';
					}
					return text_ + select;
					// return data + '<input type="hidden" data-alt="fee_id" value="' + row['fee_id'] + '">';
				}
			},
			{ data: 'invoice_details_amount' },
			{ data: 'fee_amount_type' },
<?php
if (isset($has_payment) AND (!$has_payment)) {
?>
		{
			data: 'fee_id',
			render: function(data, type, row) {
				var html = '<div class="btn-group btn-group-sm" role="group" aria-label="">';
				if (row.fee_amount_type != 'main') {
					html += '<button type="button" name="remove_additional_fee" id="remove_additional_fee_' + row.key + '" class="btn btn-danger"><i class="fas fa-trash"></i></button>';
				}
				html += '<button type="button" name="edit_fee" id="edit_fee_' + row.key + '" class="btn btn-warning"><i class="fas fa-edit"></i></button>';
				html += '<button name="cancel_fee_edit" id="cancel_fee_edit_' + row.key + '" type="button" class="btn btn-danger btn-sm d-none" title="Cancel" ><i class="fas fa-times"></i></button>';
				html += '<button name="save_fee_edit" id="save_fee_edit_' + row.key + '" type="button" class="btn btn-success btn-sm d-none" title="Save" ><i class="fas fa-check"></i></button>';
				html += '</div>';
				return html;
			}
		},
<?php
}
?>
		],
		columnDefs: [
			{
				targets: 1,
				render: function(data, type, row){
					var operator = (row['fee_amount_sign_type'] == 'negative') ? '-' : '';
					if (row['fee_amount_number_type'] == 'percentage') {
						return operator + ' ' + data + '%';
					}else{
						return operator + ' ' + formatter.format(data);
					}
				}
			}
		]
	});
	
	function load_sub_invoice(invoice_id){
		var sub_invoice_table = $('table#sub_invoice').DataTable({
			paging: false,
			searching: false,
			info: false,
			order: [[ 4, "asc" ]],
			ajax: {
				url: '<?=site_url('finance/invoice/get_payment_method_list')?>',
				data: {
					invoice_id: invoice_id
				},
				method: 'POST'
			},
			columns: [
				{ data: 'sub_invoice_amount' },
				{ data: 'invoice_amount_fined' },
				{ data: 'sub_invoice_amount_total' },
				{ data: 'sub_invoice_amount_paid' },
				{ data: 'sub_invoice_type' },
				{ data: 'sub_invoice_status' },
				{
					data: 'sub_invoice_datetime_paid_off',
					defaultContent: 'N/A'
				}
			],
			columnDefs: [
				{
					targets: [0, 1, 2, 3],
					render: function(data, type, row){
						return formatter.format(data);
					}
				},
				{
					targets: [4, 5],
					render: function(data, type, row){
						return data.toUpperCase();
					}
				},
				{
					targets: 7,
					render: function(data, type, row){
						var html = '<div class="btn-group">';
						html += '<a class="btn btn-sm btn-info" target="_blank" href="<?=site_url('finance/invoice/sub_invoice_details/')?>' + row['sub_invoice_id'] + '" title="View Details"><i class="fa fa-eye"></i></a>';
						if ((row['sub_invoice_type'] == 'installment') && (row['sub_invoice_details_data'] == false)) {
							html += '<a class="btn btn-sm btn-info" target="_blank" href="<?=site_url('finance/invoice/initial_installment_form/')?>' + row['sub_invoice_id'] + '" title="Create Installment"><i class="fa fa-object-ungroup"></i></a>';
						}
						html += '</div>';
						return html;
					}
				}
			]
		});
	}
	
	$(function() {
		load_sub_invoice(invoice_id);
		$('table#invoice_details tbody').on('click', 'button[name="edit_fee"]', function(e) {
            e.preventDefault();
            var row_data = invoice_details_table.row($(this).parents('tr')).data();
            $('#fee_key_' + row_data.key).addClass('d-none');
            $('select#select_fee_' + row_data.key).removeClass('d-none');
            
            $('button#remove_additional_fee_' + row_data.key).addClass('d-none');
            $('button#edit_fee_' + row_data.key).addClass('d-none');
            $('button#cancel_fee_edit_' + row_data.key).removeClass('d-none');
            $('button#save_fee_edit_' + row_data.key).removeClass('d-none');
        });

		$('table#invoice_details tbody').on('click', 'button[name="cancel_fee_edit"]', function(e) {
            e.preventDefault();
            var row_data = invoice_details_table.row($(this).parents('tr')).data();
            $('#fee_key_' + row_data.key).removeClass('d-none');
            $('select#select_fee_' + row_data.key).addClass('d-none');
            
            $('button#remove_additional_fee_' + row_data.key).removeClass('d-none');
            $('button#edit_fee_' + row_data.key).removeClass('d-none');
            $('button#cancel_fee_edit_' + row_data.key).addClass('d-none');
            $('button#save_fee_edit_' + row_data.key).addClass('d-none');
        });
		
		$('table#invoice_details tbody').on('click', 'button[name="save_fee_edit"]', function(e) {
            e.preventDefault();
			if (confirm('Are you sure to submit invoice details ?')) {
				$.blockUI();
				var row_data = invoice_details_table.row($(this).parents('tr')).data();
				var a_data = {
					invoice_id: row_data.invoice_id,
					old_fee_id: row_data.fee_id,
					new_fee_id: $('select#select_fee_' + row_data.key).val()
				}

				$.post('<?=base_url()?>finance/invoice/update_invoice_details', a_data, function(result) {
					$.unblockUI();
					if (result.code == 0) {
						window.location.reload();
					}
					else{
						toastr.warning(result.message, 'Warning');
					}
				}, 'json').fail(function(params) {
					$.unblockUI();
					toastr.error('Error processing data!', 'Error');
				});
			}
        });

		$('table#invoice_details tbody').on('click', 'button[name="remove_additional_fee"]', function(e) {
            e.preventDefault();
			if (confirm('Are you sure to delete these details?')) {
				$.blockUI();
				var row_data = invoice_details_table.row($(this).parents('tr')).data();
				var a_data = {
					invoice_id: row_data.invoice_id,
					fee_id: row_data.fee_id
				}

				$.post('<?=base_url()?>finance/invoice/delete_invoice_details', a_data, function(result) {
					$.unblockUI();
					if (result.code == 0) {
						window.location.reload();
					}
					else{
						toastr.warning(result.message, 'Warning');
					}
				}, 'json').fail(function(params) {
					$.unblockUI();
					toastr.error('Error processing data!', 'Error');
				});
			}
        });
	});
</script>