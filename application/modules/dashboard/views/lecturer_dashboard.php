<?=modules::run('academic/class_group/form_filter_class_group');?>
<div class="row mb-2">
    <div class="col-12">
        <div class="float-right">
            <div class="btn-group" role="group" aria-label="Button dashboard lecturer">
                <div class="btn-group" role="group">
                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Generate Assigment Letter
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <button class="dropdown-item" id="btn_generate_assignment_letter_personal" <?=(isset($show_all_dummy)) ? 'disabled="disabled"' : '';?>>
                            <i class="fas fa-sticky-note"></i> Generate / Download Teaching Assignment Letter
                        </button>
                        <button class="dropdown-item" id="btn_generate_assignment_community_personal">
                            <i class="fas fa-sticky-note"></i> Generate / Download Assignment Letter for Community
                        </button>
                        <button class="dropdown-item" id="btn_generate_assignment_research_personal" <?=(isset($show_all_dummy)) ? 'disabled="disabled"' : '';?>>
                            <i class="fas fa-sticky-note"></i> Generate / Download Assignment Letter for Research
                        </button>
                        <button class="dropdown-item <?= ((isset($is_advisor_current)) AND ($is_advisor_current)) ? '' : 'd-none';?>" id="btn_generate_assignment_advisor_thesis" <?=(isset($show_all_dummy)) ? 'disabled="disabled"' : '';?>>
                            <i class="fas fa-sticky-note"></i> Generate / Download Assignment Letter Advisor Thesis
                        </button>
                        <button class="dropdown-item <?= ((isset($is_examiner_current)) AND ($is_examiner_current)) ? '' : 'd-none';?>" id="btn_generate_assignment_examiner_thesis" <?=(isset($show_all_dummy)) ? 'disabled="disabled"' : '';?>>
                            <i class="fas fa-sticky-note"></i> Generate / Download Assignment Letter Examiner Thesis Defense
                        </button>
                        <button class="dropdown-item <?= ((isset($is_lecturer_internship)) AND ($is_lecturer_internship)) ? '' : 'd-none';?>" id="btn_generate_assignment_advisor_internship" <?=(isset($show_all_dummy)) ? 'disabled="disabled"' : '';?>>
                            <i class="fas fa-sticky-note"></i> Generate / Download Assignment Letter Advisor Internship
                        </button>
                    </div>
                </div>
                <input type="hidden" name="public_link_submit" id="public_link_submit" value="public/classlist/class/<?=$this->session->userdata('employee_id');?>/">
                <button type="button" id="copy_public_link" class="btn btn-info"><i class="fas fa-copy"></i> Copy Public Link</button>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        List Class
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="lecturer_class" class="table table-hover table-striped">
                <thead class="bg-dark">
                    <tr>
                        <th>No</th>
                        <th>Class Name</th>
                        <th>Study Program</th>
                        <th>Subject</th>
                        <th>Count Student</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_new_letter">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form onsubmit="return false" id="form_assignment_letter_community">
                    <div class="row">
                        <div class="col-md-6">
                            <span><i>another assigned:</i></span>
                            <div class="btn-group btn-group-sm btn-group-toggle ml-4" data-toggle="buttons">
                                <label class="btn btn-primary btn-sm active">
                                    <input type="radio" name="letter_assigns_to" id="letter_assigns_to_lecturer" autocomplete="off" value="lecturer"> Lecturer
                                </label>
                                <label class="btn btn-primary btn-sm">
                                    <input type="radio" name="letter_assigns_to" id="letter_assigns_student" autocomplete="off" value="student"> Student
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="option_lecturer" class="form-group">
                                <label for="employee_add_community">Lecturer</label>
                                <div class="input-group input-group-sm">
                                    <select name="employee_add_community" id="employee_add_community" class="form-control" style="width: 80%">
                                        <option value=""></option>
                                    </select>
                                    <button class="btn btn-success btn-sm w-10" id="btn_add_lecturer_community" type="button"><i class="fas fa-plus"></i> Add</button>
                                </div>
                            </div>
                            <div id="option_student" class="form-group d-none">
                                <label for="student_id_add_community">Student</label>
                                <div class="input-group input-group-sm">
                                <select name="student_id_add_community" id="student_id_add_community" class="form-control" style="width: 80%">
                                    <option value=""></option>
                                </select>
                                    <button class="btn btn-success btn-sm w-10" id="btn_add_student_community" type="button"><i class="fas fa-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="tablemember_community" class="table table-bordered table-hovered">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="generate_assignment_letter_community">Generate Letter</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var tablemember_community = $('#tablemember_community').DataTable({
            searching: false,
            info: false,
            paging: false,
            ordering: false
        });

        var lect_class = $('table#lecturer_class').DataTable({
            processing: true,
            paging: false,
            ajax: {
                url: '<?=base_url()?>academic/class_group/filter_class_group_data',
                type: 'POST',
                data: function(d) {
                    d.academic_year_id = $('#academic_year_id').val(),
                    d.semester_type_id = $('#semester_type_id_search').val(),
                    d.is_lecturer = 'YES',
                    d.show_all = '<?=(isset($show_all_dummy)) ? $show_all_dummy : "";?>'
                }
            },
            columns: [
                {data: 'class_group_id'},
                {
                    data: 'class_master_name',
                    render: function(data, type, row) {
                        // var btn = data;
                        // var class_master = row.class_master_id;
                        
                        if ($.type(row.class_master_id) !== "undefined") {
                            btn = '<a href="<?=base_url()?>academic/class_group/class_group_lists/' + row.class_master_id + '" target="blank">' + data + '</a>';
                        }else{
                            btn = row.class_group_name;
                        }
                        return btn;
                    }
                },
                {data: 'study_prog'},
                {data: 'study_subject'},
                {data: 'student_count'},
                {
                    data: 'class_master_id',
                    orderable: false,
                    render: function ( data, type, row ) {
                        var html = '';
                        // var class_master = row.class_master_id;
                        if ($.type(row.class_master_id) !== "undefined") {
                            html = '<div class="btn-group" role="group" aria-label="">';
                            html += '<a href="<?=base_url()?>academic/class_group/class_group_lists/' + row.class_master_id + '" class="btn btn-info btn-sm" id="btn_view_score" name="btn_view_score" title="view score absence" target="blank"><i class="fas fa-address-card"></i></a>';
                            html += '<a href="<?=base_url()?>academic/class_group/download_score_template/' + row.class_master_id + '" class="btn btn-info btn-sm <?=(isset($show_all_dummy)) ? 'disabled' : '';?>" id="btn_view_score" name="btn_view_score" title="<?=(isset($show_all_dummy)) ? 'action disable in dummy access' : 'download score template';?>"><i class="fas fa-file-download"></i></a>';
                            html += '</div>';
                        }
                        return html;
                    }
                }
            ]
        });

        lect_class.on( 'order.dt search.dt', function () {
            lect_class.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();

        $('select#employee_add_community').select2({
            allowClear: true,
            placeholder: "Please select..",
            minimumInputLength: 1,
            theme: "bootstrap",
            cache: false,
            ajax: {
                url: '<?=base_url()?>employee/get_lecturer_by_name',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    return {
                        keyword: params.term
                    }
                },
                processResults: function(result) {
                    data = result.data;
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.fullname,
                                id: item.personal_data_id,
                                employee_key: item.employee_id
                            }
                        })
                    }
                }
            }
        });
        
        $('select#student_id_add_community').select2({
            allowClear: true,
            placeholder: "Please select..",
            minimumInputLength: 1,
            theme: "bootstrap",
            cache: false,
            ajax: {
                url: '<?=base_url()?>student/get_student_by_name',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    return {
                        keyword: params.term
                    }
                },
                processResults: function(result) {
                    data = result.data;
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.personal_data_name + ' - ' + item.study_program_abbreviation + '/' + item.finance_year_id,
                                id: item.personal_data_id,
                                student_key: item.student_id
                            }
                        })
                    }
                }
            }
        });

        $('input[name=letter_assigns_to]').on('change', function(e) {
            e.preventDefault();

            if ($(this).val() == 'student') {
                $('#option_student').removeClass('d-none');
                $('#option_lecturer').addClass('d-none');
            }
            else {
                $('#option_lecturer').removeClass('d-none');
                $('#option_student').addClass('d-none');
            }
        });

        $('button#btn_add_lecturer_community').on('click', function(e) {
            e.preventDefault();

            var s_data = $('#employee_add_community').select2('data');
            add_table(s_data[0].text, s_data[0].employee_key, 'employee')
        });
        
        $('button#btn_add_student_community').on('click', function(e) {
            e.preventDefault();

            var s_data = $('#student_id_add_community').select2('data');
            add_table(s_data[0].text, s_data[0].student_key, 'student')
        });

        $('#tablemember_community tbody').on('click', 'button#remove_row_table', function(e) {
            e.preventDefault();

            tablemember_community.row($(this).parents('tr')).remove().draw();
        });

        $('button#copy_public_link').on('click', function(e) {
            e.preventDefault();

            var valuecopy = $('#public_link_submit').val() + "" + $('#academic_year_id').val() + $('#semester_type_id_search').val();
            const result = window.btoa(valuecopy);
            // console.log(result);
            // encode(valuecopy);
            valuecopy = encodeURIComponent(result);
            valuecopy = '<?=base_url()?>plink/goto/' + valuecopy;
            console.log(valuecopy);
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(valuecopy).select();
            document.execCommand("copy");
            $temp.remove();

            toastr.success('Copied the text: ' + valuecopy);
        });
        
        $('#form_filter_class_group').on('submit', function(e) {
            e.preventDefault();
            lect_class.ajax.reload();
            console.log('ada');
            return false;
        });
        
        $('select#academic_year_id').on('change', function(params) {
            var semester = $('#academic_year_id').val() + '' + $('#semester_type_id_search').val();
            if (($('#semester_type_id_search').val() == 4) || ($('#semester_type_id_search').val() == 6)) {
                $('#btn_generate_assignment_advisor_thesis').addClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').addClass('d-none');
            }
            else if (semester > 20210) {
                $('#btn_generate_assignment_advisor_thesis').removeClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').removeClass('d-none');
            }
            else {
                $('#btn_generate_assignment_advisor_thesis').addClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').addClass('d-none');
            }
        });

        $('select#semester_type_id_search').on('change', function(params) {
            var semester = $('#academic_year_id').val() + '' + $('#semester_type_id_search').val();
            if (($('#semester_type_id_search').val() == 4) || ($('#semester_type_id_search').val() == 6)) {
                $('#btn_generate_assignment_advisor_thesis').addClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').addClass('d-none');
                $('#btn_generate_assignment_advisor_internship').addClass('d-none');
            }
            if (($('#semester_type_id_search').val() == 7) || ($('#semester_type_id_search').val() == 8)) {
                $('#btn_generate_assignment_advisor_thesis').addClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').addClass('d-none');
                $('#btn_generate_assignment_advisor_internship').addClass('d-none');
            }
            else if (semester > 20210) {
                $('#btn_generate_assignment_advisor_thesis').removeClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').removeClass('d-none');
                $('#btn_generate_assignment_advisor_internship').removeClass('d-none');
            }
            else {
                $('#btn_generate_assignment_advisor_thesis').addClass('d-none');
                $('#btn_generate_assignment_examiner_thesis').addClass('d-none');
                $('#btn_generate_assignment_advisor_internship').addClass('d-none');
            }
            // console.log(semester);
        });

        $('button#btn_generate_assignment_advisor_thesis').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_thesis_advisor_dashboard';
            $.post(url, data, function(result) {
                // console.log(result);
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                // console.log('error');
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        $('button#btn_generate_assignment_advisor_internship').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_internship_advisor_dashboard';
            $.post(url, data, function(result) {
                // console.log(result);
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                // console.log('error');
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        $('button#btn_generate_assignment_examiner_thesis').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_thesis_examiner_dashboard';
            $.post(url, data, function(result) {
                // console.log(result);
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                // console.log('error');
                // console.log(params);
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        $('button#btn_generate_assignment_letter_personal').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_dashboard';
            $.post(url, data, function(result) {
                // console.log(result);
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                // console.log('error');
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        // $('button#btn_generate_assignment_community_personal').on('click', function(e) {
        //     e.preventDefault();

        //     $.blockUI({ baseZ: 2000 });
        //     var data = {
        //         academic_year_id : $('#academic_year_id').val(),
        //         semester_type_id : $('#semester_type_id_search').val()
        //     };

        //     var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_community_dashboard';
        //     $.post(url, data, function(result) {
        //         // console.log(result);
        //         $.unblockUI();
        //         if (result.code == 0) {
        //             var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
        //             window.location.href = loc;
        //         }
        //         else {
        //             toastr.warning(result.message, 'Warning!');
        //         }
        //     }, 'json').fail(function(params) {
        //         // console.log('error');
        //         $.unblockUI();
        //         toastr.error('Error Processing request!', 'error');
        //     })
        // });

        $('button#generate_assignment_letter_community').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var form = $('#form_assignment_letter_community');
            var form_data = $('#form_filter_class_group');

            let data = form_data.serialize() + '&' + form.serialize();
            $.post('<?=base_url()?>apps/letter_numbering/get_assigment_letter_community_dashboard', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                    $('#modal_new_letter').modal('hide');
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
            });
        })

        $('button#btn_generate_assignment_community_personal').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/check_assignment_letter_community_dahsboard';
            $.post(url, data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success', 'Success!');
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else if (result.code == 88) {
                    $('#modal_new_letter').modal('show');
                    <?php
                    if ($this->session->has_userdata('employee_id')) {
                    ?>
                    var nameid = 'employee_id[]';
                    var keyid = '<?=$this->session->userdata('employee_id');?>';
                    var type = 'employee';
                    <?php
                    }
                    else {
                    ?>
                    var nameid = 'student_id[]';
                    var keyid = '<?=$this->session->userdata('student_id');?>';
                    var type = 'student';
                    <?php
                    }
                    ?>
                    tablemember_community.rows().remove().draw();
                    let namecolumn = '<?=$this->session->userdata('name');?>' + ' <input type="hidden" name="' + nameid + '" value="' + keyid + '">';
                    var rowNode = tablemember_community.row.add( [ namecolumn, type.toUpperCase(), '' ] )
                        .draw()
                        .node();
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        $('button#btn_generate_assignment_research_personal').on('click', function(e) {
            e.preventDefault();

            $.blockUI({ baseZ: 2000 });
            var data = {
                academic_year_id : $('#academic_year_id').val(),
                semester_type_id : $('#semester_type_id_search').val()
            };

            var url = '<?=base_url()?>apps/letter_numbering/get_assigment_letter_research_dashboard';
            $.post(url, data, function(result) {
                // console.log(result);
                $.unblockUI();
                if (result.code == 0) {
                    var loc = '<?=base_url()?>apps/letter_numbering/download_template_result/' + result.file + '/' + result.doc_key;
                    window.location.href = loc;
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                // console.log('error');
                $.unblockUI();
                toastr.error('Error Processing request!', 'error');
            })
        });

        function add_table(nametext, id, type) {
            let btn_remove = '<button name="remove_row_table" id="remove_row_table" type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
            var nameid = (type == 'student') ? 'student_id[]' : 'employee_id[]';
            let namecolumn = nametext + ' <input type="hidden" name="' + nameid + '" value="' + id + '">';
            var rowNode = tablemember_community.row.add( [ namecolumn, type.toUpperCase(), btn_remove ] )
                .draw()
                .node();

            $( rowNode )
                .css( 'color', 'red' )
                .animate( { color: 'black' } );
        }
    });
</script>