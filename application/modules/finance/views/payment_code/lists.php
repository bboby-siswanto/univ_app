<div class="modal" tabindex="-1" role="dialog" id="payment_code_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Payment Type</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form class="form" id="payment_code_form" action="<?=site_url('finance/reference/save_payment_code')?>">
					<input type="hidden" name="payment_type_code" id="payment_type_code">
					<div class="form-group">
						<label>Payment Type Name</label>
						<input class="form-control" id="payment_type_name" name="payment_type_name">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-md btn-info float-right" data-dismiss="modal" aria-label="Close">Close</button>
				<button class="btn btn-md btn-info float-right" id="btn_save_payment_type">Create/Save changes</button>
			</div>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-header">
        Payment Type List
        <div class="card-header-actions">
			<a class="card-header-action" href="#" data-toggle="modal" data-target="#payment_code_modal" aria-expanded="true">
				<i class="fa fa-plus"></i> Payment Type
			</a>
		</div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
			<table id="payment_type_table" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Payment Type Code</th>
						<th>Payment Type Name</th>
						<th>Actions</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<script>
	var payment_type_table = $('table#payment_type_table').DataTable({
		ajax: {
			url: '<?=site_url('finance/reference/get_payment_code')?>',
			method: 'GET'
		},
		columns: [
			{ data: 'payment_type_code' },
			{ data: 'payment_type_name' }
		],
		columnDefs: [
			{
				targets: 2,
				render: function(data, type, row){
					let html = '<div class="btn-group">'+
					'<button id="edit_item" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></button>'+
					'</div>';
					return html;
				}
			}
		]
	});
	
	$('button#btn_save_payment_type').on('click', function(e){
		e.preventDefault();
		let form = $('form#payment_code_form');
		$.post(form.attr('action'), form.serialize(), function(rtn){
			if(rtn.code == 0){
				payment_type_table.ajax.reload();
			}
		}, 'json');
	});
</script>