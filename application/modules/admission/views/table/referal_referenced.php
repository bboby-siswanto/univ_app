<?php
if ($referral_agent) {
?>
<div class="card">
    <div class="card-header">
        <strong class="text-primary"><?=$referral_data->personal_data_name;?> (<?= $referral_data->personal_data_reference_code;?>)</strong>
    </div>
</div>
<div class="card">
    <div class="card-header">Reference Lists</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="reference_lists" class="table table-bordered table-striped">
                <thead>
                    <tr class="bg-dark">
                        <th>No</th>
                        <th>Name</th>
						<th>Email</th>
						<th>Student Number</th>
						<th>Batch</th>
						<th>Student Email</th>
						<th>Date of Birth</th>
						<th>Place of Birth</th>
						<th>Phone</th>
						<th>School</th>
						<th>Study Program</th>
						<th>Candidate Status</th>
						<th>Register Date</th>
						<th>Parent Name</th>
						<th>Parent Phone</th>
						<th>Parent Email</th>
						<th>Parent Occupation</th>
						<th>City</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        var filter_data = {
            referrer_id: '<?=$referral_data->personal_data_id;?>'
        }

        var reference_lists_table = $('table#reference_lists').DataTable({
            responsive: true,
			ajax: {
				url: '<?=site_url('admission/referral/get_reference_list')?>',
				type: 'POST',
				data: filter_data
			},
            columns: [
                {data: 'referrer_id', responsivePriority: 1},
                {
                    data: 'personal_data_name',
                    responsivePriority: 2,
                    render: function(data, type, row) {
                        return '<a href="<?=base_url()?>personal_data/profile/' + row['personal_data_id'] + '">' + data + '</a>';
                    }
                },
                {data: 'personal_data_email', responsivePriority: 3},
                {data: 'student_number'},
                {data: 'academic_year_id'},
                {data: 'student_email'},
                {data: 'personal_data_date_of_birth'},
                {data: 'personal_data_place_of_birth'},
                {data: 'personal_data_cellular'},
                {data: 'referrer_id'},
                {data: 'study_program_name'},
                {data: 'student_status', responsivePriority: 6},
                {data: 'register_date'},
                {data: 'referrer_id'},
                {data: 'referrer_id'},
                {data: 'referrer_id'},
                {data: 'referrer_id'},
                {data: 'referrer_id'}
                
            ]
        });
    });
</script>
<?php
}else {
?>
<div class="card">
    <div class="card-header">
        <h3>This person is not a referral agent!</h3>
    </div>
</div>
<?php
}
?>