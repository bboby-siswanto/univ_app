<div class="card">
    <div class="card-header">List Data</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="recap_list">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Batch</th>
                        <!-- <th>Program</th> -->
                        <th>Faculty</th>
                        <th>Study Program</th>
                        <th>Status</th>
                        <th>Unpaid Tuition Fee (Semester Selected) (IDR)</th>
                        <th>Unpaid Total Tuition Fee (IDR)</th>
<?php
if (($this->session->userdata('auth')) AND ($user_update_allowed !== null) AND (in_array($this->session->userdata('user'), $user_update_allowed))) {
    print('<th>Action</th>');
}
?>
                    </tr>
                </thead>
                <tbody></tbody>
<?php
if (($this->session->userdata('auth')) AND ($user_update_allowed !== null) AND (in_array($this->session->userdata('user'), $user_update_allowed))) {
?>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right">Total</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
<?php
}
?>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_approval_invoice">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Approval of Payment Delays</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row pb-3">
                <div class="col-12">
                    <span><strong id="student_form_name"></strong></span>
                </div>
            </div>
            <form action="<?=base_url()?>finance/invoice/submit_approval_payment_delay" id="form_approval_invoice" onsubmit="return false">
                <input type="hidden" name="personal_data_id" id="approval_personal_data_id">
                <input type="hidden" name="student_id" id="approval_student_id">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="file_approval" class="required_text">Approval File</label>
                            <input type="file" class="form-control" name="approval_file" id="approval_file">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="deadline_date" class="required_text">Invoice Deadline</label>
                            <input type="date" class="form-control" name="deadline_date" id="deadline_date">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn_submit_approval">Save changes</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('.form-number').number(true, 0);
    var table_list = $('table#recap_list').DataTable({
        // paging: false,
        // searching: false,
        // info: false,
        // ordering: false,
        processing: true,
        ajax: {
            url: '<?=base_url()?>finance/invoice/get_invoice_recap',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#filter_recap').serialize();
                return a_form_data;
            }
        },
        dom: 'Bfrtip',
        buttons: [
            {
                text: 'Download Excel',
                extend: 'excelHtml5',
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
            'colvis',
            {
                text: 'Reload Data',
                action: function ( e, dt, button, config ) {
                    table_list.ajax.reload(null, false);
                },
            }
        ],
        columns: [
            {
                data: 'personal_data_name',
                // render: function(data, type, row) {
                //     if (row.student_approved_payment_delay == 1) {
                //         style="background-color: red;"
                //         return '<span style="background-color: green;">' + data + '</span>';
                //     }
                // }
            },
            { data: 'student_number' },
            { data: 'academic_year_id' },
            // { data: 'program_code' },
            { data: 'faculty_abbreviation' },
            { data: 'study_program_abbreviation' },
            { data: 'student_status' },
            { 
                data: 'amount_semester_pending',
                className: "text-right",
                render: function(data, type, row) {
                    data = $.number(data);
                    var html = data;
                    if (parseFloat(data) > 0) {
                        var fined_semester = row.amount_semester_fined;
                        fined_semester = $.number(fined_semester);
                        if (parseFloat(row.amount_semester_fined) > 0) {
                            html = '<span data-toggle="tooltip" data-placement="bottom" title="include fine IDR ' + fined_semester + '"><i class="fas fa-coins"></i> ' + data + '</span>';
                        }
                    }

                    return html;
                }
            },
            {
                data: 'amount_total_pending',
                className: "text-right",
                render: function(data, type, row) {
                    data = $.number(data);
                    var html = data;
                    if (parseFloat(data) > 0) {
                        var fined_all = row.amount_total_fined;
                        fined_all = $.number(fined_all);
                        if (parseFloat(row.amount_total_fined) > 0) {
                            html = '<span data-toggle="tooltip" data-placement="bottom" title="include total fine IDR ' + fined_all + '"><i class="fas fa-coins"></i> ' + data + '</span>';
                        }
                    }

                    return html;
                }
            },
<?php
if (($this->session->userdata('auth')) AND ($user_update_allowed !== null) AND (in_array($this->session->userdata('user'), $user_update_allowed))) {
?>
            {
                data: 'personal_data_id',
                orderable: false,
                render: function(data, type, rows) {
                    var html = '<div class="btn-group btn-group-sm" aria-label="">';
                    if (parseFloat(rows.amount_total_pending) > 0) {
                        // html += '<button type="button" class="btn btn-info btn-sm" id="btn_approval_invoice"><i class="fas fa-user-check"></i></button>';
                        let btn_invoice = '<a href="<?=site_url('finance/invoice/lists/')?>' + rows['personal_data_id'] + '" class="btn btn-info btn-sm" title="Invoice Data"><i class="fas fa-file-invoice-dollar"></i></a>';
                        let btn_history = '<a href="<?=base_url()?>devs/get_historycal_payment/' + rows['student_id'] + '" target="_blank" class="btn btn-sm btn-info" title="Download Historical Payment"><i class="fas fa-file"></i></a>';

                        html += btn_invoice;
                        html += btn_history;
                    }
                    html += '</div>';
                    return html;
                }
            }
<?php
}
?>
        ],
        createdRow: function( row, data, dataIndex){
            if (data.student_approved_payment_delay == 1) {
                $(row).addClass('bg-success');
            }
            else if (parseFloat(data.amount_total_pending) > 0) {
                $(row).addClass('bg-danger');
            }
        },
<?php
if (($this->session->userdata('auth')) AND ($user_update_allowed !== null) AND (in_array($this->session->userdata('user'), $user_update_allowed))) {
?>
        footerCallback: function (row, data, start, end, display) {
            var colsemester = 6;
            var coltotalsemester = 7;
            var data_table = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            value_semester = data_table.column(colsemester).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            value_semesterpage = data_table.column(colsemester, { page: 'current' }).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            value_totalsemester = data_table.column(coltotalsemester).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);

            value_totalsemesterpage = data_table.column(coltotalsemester, { page: 'current' }).data().reduce(function (a, b) {
                return intVal(a) + intVal(b);
            }, 0);
 
            $(data_table.column(colsemester).footer()).html(($.number(value_semesterpage, 0, ',', '.')) + '<br>From ' + ($.number(value_semester, 0, ',', '.')));
            $(data_table.column(coltotalsemester).footer()).html(($.number(value_totalsemesterpage, 0, ',', '.')) + '<br>From ' + ($.number(value_totalsemester, 0, ',', '.')));
        },
<?php
}
?>
    });

    $('button#btn_submit_approval').on('click', function(e) {
        e.preventDefault();
        var form = $('form#form_approval_invoice');
        form_data = new FormData(form[0]);
        let url = form.attr('action');

        $.blockUI({ baseZ: 2000 });
        $.ajax({
            url: url,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    toastr['success']('Data saved', 'Success!');
                    table_list.ajax.reload(null, false);
                    $('div#modal_approval_invoice').modal('hide');
                }
                else{
                    toastr.warning(rtn.message, 'Warning!');
                }
            },
            fail: function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            }
        });
    });

    $('button#submit_filter').on('click', function(e) {
        e.preventDefault();

        table_list.ajax.reload(null, false);
    });

    $('table#recap_list tbody').on('click', 'button[id="btn_approval_invoice"]', function(e) {
        e.preventDefault();

        var table_data = table_list.row($(this).parents('tr')).data();
        $('#student_form_name').html(table_data.personal_data_name + ' (' + table_data.study_program_abbreviation + '/' + table_data.academic_year_id + ')');
        $('input#approval_personal_data_id').val(table_data.personal_data_id);
        $('input#approval_student_id').val(table_data.student_id);
        $('div#modal_approval_invoice').modal('show');
    });

    function reload_data() {
        table_list.ajax.reload(null, true);
    }

    // $(document).ready(function() {
    //     setInterval(reload_data ,30000);
    // });
    
});
</script>