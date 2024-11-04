<link rel="stylesheet" href="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.css">
<script src="<?=base_url()?>assets/vendors/bootstrap/datepicker/bootstrap-datepicker.min.js"></script>
<style>
    .dropdown-menu.show {
        z-index: 99999 !important;
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
                        <input type="hidden" name="exchange_type" id="exchange_type" value="out">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter_program_id">Program</label>
                                    <select class="form-control" name="program_id" id="filter_program_id">
                                        <option value="all" selected="selected">All</option>
                                        <option value="7">Exchange Student</option>
                                        <option value="8">International Join Degree</option>
                                        <option value="6">Double Degree</option>
                                        <option value="4">Master Degree (NFU 3+2)</option>
                                    </select>
                                </div>  
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="filter_study_program_id" id="label_filter_study_program">
                                        Study Program
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="studyprogram">Study Year</label>
                                    <select class="form-control" id="academic_study_year" name="academic_study_year">
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="studyprogram">Student Batch</label>
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
                <div class="card-header-actions">
                    <button class="card-header-action btn btn-link" id="btn_new_student_abroad">
                        <i class="fa fa-plus"></i> Student
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table_list" class="table table-bordered table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Full Name</th>
                                <th>Student ID</th>
                                <th>Faculty</th>
                                <th>Study Program</th>
                                <th>Program</th>
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
                                <th>Study Location</th>
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
                                <th>Student Batch</th>
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
<div class="modal" tabindex="-1" role="dialog" id="modal_abroad_student">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Abroad Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>admission/international_office/submit_abroad_form" id="form_abroad" onsubmit="return false">
                    <input type="hidden" name="bf_exchange_id" id="bf_exchange_id">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bf_student_id" class="required_text">Student</label>
                                        <select name="bf_student_id" id="bf_student_id" class="form-control">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bf_program_id" class="required_text">Program</label>
                                        <select name="bf_program_id" id="bf_program_id" class="form-control">
                                            <option value=""></option>
                                            <option value="7">Exchange Student</option>
                                            <option value="8">International Join Degree</option>
                                            <option value="6">Double Degree</option>
                                            <option value="4">Master Degree (NFU 3+2)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bf_student_number">Student ID</label>
                                        <input type="text" name="bf_student_number" id="bf_student_number" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bf_study_program_name">Study Program</label>
                                        <input type="text" name="bf_study_program_name" id="bf_study_program_name" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bf_faculty_name">Faculty</label>
                                        <input type="text" name="bf_faculty_name" id="bf_faculty_name" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bf_institution_id" class="required_text">Study Location</label>
                                        <select name="bf_institution_id" id="bf_institution_id" class="form-control">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bf_institution_country">Institution Country</label>
                                        <input type="text" name="bf_institution_country" id="bf_institution_country" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="bf_academic_year" class="required_text">Academic Year</label>
                                        <select name="bf_academic_year" id="bf_academic_year" class="form-control">
                                            <option value=""></option>
                                    <?php
                                    if ($batch) {
                                        foreach ($batch as $o_academic_year) {
                                    ?>
                                            <option value="<?=$o_academic_year->academic_year_id;?>"><?=$o_academic_year->academic_year_id;?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bf_passpor_no">Passport No.</label>
                                        <input type="text" name="bf_passpor_no" id="bf_passpor_no" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group form-card-expired">
                                        <label for="bf_passpor_valid">Passport valid</label>
                                        <div class="input-group mb-3 input-daterange">
                                            <input type="text" class="form-control" name="bf_passpor_valid_from" id="bf_passpor_valid_from">
                                            <div class="input-group-append">
                                                <span class="input-group-text">to</span>
                                            </div>
                                            <input type="text" class="form-control" name="bf_passpor_valid_to" id="bf_passpor_valid_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_student_international">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_new_institution">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Univesity Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-institution">
                <form url="<?=base_url()?>institution/submit_international_university" id="form_create_university" onsubmit="return false">
                    <div class="form-group">
                        <label for="univ_institution_name" class="required_text">Name of University</label>
                        <input type="text" class="form-control" name="univ_institution_name" id="univ_institution_name">
                        <input type="hidden" name="univ_institution_id" id="univ_institution_id">
                    </div>
                    <div class="form-group">
                        <label for="univ_institution_email">University Email</label>
                        <input type="text" class="form-control" name="univ_institution_email" id="univ_institution_email">
                    </div>
                    <div class="form-group">
                        <label for="univ_institution_phone">University Phone</label>
                        <input type="text" class="form-control" name="univ_institution_phone" id="univ_institution_phone">
                    </div>
                    <div class="form-group">
                        <label for="univ_institution_country" class="required_text">Country of University</label>
                        <select name="univ_institution_country" id="univ_institution_country" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_university">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var student_list = JSON.parse('<?= (isset($student_list)) ? json_encode($student_list) : json_encode('[]'); ?>');
$(function() {
    $('#filter_student').on('click', function(e) {
        e.preventDefault();

        table_list.ajax.reload();        
    });

    $('.input-daterange').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate: new Date((2020), 0, 1),
        container:'.form-card-expired'
    });

    $('#bf_program_id, #bf_academic_year').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
    });

    $('#bf_institution_id').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        language: {
            "noResults": function(){
                return '<p>No Results Found</p><button class="btn btn-block btn-success" type="button" id="button-new-institution" data-toggle="modal" data-target="#modal_new_institution"><i class="fas fa-plus"></i> Add New University</button>';
            }
        },
        ajax: {
            url: '<?=site_url('institution/get_institution_partner')?>',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    term: params.term
                }
                return query;
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (item) {
                        return {
                            text: item.institution_name,
                            id: item.institution_id,
                            country: item.country_name
                        }
                    })
                };
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
    
    $('#bf_student_id').select2({
        data: student_list,
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },
        templateSelection: function(data) {
            return data.text;
        }
    });

    $('#modal_new_institution').on('show.bs.modal', function () {
        $("#bf_institution_id").select2("close");
        $('#modal_abroad_student').modal('hide');
        $('input#univ_institution_name').focus();
        $('#form_create_university').find('input, select').val('').trigger('change');
    });

    // $('#button-new-institution').on('click', function(e) {
    //     e.preventDefault();

    //     $('#modal_new_institution').modal('show');
    // });

    $('#btn_new_student_abroad').on('click', function(e) {
        e.preventDefault();

        $('#form_abroad').find('input, select').val('').trigger('change');
        $('#modal_abroad_student').modal('show');
    })

    $('table#table_list tbody').on('click', 'button#btn_edit_exchange_data', function(e) {
        e.preventDefault();
        
        $('#form_abroad').find('input, select').val('').trigger('change');
        var tabledata = table_list.row($(this).parents('tr')).data();

        var newOption = new Option(tabledata.institution_name, tabledata.institution_id, false, false);
        $('#bf_institution_id').append(newOption).trigger('change');
        
        $('#bf_student_id').val(tabledata.student_id).trigger('change');
        $('#bf_program_id').val(tabledata.exchange_program_id).trigger('change');
        $('#bf_institution_id').val(tabledata.institution_id).trigger('change');
        $('#bf_institution_country').val(tabledata.institution_country_name);
        $('#bf_academic_year').val(tabledata.exchange_academic_year).trigger('change');
        $('#bf_passpor_no').val(tabledata.passport_number);
        $('#bf_passpor_valid_from').val(tabledata.personal_data_id_card_valid_from);
        $('#bf_passpor_valid_to').val(tabledata.personal_data_id_card_valid);

        $('#modal_abroad_student').modal('show');
    });

    $('#bf_student_id').on('change.select2', function(e) {
        e.preventDefault();

        var data = $('#bf_student_id').select2('data')[0];
        $('#bf_study_program_name').val(data.study_program_name);
        $('#bf_student_number').val(data.student_number);
        $('#bf_faculty_name').val(data.faculty_name);
    });

    $('#bf_institution_id').on('change.select2', function(e) {
        e.preventDefault();

        var data = $('#bf_institution_id').select2('data')[0];
        $('#bf_institution_country').val(data.country);
    });

    $('#submit_university').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form = $('#form_create_university');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                // $('#modal_new_institution').modal('hide');
                // $('#modal_abroad_student').modal('show');
                window.location.reload();
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your request!');
        })
    })

    $('#submit_student_international').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form = $('#form_abroad');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_abroad_student').modal('hide');
                toastr.success('Success!');
                table_list.ajax.reload();
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(e) {
            $.unblockUI();
            toastr.error('Error processing your data!');
        });
    });

    $('#univ_institution_country').select2({
        minimumInputLength: 2,
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        ajax: {
            url: '<?=site_url('institution/get_country_by_name')?>',
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    term: params.term
                }
                return query;
            },
            processResults: function (res) {
                return {
                    // results: res.data
                    results: $.map(res.data, function (item) {
                        return {
                            text: item.country_name,
                            id: item.country_id
                        }
                    })
                };
            }
        }
    });

    $('input#univ_institution_name').autocomplete({
        max:10,
		minLength: 2,
		appendTo: '.modal-institution',
		source: function(request, response){
			var url = '<?=site_url('institution/get_institutions')?>';
			var data = {
				term: request.term
			};
            
			$.post(url, data, function(rtn){
				if(rtn.code == 0){
					var arr = [];
					arr = $.map(rtn.data, function(m){
						return {
							id: m.institution_id,
							value: m.institution_name,
                            univ_mail: m.institution_email,
                            univ_phone: m.institution_phone_number,
                            countryid: m.country_id,
                            countryname: m.country_name
						}
					});
					// response(arr);
					response(arr.slice(0, 10));
				}
			}, 'json');
		},
		select: function(event, ui){
			var id = ui.item.id;
            var newOption = new Option(ui.item.countryname, ui.item.countryid, false, false);

            $('#univ_institution_country').append(newOption).trigger('change');
			$('input#univ_institution_id').val(id);
            $('input#univ_institution_phone').val(ui.item.univ_phone);
            $('input#univ_institution_email').val(ui.item.univ_mail);
            $('select#univ_institution_country').val(ui.item.countryid).trigger('change');
		},
		change: function(event, ui){
			if(ui.item === null){
				$('input#univ_institution_id').val('');
				$('input#univ_institution_phone').val('');
				$('input#univ_institution_email').val('');
				$('select#univ_institution_country').val('').trigger('change');
			}
		}
	});
});

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
            data: 'student_number',
            responsivePriority: 2,
        },
        {
            data: 'faculty_name',
            // responsivePriority: 1,
        },
        {
            data: 'study_program_name',
            // responsivePriority: 1,
        },
        {
            data: 'program_exchange',
            // responsivePriority: 1,
        },
        {
            data: 'personal_data_email',
            // responsivePriority: 2,
        },
        {
            data: 'personal_data_place_of_birth',
            responsivePriority: 3,
        },
        {
            data: 'personal_data_dob',
            responsivePriority: 4,
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
            // responsivePriority: 4,
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
            // responsivePriority: 7,
        },
        {
            data: 'semester_type_name',
            // visible: false
        },
        {
            data: 'exchange_id',
            responsivePriority: 7,
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
                    btn_download += '<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="height: 250px; overflow-y: auto;">';
                    btn_download += list_file;
                    btn_download += '</div></div>';

                var btn_edit = '<button type="button" class="btn btn-warning btn-sm" id="btn_edit_exchange_data" title="Edit Exchange Data"><i class="fas fa-edit"></i></button>';
                var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                if (row.student_status == 'active') {
                    html += btn_edit;
                }
                html += btn_download;
                // html += '<a href="<?=base_url()?>admission/international_office/abroad_form/' + row.student_id + '" class="btn btn-warning btn-sm" id="btn_edit_exchange_data" title="Edit Exchange Data"><i class="fas fa-edit"></i></a>';
                html += '</div>';

                return html;
            }
            // visible: false
        },
    ]
});
</script>