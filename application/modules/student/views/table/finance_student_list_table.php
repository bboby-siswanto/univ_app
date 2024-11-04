<style>
    .table-responsive {
        min-height: 600px !important;
    }
</style>
<div class="card">
    <div class="card-header">
        Student List
        <div class="card-header-actions">
            <button class="btn btn-link card-header-action" data-toggle="dropdown" id="settings_dropdown" aria-expanded="true">
                <i class="fas fa-sliders-h"></i> Quick Actions
            </button>
            <div class="dropdown-menu" aria-labelledby="settings_dropdown">
                <button id="btn_dl_tf_report" class="dropdown-item" class="card-header-action btn btn-link" data-toggle="tooltip" title="Download Tuition Fee Report">
                    <i class="fas fa-file-excel"></i> Download Tuition Fee Report
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table id="student_list_table" class="table table-striped table-bordered">
				<thead class="thead-dark">
					<tr>
						<th>Name</th>
						<th>Student Number</th>
						<th>Batch</th>
						<th>Entry Year</th>
                        <th>Faculty</th>
                        <th>Student Type</th>
                        <th>Study Program</th>
                        <th>Status</th>
						<th>Student Email</th>
						<th>Personal Email</th>
						<th>Personal Cellular</th>
						<th>Personal Phone</th>
						<th>Parent Name</th>
						<th>Parent Phone</th>
						<th>Parent Email</th>
						<th>Action</th>
					</tr>
				</thead>
			</table>
	    </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="set_refferal_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Refferenced Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_refferal_input" onsubmit="return false">
					<input type="hidden" name="personal_data_id" id="personal_data_id_refferal">
					<div class="form-group">
						<label for="reffference_code_refferal">Refference Code</label>
						<input type="text" class="form-control" name="reffference_code_refferal" id="reffference_code_refferal">
					</div>
				</form>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="submit_refference_code_refferal">Submit</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>
