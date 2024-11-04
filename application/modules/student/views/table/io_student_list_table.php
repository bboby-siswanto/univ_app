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
                    <form method="post" id="student_io_filter_form" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filter_program_id">Program</label>
                                    <select class="form-control" name="program_id" id="filter_program_id">
                                        <option value="all" selected="selected">All</option>
                                        <?php
                                        foreach($ref_program as $value){
                                        ?>
                                        <option value="<?=$value->program_id?>"><?=$value->program_name?></option>
                                        <?php
                                        }  
                                        ?>
                                    </select>
                                </div>  
                            </div>
                            <div class="col-md-6">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="studyprogram">Batch</label>
                                    <select class="form-control" id="academic_year_id" name="academic_year_id">
                                        <option value="all">All</option>
                                        <?php
                                        foreach($batch as $value){
                                        ?>
                                        <option value="<?=$value->academic_year_id?>"><?=$value->academic_year_id?></option>
                                        <?php
                                        }  
                                        ?>
                                    </select>
                                </div>  
                            </div>
                            <div class="col-md-3">
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
                            <!-- <div class="col-md-6">
                                <div class="form-group" >
                                    <label>Academic Year</label>
                                    <div class="input-group">
                                        <select name="semester_academic_year_id" id="semester_academic_year_id" class="form-control">
                                            <option value=""></option>
                                        </select>
                                        <select name="semester_semester_type_id" id="semester_semester_type_id" class="form-control">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
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
                    <table id="table_list" class="table table-bordered table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Place of Birth</th>
                                <th>Date of Birth</th>
                                <th>Passport No</th>
                                <th>Passport Valid</th>
                                <th>Gender</th>
                                <th>Nationality</th>
                                <th>Mobile Phone</th>
                                <th>Marital Status</th>
                                <th>Residential Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Postal Code</th>
                                <th>Parents/Guardian Name</th>
                                <th>Relationship</th>
                                <th>Parent Email Address</th>
                                <th>Parent Mobile Phone</th>
                                <th>Name of Home University</th>
                                <th>Country of Home University</th>
                                <th>Faculty of Home University</th>
                                <th>Major of Home University</th>
                                <th>Curent Semester</th>
                                <th>GPA</th>
                                <th>Coordinator Name</th>
                                <th>Email Address of Coordinator</th>
                                <th>Mobile Phone of Coordinator</th>
                                <th>Program Selected of IULI</th>
                                <th>Major Selected of IULI</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>action</th>
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

        table_list.ajax.reload();
    })
})
    var table_list = $('#table_list').DataTable({
        processing: true,
        responsive: true,
        ajax: {
            url: '<?=base_url()?>admission/International_office/get_student_io',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#student_io_filter_form').serialize();
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
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                text: 'Download Pdf',
                extend: 'pdf',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Print',
                extend: 'print',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
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
            },
            {
                data: 'personal_data_email',
                responsivePriority: 2,
            },
            {
                data: 'personal_data_place_of_birth',
                // visible: false
            },
            {
                data: 'personal_data_dob',
                responsivePriority: 3,
            },
            {
                data: 'personal_data_id_card_number',
                // visible: false
            },
            {
                data: 'passpor_valid',
                // visible: false
            },
            {
                data: 'personal_data_gender',
                // visible: false,
                render: function(data, type, row) {
                    return (data == 'F') ? 'Female' : 'Male'
                }
            },
            {
                data: 'citizenship_country_name',
                // visible: false
            },
            {
                data: 'personal_data_cellular',
                responsivePriority: 4,
            },
            {
                data: 'personal_data_marital_status',
                // visible: false
            },
            {
                data: 'address_street',
                // visible: false
            },
            {
                data: 'address_city',
                // visible: false
            },
            {
                data: 'address_district',
                // visible: false
            },
            {
                data: 'address_country_name',
                // visible: false
            },
            {
                data: 'address_zipcode',
                // visible: false
            },
            {
                data: 'parent_name',
                // visible: false
            },
            {
                data: 'parent_relation',
                // visible: false
            },
            {
                data: 'parent_email',
                // visible: false
            },
            {
                data: 'parent_phone',
                // visible: false
            },
            {
                data: 'institution_name',
                responsivePriority: 5,
            },
            {
                data: 'institution_country_name',
                // visible: false
            },
            {
                data: 'exchange_faculty_name',
                // visible: false
            },
            {
                data: 'exchange_study_program_name',
                // visible: false
            },
            {
                data: 'semester_id',
                // visible: false
            },
            {
                data: 'last_gpa',
                // visible: false
            },
            {
                data: 'coordinator_name',
                // visible: false
            },
            {
                data: 'coordinator_email',
                // visible: false
            },
            {
                data: 'coordinator_phone',
                // visible: false
            },
            {
                data: 'program_name',
                responsivePriority: 6,
            },
            {
                data: 'study_program_name',
            },
            {
                data: 'student_batch',
                responsivePriority: 7,
            },
            {
                data: 'semester_type_name',
                // visible: false
            },
            {
                data: 'student_id',
                responsivePriority: 8,
                render: function(data, type, row) {
                    var list_file = '';
                    if (row.document_list.length > 0) {
                        var document_list_data = row.document_list;
                        $.each(document_list_data, function(i, v) {
                            var btn_files = '<a target="_blank" class="dropdown-item" href="<?=base_url()?>file_manager/download_files/' + v.document_valid + '">' + v.document_name + '</a>'
                            list_file += btn_files;
                        });
                    }
                    var btn_download = '<div class="btn-group" role="group">';
                    btn_download += '<button id="btnGroupDrop1" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-download"></i></button>';
                    btn_download += '<a href="https://pmb.iuli.ac.id/international_form/registration/retrieve_pmb/' + row.personal_data_id + '" class="btn btn-sm btn-warning" target="_blank" title="force retrieve data"><i class="fas fa-sync"></i></a>' 
                    btn_download += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">';
                    btn_download += '';
                    btn_download += list_file;
                    btn_download += '</div></div>';
                    var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">'
                    html += btn_download;
                    html += '</div>';
                    return html;
                }
                // visible: false
            },
        ]
    });
</script>