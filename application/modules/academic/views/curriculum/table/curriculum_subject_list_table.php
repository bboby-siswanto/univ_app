<div class="table-responsive">
    <table id="table_curriculum" class="table table-bordered table-striped table-hover table-sm">
        <thead class="bg-dark">
            <tr>
                <th>Subject Name</th>
                <th>Subject Code</th>
                <th>Semester</th>
                <th>SKS</th>
                <th>Curriculum Subject Type</th>
                <th>Curriculum Subject Category</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal face" tabindex="-1" role="dialog" id="modal_input_status">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Subject Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_input_status_subject_ofse" onsubmit="return false">
                    <input type="hidden" name="curriculum_subject_id" id="curriculum_subject_id">
                    <div class="form-group">
                        <label>Select subject status:</label>
                        <select name="subject_status" id="subject_status" class="form-control">
                            <option value="">Please select...</option>
                            <option value="mandatory">Mandatory</option>
                            <option value="elective_uni">Elective Uni</option>
                            <option value="elective_fac">Elective Faculty</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-info float-right" id="submit_offered_subject">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="new_curriculum_add_offered_subject">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-modal">Offer Subjects</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal_curriculum_offer_subject">
                <?=modules::run('academic/offered_subject/form_filter_offered_subject');?>
                <div id="ofse_status" class="d-none">
                    <div class="form-group">
                        <label>Subject status</label>
                        <select name="subject_status" id="subject_status_cursub" class="form-control">
                            <option value="">Please select...</option>
                            <option value="mandatory">Mandatory</option>
                            <option value="elective_uni">Elective Uni</option>
                            <option value="elective_fac">Elective Faculty</option>
                        </select>
                    </div>
                </div>
                <div class="float-right">
                    <button id="btn_curriculum_offer_subject" class="btn btn-info">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var show_datatable_curriculum_subject = function(filter_data) {
            if ($.fn.DataTable.isDataTable('table#table_curriculum')) {
                curriculum_list_table.destroy();
            }

            curriculum_list_table = $('table#table_curriculum').DataTable({
                processing: true,
                ajax: {
                    url: '<?= base_url()?>academic/curriculum/filter_curriculum_subject_lists',
                    type: 'POST',
                    data: filter_data
                },
                order:[],
                dom: 'Bfrltip',
                buttons: [
	                'excelHtml5'
                ],
                columns: [
                    {data: 'subject_name'},
                    {data: 'subject_code'},
                    {data: 'semester_number'},
                    {data: 'sks'},
                    {data: 'curriculum_subject_type'},
                    {
                        data: 'curriculum_subject_category',
                        visible: ('<?= $this->uri->segment(2);?>' == 'offered_subject') ? false : true,
                        render: function(data, type, row) {
                            return ucwords(data);
                        }
                    },
                    {data: 'curriculum_subject_id'}
                ],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        render: function(data, type, row) {
                            var html = '<div class="btn-group" role="group" aria-label="">';
                            if ('<?=(isset($mode)) ? $mode : false;?>' == 'curriculum') {
                                html += '<button name="btn_edit_subject" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Edit Subject" ><i class="fas fa-edit"></i></button>';
                            } else {
                                html += '<button name="btn_curriculum_add_offered_subject" type="button" data_id="'+data+'" class="btn btn-info btn-sm" data-toggle="tooltip" title="Offer Subject" ><i class="fas fa-angle-double-right"></i></button>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        targets: [2,3],
                        visible: ($('#semester_id').val() == 17) ? false : true
                    }
                ]
            });
        }
        show_datatable_curriculum_subject({
            semester_id: '<?= ($s_semester_id) ? $s_semester_id : 'All' ?>',
            curriculum_id : '<?= ($curriculum_id) ? $curriculum_id : '';?>'
        });

        $('select#semester_type_id').on('change', function(e) {
            e.preventDefault();

            if (($('select#semester_type_id').val() == 4) || ($('select#semester_type_id').val() == 6)) {
                $('div#ofse_status').removeClass('d-none');
            }else{
                $('div#ofse_status').addClass('d-none');
            }
        });

        $('button#btn_new_curriculum').on('click', function(e) {
            e.preventDefault();
            var s_curriculum_id = '<?= $curriculum_id; ?>';
            $.post('<?= base_url()?>academic/curriculum/form_create_curriculum_subject/' + s_curriculum_id + '/<?= $s_semester_id?>', function(result) {
                $('div#modal_input_curriculum').html(result.data);
                $('.title-modal').text('Add new curriculum subjects');
                $('div#new_curriculum_modal').modal('show');
            },'json');
        });

        $('button#btn_filter_curriculum_subject').on('click', function(e){
            e.preventDefault();
            
            show_datatable_curriculum_subject({
                semester_id: $('#semester_id').val(),
                curriculum_id : '<?= $curriculum_id;?>'
            });
        });

        $('#btn_filter_curriculum_offered_subject').on('click', function(e) {
            e.preventDefault();

            if (($('#active_year_id').val() == '') || ($('#curriculum_id').val() == '') || ($('#semester_id').val() == '')) {
                toastr['warning']('Please select curriculum filter field ', 'Warning');
            }else{
                let text = [
                    $('select#curriculum_id option:selected').text(), 
                    $('select#active_year_id option:selected').text(),
                    $('select#semester_id option:selected').text()
                ].join('/');

                $('#curriculum_data_filter').text(' (' + text + ')');

                show_datatable_curriculum_subject({
                    semester_id: $('select#semester_id').val(),
                    curriculum_id : $('#curriculum_id').val()
                });

                if ($('#semester_id').val() == 17) {
                    offer_subject_datatable.column( 3 ).visible( true );
                    offer_subject_datatable.column( 4 ).visible( true );
                }else{
                    offer_subject_datatable.column( 3 ).visible( false );
                    offer_subject_datatable.column( 4 ).visible( false );
                }

                $('html, body').animate({
                    scrollTop: $("#offered_subject_table").offset().top
                }, 500);
            }
        });

        $('table#table_curriculum tbody').on('click', 'button[name="btn_edit_subject"]', function(e) {
            e.preventDefault();
            var s_curriculum_subject_id = $(this).attr("data_id");
            var s_curriculum_id = '<?= $curriculum_id; ?>';
            $.post('<?= base_url()?>academic/curriculum/form_create_curriculum_subject/' + s_curriculum_id, {curriculum_subject_id: s_curriculum_subject_id}, function(result) {
                $('div#modal_input_curriculum').html(result.data);
                $('.title-modal').text('Update curriculum subject');
                $('div#new_curriculum_modal').modal('show');
            },'json');
        });

        // $('table#table_curriculum tbody').on('click', 'button[name="btn_curriculum_table_offer_subject"]', function(e) {
        //     e.preventDefault();
        //     var s_curriculum_subject_id = $(this).attr("data_id");
        //     $('#curriculum_subject_id_cursub').val(s_curriculum_subject_id);
        //     $('.title-modal').text('Add to Offered Subject');
        //     $('#new_curriculum_add_offered_subject').modal('show');
        // });

        $('table#table_curriculum tbody').on('click', 'button[name="btn_curriculum_add_offered_subject"]', function(e) {
            e.preventDefault();

            var s_semester_type_id = $('#semester_type_id').val();
            var s_curriculum_subject_id = $(this).attr("data_id");

            if ((s_semester_type_id == 4) || (s_semester_type_id == 6)) {
                $('#subject_status').val('');
                $('input#curriculum_subject_id').val(s_curriculum_subject_id);
                $('div#modal_input_status').modal('show');
            }else{
                set_offered_subject(s_curriculum_subject_id, false);
            }
        });

        $('#btn_curriculum_offer_subject').on('click', function(e) {
            e.preventDefault();
            $.blockUI({ baseZ: 2000 });

            var data = $('#form_filter_offer_subject').serialize();
            // console.log(data);return false;
            $.post('<?= base_url()?>academic/offered_subject/save_offer_subject', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#new_curriculum_add_offered_subject').modal('hide');
                    toastr['success']('Success transfer data', 'Success');
                    if($.fn.DataTable.isDataTable(curriculum_list_table)){
                        curriculum_list_table.ajax.reload();
                    }else{
                        window.location.reload();
                    }
                }else{
                    toastr['warning'](result.message, 'Warning');
                }
            },'json').fail(function(params) {
                $.unblockUI();
            });
        });

        $('button#submit_offered_subject').on('click', function(e) {
            e.preventDefault();

            var s_curr_sub_id = $('input#curriculum_subject_id').val();
            if (s_curr_sub_id == '') {
                toastr.warning('Error retrieve curriculum data!', 'Warning!');
            }else{
                set_offered_subject(s_curr_sub_id, true);
            }
        });

        $('table#table_curriculum tbody').on('click', 'button[name="btn_delete_subject"]', function(e) {
            e.preventDefault();

            if(confirm("Are you sure deleted this item ?")) {
                var s_curriculum_subject_id = $(this).attr("data_id");
                $.post('<?= base_url()?>academic/curriculum/delete_subject_curriculum', {curriculum_subject_id: s_curriculum_subject_id}, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('curriculum subject has been removed', 'Success');
                        if ($.fn.DataTable.isDataTable(curriculum_list_table)) {
                            curriculum_list_table.ajax.reload();
                        }else{
                            window.location.reload();
                        }
                    }else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                },'json');
            }
        });

        function set_offered_subject(s_curriculum_subject_id, ofse = false) {
            // $.blockUI();
            var data = $('#form_filter_offer_subject').serialize();
            if (ofse == true) {
                var s_ofse = $('#subject_status').val();
                data += '&is_ofse=true&subject_status=' + s_ofse;
            }else{
                data += '&is_ofse=false';
            }
            data += '&curriculum_subject_id=' + s_curriculum_subject_id;
            $.post('<?= base_url()?>academic/offered_subject/save_offer_subject', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('div#modal_input_status').modal('hide');
                    toastr['success']('Success transfer data', 'Success');
                    if($.fn.DataTable.isDataTable(offer_subject_datatable)){
                        offer_subject_datatable.ajax.reload();
                    }else{
                        window.location.reload();
                    }
                }else if(result.code == 2){
                    toastr.warning(result.message, 'Warning');
                }else{
                    toastr['warning'](result.message, 'Warning');
                    $('html, body').animate({
                        scrollTop: $("#target").offset().top
                    }, 500);
                }
            },'json').fail(function(params) {
                $.unblockUI();
            });
        }
    });
</script>