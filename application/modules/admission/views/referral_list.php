<div class="modal" tabindex="-1" role="dialog" id="form_referral">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">New SGS Data</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?=modules::run('admission/referral/form_referral')?>
			</div>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-header">
        Referral List
        <div class="card-header-actions">
			<a class="card-header-action" href="#" data-toggle="modal" data-target="#form_referral" aria-expanded="true">
				<i class="fa fa-plus"></i> Referral
			</a>
		</div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table id="table_referral_list" class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Name</th>
						<th>Ref #</th>
						<th>Phone</th>
						<th>Email</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<script>
	table_referral_list = $('table#table_referral_list').DataTable({
		ajax: {
			url: '<?=site_url('admission/referral/get_referral_lists')?>'
		},
		columns: [
			{ data: 'personal_data_name' },
			{ data: 'personal_data_reference_code' },
			{ data: 'personal_data_cellular' },
			{ data: 'personal_data_email' },
			{
				data: 'personal_data_id',
				render: function(data, type, row) {
					var html = '<div class="input-group">';
					html += '<a href="<?=base_url()?>admission/referral/reference_list/' + data + '" title="Referenced Lists" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
					html +='</div>';
					return html;
				}
			}
		]
	});
</script>