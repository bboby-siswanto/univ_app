<div class="card">
    <div class="card-header">
        Institution List
        <div class="card-header-actions">
			<a class="card-header-action" href="#" data-toggle="modal" data-target="#save_or_edit_institution_modal" aria-expanded="true">
				<i class="fa fa-plus"></i> Institution
			</a>
		</div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table id="institution_list_table" class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Type</th>
						<th>Address</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<script>
	institution_list_table = $('table#institution_list_table').DataTable({
		resetPaging: false,
		serverSide: true,
		processing: true,
		ajax: {
			url: '<?=site_url('institution/load_all_institutions')?>',
			type: 'POST'
		},
		columns: [
			{ 
				data: 'institution_name',
				render: function(data, type, row){
					return '<a href="<?=site_url('institution/details/')?>'+row['institution_id']+'">'+data.toUpperCase()+'</a>';
				},
				defaultContent: 'N/A'
			},
			{
				data: 'institution_email',
				defaultContent: 'N/A'
			},
			{
				data: 'institution_phone_number',
				defaultContent: 'N/A'
			},
			{
				data: 'institution_type',
				defaultContent: 'N/A',
				render: function(data, type, row){
					return (data !== null) ? data.toUpperCase() : 'N/A';
				}
			},
			{
				data: 'address_id'
			},
			{
				data: 'institution_id'
			}
		]
/*
		columns: [
			{ data: 'institution_name' },
			{ data: 'institution_email' },
			{ data: 'institution_phone_number' },
			{ data: 'institution_type' },
			{ data: 'address_id' },
			{ data: 'institution_id' }
		],
		columnDefs: [
			{
				targets: [0],
				render: function(data, type, row){
					return '<a href="<?=site_url('institution/details/')?>'+row['institution_id']+'">'+data+'</a>';
				}
			},
			{
				targets: [1, 4],
				defaultContent: 'N/A'
			},
			{
				targets: [0, 3],
				render: function(data, type, row){
					return data.toUpperCase();
				}
			},
			{
				targets: 4,
				render: function(data, type, row){
					return row['address_street']+', '+row['address_province']+'<br> '+row['address_city']+'<br> '+row['address_zipcode'];
				}
			},
			{
				targets: 5,
				render: function(data, type, row){
					var html = '<div class="btn-group" role="group" aria-label="">';
					html += '<?=$btn_html?>'
					html += '</div>';
					return html;
				}
			}
		],
		order: [ 0, 'asc' ]
*/
	});
</script>
<?=$modal_html?>