<div id="modal_filter_krs" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Academic Year</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_filter_krs" onsubmit="return false;">
                    <input type="hidden" id="krs_personal_data_id" name="krs_personal_data_id">
                    <div class="form-group">
                        <label>Academic Year</label>
                        <select name="academic_year_id" id="krs_academic_year_id" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                    if ($mbo_academic_year) {
                        foreach ($mbo_academic_year as $year) {
                    ?>
                            <option value="<?=$year->academic_year_id?>"><?=$year->academic_year_id?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Semester Type</label>
                        <select name="semester_type_id" id="krs_semester_type_id" class="form-control">
                            <option value="">Please select...</option>
                    <?php
                    if ($mbo_semester_type) {
                        foreach ($mbo_semester_type as $semester) {
                    ?>
                            <option value="<?=$semester->semester_type_id?>"><?=$semester->semester_type_name?></option>
                    <?php
                        }
                    }
                    ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="submit_show_krs" name="submit_show_krs" type="button" class="btn btn-info">Submit</button>
            </div>
        </div>
    </div>
</div>
<div id="modal_send_email" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Email to Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_send_mail" onsubmit="return false">
                    <input type="hidden" id="mail_student_id" name="student_id">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>To</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="mail_student" id="mail_student" class="form-control" readonly="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Subject</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="mail_subject" id="mail_subject" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="mail_message" id="mail_message"></textarea>
                        <input type="hidden" name="body_email" id="body_email">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="send_mail_student" type="button" class="btn btn-primary">Send</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    CKEDITOR.replace('mail_message');
    
    $('select#academic_year_id').val('<?=$academic_year_active;?>');
    // $('select[name="student_status[]"]').val('active');
    $('select[name="student_status[]"]').selectpicker('val', ['active']);
    $('.selectpicker').selectpicker('refresh');

    var dt_search = '';
    if (sessionStorage.length > 0) {
        // console.log(sessionStorage);
        dt_search = (sessionStorage.getItem("datatable_search") != null) ? sessionStorage.getItem("datatable_search") : '';

        if (sessionStorage.getItem("filter_program_id") != null) {
            $('#filter_program_id').val(sessionStorage.getItem("filter_program_id"));
        }

        if (sessionStorage.getItem("filter_study_program_id") != null) {
            $('#filter_study_program_id').val(sessionStorage.getItem("filter_study_program_id"));
        }

        if (sessionStorage.getItem("filter_batch") != null) {
            $('#academic_year_id').val(sessionStorage.getItem("filter_batch"));
        }

        if (sessionStorage.getItem("filter_status") != null) {
            // $('#filter_student_status2').val(sessionStorage.getItem("filter_status"));
            var data = sessionStorage.getItem("filter_status").split(",");
            $('#filter_student_status2').selectpicker('val', data);
        }

    }

    var student_table_list = $('table#student_list_table').DataTable({
        search: {
            "search": dt_search
        },
        processing: true,
        ajax:{
            url: '<?=base_url()?>student/filter_student_finance',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#student_filter_form').serialize();
                return a_form_data;
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Download Excel',
                extend: 'excel',
                title: 'Student List Data',
                exportOptions: {columns: ':visible'}
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
            'colvis'
        ],
        columns: [
            {data: 'personal_data_name'},
            {data: 'student_number'},
            {data: 'academic_year_id'},
            {data: 'finance_year_id'},
            {data: 'faculty_abbreviation'},
            {
                data: 'student_type',
                render: function(data, type, row) {
                    return (data === null) ? '' : data.toUpperCase();
                }
            },
            {data: 'study_program_name'},
            {
                data: 'student_status',
                render: function(data, type, row) {
                    return (data === null) ? '' : data.toUpperCase();
                }
            },
            {data: 'student_email'},
            {
                data: 'personal_data_email',
                visible: false
            },
            {data: 'personal_data_cellular'},
            {
                data: 'personal_data_phone',
                visible: false
            },
            {
                data: 'personal_data_phone',
                visible: false
            },
            {
                data: 'personal_data_phone',
                visible: false
            },
            {
                data: 'personal_data_phone',
                visible: false
            },
            {
                data: 'student_id',
                orderable: false,
                render: function(data, type, row) {
                    let btn_message = '<button type="button" id="btn_display_modal" class="btn btn-info btn-sm" title="Send Mail"><i class="fas fa-envelope"></i></button>';
                    let btn_student_score = '<a href="<?=site_url('academic/score/student_score/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" title="Student score"><i class="fas fa-book-open"></i></a>';
                    let btn_finance_setting = '<a href="<?=site_url('finance/invoice/student_setting/')?>' + row['student_id'] + '" class="btn btn-info btn-sm" target="blank" title="Student Finance Settings"><i class="fas fa-cogs"></i></a>';
                    let btn_show_krs = '<button id="show_krs_approval" type="button" class="btn btn-sm btn-info" title="Show KRS"><i class="fas fa-clipboard-check"></i></button>';
                    let btn_invoice = '<a href="<?=site_url('finance/invoice/lists/')?>' + row['personal_data_id'] + '" class="btn btn-info btn-sm" title="Invoice Data"><i class="fas fa-file-invoice-dollar"></i></a>';
                    let btn_history = '<a href="<?=base_url()?>devs/get_historycal_payment/' + row['student_id'] + '" target="_blank" class="btn btn-sm btn-info" title="Download Historical Payment"><i class="fas fa-file"></i></a>';
                    let btn_student_record = '<a href="<?=base_url()?>student/notes/' + row['student_id'] + '" class="btn btn-sm btn-info" title="Student Notes"><i class="fas fa-quote-right"></i></a>';

                    html = '<div class="btn-group btn-group-sm" role="group" aria-label="Actions Button">';
                    html += btn_message;
                    html += btn_student_score;
        <?php
        if (in_array($this->session->userdata('user'), ['47013ff8-89df-11ef-8f45-0068eb6957a0'])) {
        ?>
                    html += btn_finance_setting;
        <?php
        }
        ?>
                    html += btn_student_record;
                    html += btn_history;
                    html += btn_invoice;
                    html += '</div>';
                    return html;
                }
            },
        ]
    });

    $('button#btn_dl_tf_report').on('click', function(e) {
        e.preventDefault();

        $.blockUI();
        var a_form_data = $('form#student_filter_form').serialize();
        $.post('<?=base_url()?>finance/invoice/download_invoice_report', a_form_data, function(result) {
            $.unblockUI();

            if (result.code == 0) {
                window.location.href = '<?=base_url()?>download/download_invoice_report/' + result.semester + '/' + result.filename;
            }else{
                toastr.warning(result.message, 'Warning!');
            }

        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        });
    });

    $('button#submit_show_krs').on('click', function(e) {
        e.preventDefault();
        if (($('select#krs_academic_year_id').val() != '') && ($('select#krs_semester_type_id').val() != '')) {
            if ($('input#krs_personal_data_id').val() != '') {
                $('div#modal_filter_krs').modal('hide');
                let url = "<?=base_url()?>krs/krs_approval/" + $('select#krs_academic_year_id').val() + "/" + $('select#krs_semester_type_id').val() + "/" + $('input#krs_personal_data_id').val();
                window.open(url, '_blank');
            }else{
                toastr.error('Error retrieve student data!', 'Error!');
            }
        }else{
            toastr.warning('Please select filter field!', 'Warning!');
        }
    });

    $('table#student_list_table tbody').on('click', 'button[id="show_krs_approval"]', function(e) {
        e.preventDefault();

        var table_data = student_table_list.row($(this).parents('tr')).data();
        $('input#krs_personal_data_id').val(table_data.personal_data_id);
        $('div#modal_filter_krs').modal('show');
    });

	$('button#filter_student').on('click', function(e){
		e.preventDefault();
        student_table_list.ajax.reload();

        sessionStorage.setItem('filter_program_id', $('#filter_program_id').val());
        sessionStorage.setItem('filter_study_program_id', $('#filter_study_program_id').val());
        sessionStorage.setItem('filter_batch', $('#academic_year_id').val());
        sessionStorage.setItem('filter_status', $('#filter_student_status2').val());
	});
	
	
	$('table#student_list_table tbody').on('click', 'button[id="btn_display_modal"]', function(e) {
        e.preventDefault();

        var table_data = student_table_list.row($(this).parents('tr')).data();
        $('#mail_student_id').val(table_data.student_id);
        $('#mail_student').val(table_data.student_email);
        $('div#modal_send_email').modal('show');
    });

    $(function() {
        $('.dataTables_filter input').on('input', function(r) {
            var value = $('.dataTables_filter input').val();
            sessionStorage.setItem('datatable_search', value);
        });

        // $("#student_list_table [type='search']").focus();
        // $(".dataTables_filter input").focus();
    });

    $(document).keydown(function(event) {
        // console.log(event);
        var charinput = String.fromCharCode(event.which);
        // console.log(charinput);
        if ((event.ctrlKey) && (event.altKey) && (charinput == 'F')){
            $(".dataTables_filter input").focus();
        }
        return true;
        event.preventDefault();
    });
</script>