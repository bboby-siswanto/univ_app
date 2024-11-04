<div class="table table-responsive">
    <table id="offered_subject_table" class="table table-bordered table-striped table-sm">
        <thead class="bg-dark">
            <th>Subject Name</th>
            <th>Lecturer</th>
            <th>Examiner 1</th>
            <th>Examiner 2</th>
            <th>SKS</th>
            <th>Subject Status</th>
            <th>Subject Type</th>
            <th>Action</th>
        </thead>
    </table>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_input_lecturer">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Team Teaching</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_curriculum_offer_subject">
                <?=modules::run('academic/offered_subject/form_input_lecturer');?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_input_lecturer_ofse">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Add Examiner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_curriculum_offer_subject">
                <?=modules::run('academic/offered_subject/form_input_examiner');?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="class_modal_view_lecturer">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Team Teachings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="lists_lecturer_view">
                <?=modules::run('academic/offered_subject/view_lecturer_lists');?>
            </div>
        </div>
    </div>
</div>
<div id="input_password" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enter Your Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" onsubmit="return false()" id="form-input-key">
                    <div class="form-group">
                        <!-- <input type="hidden" name="set_url" id="set_url">
                        <input type="hidden" name="set_data" id="set_data"> -->
                        <input type="password" class="form-control" name="offered_subject_key" id="offered_subject_key">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_offered_key">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var table_offer_subject = function(filter_data) {
        if($.fn.DataTable.isDataTable('table#offered_subject_table')){
            offer_subject_datatable.destroy();
        }
        
        var term = {term: filter_data};

        offer_subject_datatable = $('table#offered_subject_table').DataTable({
            // responsive: true,
            processing: true,
            order: [[ 0, "asc" ]],
            dom: 'Bfrtip',
            buttons: [
            {
                text: 'Excel',
                extend: 'excel',
                title: 'Offered Subject List',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Pdf',
                extend: 'pdf',
                title: 'Offered Subject List',
                exportOptions: {columns: ':visible'}
            },
            {
                text: 'Print',
                extend: 'print',
                title: 'Offered Subject List',
                exportOptions: {columns: ':visible'}
            }
        ],
            ajax: {
                url: '<?= base_url()?>academic/offered_subject/filter_offered_subject_lists',
                type: 'POST',
                data: term
            },
            columns: [
                {data: 'subject_name'},
                {data: 'lecturer_subject'},
                {data: 'lecturer_subject'},
                {data: 'lecturer_subject'},
                {
                    data: 'curriculum_subject_credit',
                    // render: function(data, type, row) {
                    //     if ('<?=$this->session->userdata("user")?>' == '47013ff8-89df-11ef-8f45-0068eb6957a0') {
                    //         return  row.curriculum_subject_id;
                    //     }else{
                    //         return data;
                    //     }
                    // }
                },
                {data: 'ofse_status'},
                {data: 'curriculum_subject_type'},
                {data: 'offered_subject_id'}
            ],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        if(row['deleteable'] === true){
                            html += '<button name="btn_remove_offered_subject" type="button" data_id="' + data + '" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove Offer Subject"><i class="fas fa-trash"></i></button>';
                        }
                        html += '<button name="btn_team_teaching" type="button" data_id="' + data + '" class="btn btn-success btn-sm" data-toggle="tooltip" title="Team Teaching"><i class="fas fa-user-plus"></i></button>';
                        html += '<button name="btn_view_lecturer" type="button" id="btn_view_lecturer" class="btn btn-info" title="View lecturer"><i class="fas fa-users"></i></button>';
                        html += '</div>';
                        return html;
                    }
                },
                {
                    targets: 2,
                    visible: false,
                    render: function(data, type, row) {
                        let lect = row.lecturer_data;
                        if (lect.length > 0) {
                            return lect[0];
                        }else{
                            return '';
                        }
                    }
                },
                {
                    targets: 3,
                    visible: false,
                    render: function(data, type, row) {
                        let lect = row.lecturer_data;
                        if (lect.length > 1) {
                            return lect[1];
                        }else{
                            return '';
                        }
                    }
                },
                {
                    targets: [-3, -2],
                    visible: false,
                    render: function( data, type, row) {
                        var status = '';
                        if (data != null) {
                            status.toUpperCase();
                            status = data.replace(/_/g, ' ');
                        }
                        return status;
                    }
                }
            ],
        });
    };
    $(function() {
        $(document).on('show.bs.modal', '.modal', function (event) {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });
        
        table_offer_subject({
            academic_year_id: $('#academic_year_id').val(),
            program_id: $('#program_id').val(),
            study_program_id: $('#study_program_id').val(),
            semester_type_id: $('#semester_type_id').val()
        });

        $('select#study_program_id, select#academic_year_id, select#semester_type_id').on('change', function(e) {
            e.preventDefault();
            toggle_ofse();
        });

        $('button#btn_filter_offered_subject').on('click', function(e) {
            e.preventDefault();

            if (($('#academic_year_id').val() == '') || ($('#program_id').val() == '') || ($('#study_program_id').val() == '') || ($('#semester_type_id').val() == '')) {
                toastr['warning']('Please select offered subject filter field ', 'Warning');
            }else{
                toggle_ofse();
                $('html, body').animate({
                    scrollTop: $("#offered_subject_table").offset().top
                }, 500);
            }
        });

        $('table#offered_subject_table tbody').on('click', 'button[name="btn_view_lecturer"]', function(e) {
            e.preventDefault();
            var row_data = offer_subject_datatable.row($(this).parents('tr')).data();

            show_table_data_lecturer_offer_subject({offered_subject_id : row_data.offered_subject_id});
            $('div#class_modal_view_lecturer').modal('show');
        });

        $('table#offered_subject_table tbody').on('click', 'button[name="btn_team_teaching"]', function(e) {
            e.preventDefault();
            var row_data = offer_subject_datatable.row($(this).parents('tr')).data();
            var row_index = offer_subject_datatable.row($(this).parents('tr')).index();
            if (($('#semester_type_id').val() == 4) || ($('#semester_type_id').val() == 6)) {
                var lect = row_data.lecturer_data;
                if (lect.length >= 2) {
                    toastr['warning']('Examiner is available', 'Warning');
                }else{
                    $('#offered_subject_id_ofse').val(row_data.offered_subject_id);
                    $('#ex_personal_data_name').val('');
                    $('#ex_employee_id').val('');
                    // $('#row_index_offer_subject').val(row_index);
                    $('div#class_modal_input_lecturer_ofse').modal('show');
                }
            }else{
                $('#form_input_lecturer').find('input, select').val('');
                $('#curriculum_subject_credit').text('');
                $('#remaining_allocation').text('');
                $('.dosen_pengampu').removeClass('show').addClass('d-none');
                $('#lecturer_reported').val('0');
                $("#offered_subject_type").val(row_data.curriculum_subject_type);
                
                var sks_available = (row_data.curriculum_subject_credit - row_data.sks_count_total).toFixed(2);
                if (row_data.sks_count_total < row_data.curriculum_subject_credit) {
                    $('#offered_subject_id').val(row_data.offered_subject_id);
                    $('#curriculum_subject_credit').text(row_data.curriculum_subject_credit);
                    $('#remaining_allocation').text(sks_available);
                    $('#remaining_credit').val(sks_available);
                    $('#row_index_offer_subject').val(row_index);
                    $('div#class_modal_input_lecturer').modal('show');
                }else{
                    toastr['warning']('subject credit available = ' + sks_available, 'Warning');
                }
            }
        });

        function remove_offered_subject(s_offered_subject_id) {
            $.blockUI();
            $.post('<?= base_url()?>academic/offered_subject/remove_offered_subject', {offered_subject_id: s_offered_subject_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('Success remove offered subject', 'Success');
                    if ($.fn.DataTable.isDataTable(offer_subject_datatable)) {
                        offer_subject_datatable.ajax.reload(null, false);
                    }else{
                        window.location.reload();
                    }
                }else{
                    toastr['warning'](result.message, 'Warning');
                }
            }, 'json').fail(function(xhr, txtStatus, errThrown) {
                $.unblockUI();
            });
        }

        $('table#offered_subject_table tbody').on('click', 'button[name="btn_remove_offered_subject"]', function(e) {
            e.preventDefault();
            $.blockUI();
            var s_offered_subject_id = $(this).attr("data_id");
            $.post('<?=base_url()?>academic/offered_subject/validate_student_offered_subject', {offered_subject_id: s_offered_subject_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    if (confirm("Are you sure?")) {
                        remove_offered_subject(s_offered_subject_id);
                    }
                }else{
                    toastr.warning('Student have already taken this offered subject!', 'Warning!');
                    // if (confirm("Student have already taken this offered subject! Continue remove ?")) {
                    //     remove_offered_subject(s_offered_subject_id);
                    // }
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error proccessing data!','Error');
            });
        });

        // $('table#offered_subject_table tbody').on('click', 'button[name="btn_remove_offered_subject"]', function(e) {
        //     e.preventDefault();
        //     if (confirm("Are you sure?")) {
        //         $.blockUI();

        //         var s_offered_subject_id = $(this).attr("data_id");
        //         $.post('<?= base_url()?>academic/offered_subject/remove_offered_subject', {offered_subject_id: s_offered_subject_id}, function(result) {
        //             $.unblockUI();
        //             if (result.code == 0) {
        //                 toastr['success']('Success remove offered subject', 'Success');
        //                 if ($.fn.DataTable.isDataTable(offer_subject_datatable)) {
        //                     offer_subject_datatable.ajax.reload(null, false);
        //                 }else{
        //                     window.location.reload();
        //                 }
        //             }else{
        //                 toastr['warning'](result.message, 'Warning');
        //             }
        //         }, 'json').fail(function(xhr, txtStatus, errThrown) {
        //             $.unblockUI();
        //         });
        //     }
        // });

        function toggle_ofse() {
            filter_offered_subject();
            if (($('#semester_type_id').val() == 4) || ($('#semester_type_id').val() == 6)) {
                offer_subject_datatable.column( -3 ).visible( true );
                offer_subject_datatable.column( 2 ).visible( true );
                offer_subject_datatable.column( 3 ).visible( true );
                offer_subject_datatable.column( 4 ).visible( false );
                offer_subject_datatable.column( -2 ).visible( false );
                offer_subject_datatable.column( 1 ).visible( false );
            }else{
                offer_subject_datatable.column( -3 ).visible( false );
                offer_subject_datatable.column( 2 ).visible( false );
                offer_subject_datatable.column( 3 ).visible( false );
                offer_subject_datatable.column( 4 ).visible( true );
                offer_subject_datatable.column( -2 ).visible( true );
                offer_subject_datatable.column( 1 ).visible( true );
            }
        }

        $('button#submit_offered_key').on('click', function(e) {
            e.preventDefault();
            if ($('input#offered_subject_key').val() != '') {
                var key = btoa($('input#offered_subject_key').val());
                $.post('<?=base_url()?>academic/offered_subject/validate_key', {key: key}, function(result) {
                    if (result.code == 0) {
                        $('div#input_password').modal('hide');
                        $('input#offer_subject_access').val('1');
                        $('button#save_class_lecturer_offered_subject').click();
                    }else{
                        toastr.warning(result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    toastr.error('Error processing data!', 'Error!');
                })
            }else{
                toastr.warning('Please input your password!', 'Warning!!');
            }
        });

        function filter_offered_subject() {
            if (($('#study_program_id').val() != '') && ($('#program_id').val() != '') && ($('#academic_year_id').val() != '') && ($('#semester_type_id').val() != '')) {

                let text = [
                    $('select#study_program_id option:selected').data('abbr'), 
                    $('#academic_year_id option:selected').text(),
                    $('#semester_type_id option:selected').text()
                ].join('/');

                $('#data_filter').text(' (' + text + ')');
                
                table_offer_subject({
                    academic_year_id: $('#academic_year_id').val(),
                    program_id: $('#program_id').val(),
                    study_program_id: $('#study_program_id').val(),
                    semester_type_id: $('#semester_type_id').val()
                });
            }
        }
    });
</script>