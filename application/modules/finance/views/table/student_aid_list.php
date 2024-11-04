<div class="card">
    <div class="card-header">
        Period <?=$aid_period->aid_period_year;?>/<?=$aid_period->aid_period_month;?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hovered" id="student_aid_list">
                <thead class="bg-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Student Number</th>
                        <th>Batch</th>
                        <th>Faculty</th>
                        <th>Study Program</th>
                        <th>Student Email</th>
                        <th>Student Phone Number</th>
                        <th>Student Cellular</th>
                        <th>Total Amount Requested</th>
                        <th>Total Amount Approved</th>
                        <th>Name of Bank</th>
                        <th>Branch</th>
                        <th>Bank Account Number</th>
                        <th>Beneficiary Name</th>
                        <th>Note</th>
                        <th>Files</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="8">Total</td>
                        <td>0</td>
                        <td>0</td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_amount_accepted_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total Amount Approved</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit_amount_approved_student_aid">
                    <input type="hidden" name="request_id" id="input_request_id_amount">
                    <div class="form-group">
                        <input type="text" name="request_amount_accepted" id="request_amount_accepted" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_amount_appoved">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="edit_note_aid_modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_edit_note_student_aid">
                    <input type="hidden" name="request_id" id="input_request_id_note">
                    <div class="form-group">
                        <textarea name="request_note" id="request_note" cols="30" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_request_note">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var student_aid_list_table = $('table#student_aid_list').DataTable({
            processing: true,
            dom: 'Bfrtip',
            paging: false,
            info: false,
            buttons: [
                {
                    text: 'Download Excel',
                    extend: 'excel',
                    footer: true,
                    title: 'Online Expense Refund',
                    exportOptions: {columns: ':visible'}
                },
                // {
                //     text: 'Print',
                //     extend: 'print',
                //     title: 'Online Expense Refund',
                //     exportOptions: {columns: ':visible'}
                // },
                'colvis'
            ],
            ajax: {
                url: '<?=site_url('finance/student_finance/get_student_aid_list')?>',
                type: 'POST',
                data: function(params) {
                    params.aid_period_id = '<?=$aid_period->aid_period_id;?>';
                    // var a_filter_data = objectify_form(a_form_data);
                    return params;
                }
            },
            columns: [
                {data: 'personal_data_name'},
                {data: 'student_number'},
                {data: 'student_batch'},
                {
                    data: 'faculty_name',
                    visible: false
                },
                {
                    data: 'study_program_name',
                    visible: false
                },
                {
                    data: 'student_email',
                    visible: false
                },
                {
                    data: 'personal_data_phone',
                    visible: false
                },
                {
                    data: 'personal_data_cellular',
                    visible: false
                },
                {
                    data: 'request_amount',
                    render: function(data, type, row) {
                        return formatter.format(data);
                    }
                },
                {
                    data: 'request_amount_accepted',
                    render: function(data, type, row) {
                        data = formatter.format(data);
                        return '<button class="btn btn-link" id="btn_edit_approved_amount">' + data + '</button>';
                    }
                },
                {data: 'bank_name'},
                {data: 'request_bank_branch'},
                {
                    data: 'request_account_number',
                    render: function(data, type, row) {
                        // return '"=' + data + '"';
                        return data + ".";
                    }
                },
                {data: 'request_beneficiary'},
                {
                    data: 'request_note',
                    orderable: false,
                    render: function(data, type, row) {
                        if (data === null) {
                            return '<button class="btn btn-link" id="btn_edit_note"><i class="fas fa-edit"></i></button>';
                        }else{
                            return '<button class="btn btn-link" id="btn_edit_note">' + data + '</button>';
                        }
                    }
                },
                {
                    data: 'request_id',
                    render: function(data, type, row) {
                        return '<button class="btn btn-link" id="btn_download_files" title="Download"><i class="fas fa-download"></i></button>';
                    }
                }
            ],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                total = api.column(8).data().reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                }, 0 );
                
                total_accepted = api.column(9).data().reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
    
                // Total over this page
                pageTotal = api.column( 8, { page: 'current'} ).data().reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
                
                pageTotal_accepted = api.column( 9, { page: 'current'} ).data().reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
    
                // Update footer
                $( api.column( 8 ).footer() ).html(formatter.format(pageTotal));
                $( api.column( 9 ).footer() ).html(formatter.format(pageTotal_accepted));
            }
        });

        $('#request_amount_accepted').number( true, 0 );

        $('table#student_aid_list tbody').on('click', 'button#btn_download_files',function(e) {
            e.preventDefault();
            var table_data = student_aid_list_table.row($(this).parents('tr')).data();
            console.log(table_data);
            
            window.location.href = '<?=base_url()?>finance/student_finance/download_files/' + table_data.request_id;
        });
        
        $('table#student_aid_list tbody').on('click', 'button#btn_edit_note',function(e) {
            e.preventDefault();
            var table_data = student_aid_list_table.row($(this).parents('tr')).data();
            
            $('#request_note').text(table_data.request_note);
            $('#input_request_id_note').val(table_data.request_id);
            
            $('div#edit_note_aid_modal').modal('show');
        });

        $('table#student_aid_list tbody').on('click', 'button#btn_edit_approved_amount',function(e) {
            e.preventDefault();
            var table_data = student_aid_list_table.row($(this).parents('tr')).data();
            
            $('#request_amount_accepted').val(table_data.request_amount_accepted);
            $('#input_request_id_amount').val(table_data.request_id);
            
            $('div#edit_amount_accepted_modal').modal('show');
        });

        $('button#submit_request_note').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 9999});

            var data = $('form#form_edit_note_student_aid').serialize();
            var uri = '<?=base_url()?>finance/student_finance/update_request_note';
            $.post(uri, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success');
                    student_aid_list_table.ajax.reload(null, false);

                    $('div#edit_note_aid_modal').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        });
        
        $('button#submit_amount_appoved').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 9999});

            var data = $('form#form_edit_amount_approved_student_aid').serialize();
            var uri = '<?=base_url()?>finance/student_finance/update_request_amount_approved';
            $.post(uri, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success');
                    student_aid_list_table.ajax.reload(null, false);

                    $('div#edit_amount_accepted_modal').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        });

    });
</script>