<style>
    .table-responsive {
        min-height: 600px !important;
    }
</style>
<div class="row">
	<div class="col-md-12">
        <div id="accordion">
            <div class="card">
                <div class="card-header" id="filter_title">
                    Student Filter
                    <div class="card-header-actions">
                        <button class="btn btn-link card-header-action" data-toggle="collapse" data-target="#card_body_student_filter" aria-expanded="true" aria-expanded="card_body_student_filter">
                            <i class="fas fa-caret-square-down"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body collapse show" id="card_body_student_filter" data-parent="#accordion">
                    <form method="post" id="student_karyawan_filter_form" method="POST">
                        <input type="hidden" name="program_id" value="2">
                        <input type="hidden" name="student_class_type" value="karyawan">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_study_program_id" id="label_filter_study_program">
                                        Study Program
                                        <div class="spinner-border-mini d-none" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </label>
                                    <select class="form-control" name="study_program_id" id="filter_study_program_id">
                                        <option value="all">All</option>
                                        <?php
                                        foreach($study_program as $value){
                                        ?>
                                        <option value="<?=$value->study_program_id?>" data-abbr="<?= $value->study_program_abbreviation; ?>"><?=$value->study_program_name?></option>
                                        <?php
                                        }  
                                        ?>
                                    </select>
                                </div>  
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="studyprogram">Batch</label>
                                    <select class="form-control" id="academic_year_id" name="academic_year_id">
                                        <option value="all">All</option>
                                        <?php
                                        foreach($batch as $value){
                                            $selected = ((isset($active_batch)) AND ($active_batch) AND ($active_batch[0]->academic_year_id == $value->academic_year_id)) ? 'selected="selected"' : '';
                                        ?>
                                        <option value="<?=$value->academic_year_id?>" <?=$selected;?>><?=$value->academic_year_id?></option>
                                        <?php
                                        }  
                                        ?>
                                    </select>
                                </div>  
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="studyprogram">Status</label>
                                    <select class="form-control" name="student_status" id="filter_student_status">
                                        <option value="all">All</option>
                                        <?php
                                        foreach ($status_lists as $status) {
                                        ?>
                                        <option value="<?= $status?>"><?= strtoupper($status);?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" id="filter_student" class="btn btn-primary float-right">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
	<div class="col-md-12">
		<div class="card">
            <div class="card-header">
                Student List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="student_list_table" class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Active Date</th>
                                <th>Student Number</th>
                                <th>Batch</th>
                                <th>Student Email</th>
                                <th>Date of Birth</th>
                                <th>Place of Birth</th>
                                <th>Phone</th>
                                <th>School</th>
                                <th>SGS Code</th>
                                <th>Referrer</th>
                                <th>Scholarship Registration</th>
                                <th>Institute</th>
                                <th>Study Program</th>
                                <th>Candidate Status</th>
                                <th>Register Date</th>
                                <th>Email Confirmed</th>
                                <th>Finish Online Test</th>
                                <th>Parent Name</th>
                                <th>Parent Phone</th>
                                <th>Parent Email</th>
                                <th>Parent Occupation</th>
                                <th>City</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>
<script>
$(function() {
    
    $('#filter_student').on('click', function(e) {
        e.preventDefault();

        student_list_table.ajax.reload();
    })
});
var student_list_table = $('#student_list_table').DataTable({
    processing: true,
    responsive: true,
    ajax: {
        url: '<?=base_url()?>student/filter_result',
        type: 'POST',
        data: function(params) {
            let a_form_data = $('form#student_karyawan_filter_form').serialize();
            // var a_filter_data = objectify_form(a_form_data);
            return a_form_data;
        }
    },
    dom: 'Bfrtip',
    buttons: [
        {
            text: 'Download Excel',
            extend: 'excel',
            title: 'Student List Data',
            // exportOptions: {
            //     columns: ':visible'
            // }
        },
        {
            text: 'Download Pdf',
            extend: 'pdf',
            title: 'Student List Data',
            // exportOptions: {columns: ':visible'}
        },
        {
            text: 'Print',
            extend: 'print',
            title: 'Student List Data',
            // exportOptions: {columns: ':visible'}
        },
        // {
        //     text: 'Column Visibility',
        //     action: function () {
        //         // show columns
        //     }
        // },
        'colvis'
    ],
    // 33
    columns: [
        { 
            data: 'personal_data_name',
            responsivePriority: 1,
            render: function(data, type, row){
                return '<a href="<?=site_url('personal_data/profile/')?>'+row['student_id']+'/'+row['personal_data_id']+'">'+data+'</a>';
            }
        },
        {
            data: 'personal_data_email',
            responsivePriority: 2,
            render: function(data, type, row){
                return '<a href="<?=site_url('personal_data/profile/')?>'+row['student_id']+'/'+row['personal_data_id']+'">'+data+'</a>';
            }
        },
        { data: 'student_date_active', defaultContent: 'N/A' },
        { data: 'student_number', defaultContent: 'N/A' },
        { data: 'academic_year_id' },
        { data: 'student_email', defaultContent: 'N/A' },
        { data: 'personal_data_date_of_birth', defaultContent: 'N/A' },
        { data: 'personal_data_place_of_birth', defaultContent: 'N/A' },
        { data: 'personal_data_cellular', responsivePriority: 3 },
        {
            data: 'institution_name',
            defaultContent: 'N/A',
            responsivePriority: '<?= ($this->session->userdata('employee_id') == '24284490-2802-4094-9675-059de314a723') ? '5' : '20' ?>'
        },
        { data: 'personal_data_reference_code', defaultContent: 'N/A' },
        { data: 'referal_name', defaultContent: 'N/A' },
        { data: 'scholarship_name' },
        { data: 'type_of_admission_name'},
        { data: 'study_program_name', responsivePriority: 9 },
        {
            data: 'student_status',
            defaultContent: 'N/A',
            responsivePriority: 4,
            render: function(data, type, row){
                return (data === null) ? '' : data.toUpperCase();
            }
        },
        {
            data: 'register_date',
            responsivePriority: '<?= ($this->session->userdata('employee_id') == '24284490-2802-4094-9675-059de314a723') ? '20' : '5' ?>'
            // render: function(data, type, row){
            // 	return moment(data).format('YYYY-MM-DD');
            // }
        },
        {
            data: 'personal_data_email_confirmation',
            responsivePriority: 7
        },
        {
            data: 'has_finished_online_test',
            responsivePriority: 8
        },
        {
            data: 'family_name'
        },
        {
            data: 'family_contact'
        },
        {
            data: 'family_email'
        },
        {
            data: 'family_ocupation'
        },
        {
            data: 'address_city'
        },
        {
            data: 'student_id',
            responsivePriority: 8,
            render: function(data, type, row) {
                let btnMessage = '<button type="button" id="btn_display_modal" class="btn btn-info btn-sm" title="Send Mail"><i class="fas fa-envelope"></i></button>';
                let btnTuitionFee = '<button type="button" id="btn_initial_tuition_fee_modal" class="btn btn-info btn-sm" title="Initial Tuition Fee Invoice"><i class="fas fa-file"></i></button>';
                let btnInitStudent = '<button name="btn_initial_student_data" type="button" id="btn_initial_student_data" class="btn btn-info btn-sm" title="Set student status to active"><i class="fas fa-user-graduate"></i></button>';
                let btnInitSetting = '<a href="<?=site_url('admission/candidate_setting/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Candidate settings" target="_blank"><i class="fas fa-cogs"></i></a>';
                let btn_student_record = '<a href="<?=base_url()?>student/notes/' + row['student_id'] + '" class="btn btn-sm btn-info" title="Student Notes"><i class="fas fa-quote-right"></i></a>';
                let btn_input_refferal = '<button name="btn_set_refferal" type="button" id="btn_set_refferal" class="btn btn-info btn-sm" title="Set Refferal"><i class="fas fa-user-tag"></i></button>';
                let btn_invoice = '<a href="<?=site_url('finance/invoice/lists/')?>' + row['personal_data_id'] + '" class="btn btn-info btn-sm" title="Invoice Data" target="_blank"><i class="fas fa-file-invoice-dollar"></i></a>';
                
                var html = '<div class="btn-group" role="group" aria-label="">';
                if (row.student_status == 'candidate') {
                    html += btnTuitionFee;
                }
                html += btnInitStudent;
                html += btnInitSetting;
                html += btn_invoice;
                html += '</div>';
                return html;
            }
            // visible: false
        },
    ]
});
</script>
<div class="modal" tabindex="-1" role="dialog" id="initial_tuition_fee_invoice_modal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Initial Tuition Fee Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?=modules::run('finance/invoice/initial_tuition_fee')?>
			</div>
		</div>
	</div>
</div>