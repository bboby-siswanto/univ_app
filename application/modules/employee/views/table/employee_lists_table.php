<div class="table-responsive">
    <table id="employee_lists" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Name</th>
                <th>ID Number</th>
                <th>KTP Number</th>
                <th>NPWP</th>
                <th>Join Date</th>
                <th>IULI Email</th>
                <th>Status</th>
                <th>Dept</th>
                <th>Sub Dept</th>
                <th>NIDN</th>
                <th>Job Title</th>
                <th>Homebase</th>
                <th>Category</th>
                <th>Academic Rank</th>
                <th>Leave Allowance</th>
                <th>Working Hour Status</th>
                <th>PTKP</th>
                <th>Place of Birth</th>
                <th>Date of Birth</th>
                <th>Bank Name</th>
                <th>Bank Account No.</th>
                <th>Bank Holder</th>
                <th>Religion</th>
                <th>Personal HP</th>
                <th>Personal Email</th>
                <th>Citizenship</th>
                <th>Address</th>
                <th>Blood Group</th>
                <th>Contact for Urgent</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_em_dept">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Employee List Department</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <input type="hidden" name="employee_dept" id="employee_dept">
                            <table class="table table-hover" id="table_employee_department">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Department Name</th>
                                        <th>Abbreviation</th>
                                    </tr>
                                </thead>
                                <tbody>
                    <?php
                    if ((isset($list_dept)) AND ($list_dept)) {
                        foreach ($list_dept as $o_department) {
                    ?>
                                    <tr>
                                        <td></td>
                                        <td><?=$o_department->department_name;?></td>
                                        <td><?=$o_department->department_abbreviation;?></td>
                                    </tr>
                    <?php
                        }
                    }
                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_submit_employee_dept">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var table_employee_department = $('#table_employee_department').DataTable({
        columnDefs: [
            {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }
        ],
        // fixedColumns: {
        //     start: 2
        // },
        // order: [[1, 'asc']],
        // scrollCollapse: true,
        // scrollX: true,
        // scrollY: 300,
        select: {
            style: 'multi',
            selector: 'td:first-child'
        }

    });
    var employee_table = $('table#employee_lists').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'colvis'
        ],
        ajax: {
            url: '<?= base_url()?>employee/get_filter_data',
            type: 'POST',
            data: function(params) {
                let a_form_data = $('form#form_filter_employee').serialize();
                return a_form_data;
            }
        },
        columns: [
            {
                data: 'fullname',
                render: function(data, type, row) {
                    return '<a href="<?=base_url()?>employee/profile/' + row.employee_id + '/' + row.personal_data_id + '" target="_blank">' + data + '</a>';
                }
            },
            {data: 'employee_id_number'},
            {
                data: 'personal_data_id_card_number',
                visible: false,
            },
            {
                data: 'personal_data_npwp_number',
                visible: false,
            },
            {
                data: 'employee_join_date',
                visible: false,
            },
            {data: 'employee_email'},
            {data: 'status'},
            {data: 'department_name'},
            {data: 'sub_department',
                render: function(data, type, row) {
                    var sub_dept = row.department_name;
                    if (data !== false) {
                        sub_dept = '';
                        $.each(data, function(i, v) {
                            sub_dept += '<li>' + v.department_name + '</li>';
                        });
                    }

                    return sub_dept;
                }
            },
            {data: 'employee_lecturer_number'},
            {
                data: 'employee_job_title',
                visible: false,
            },
            {
                data: 'employee_id',
                visible: false,
            }, // homebase
            {
                data: 'employment_group',
                visible: false,
            },
            {
                data: 'employee_academic_rank',
                visible: false,
            },
            {
                data: 'employee_leave_allowance',
                visible: false,
            },
            {
                data: 'employee_working_hour_status',
                visible: false,
            },
            {
                data: 'employee_pkpt',
                visible: false,
            },
            {
                data: 'personal_data_place_of_birth',
                visible: false,
            },
            {
                data: 'personal_data_date_of_birth',
                visible: false,
            },
            {
                data: 'employee_id', // bank name
                visible: false,
            },
            {
                data: 'employee_id',  // bank account
                visible: false,
            },
            {
                data: 'employee_id', // bank holder
                visible: false,
            },
            {
                data: 'employee_id', // religion
                visible: false,
            },
            {
                data: 'personal_data_phone', // personal_hp
                visible: false,
            },
            {
                data: 'personal_data_email', // personal_email
                visible: false,
            },
            {
                data: 'personal_data_nationality',
                visible: false,
            },
            {
                data: 'employee_id', // address text
                visible: false,
            },
            {
                data: 'personal_data_blood_group',
                visible: false,
            },
            {
                data: 'employee_id', // contact
                visible: false,
            },
            {
                data: 'employee_id',
                orderable: false,
                render: function(data, type, row) {
                    var dl_reff_letter = '<button id="download_reff_letter" type="button" class="btn dropdown-item" title="Download Refference Letter"><i class="fas fa-file-word"></i> Download Refference Letter</button>';
                    var dl_reff_letter_resign = '<button id="download_reference_letter_resign" type="button" class="btn dropdown-item" title="Download Refference Letter (Resign)"><i class="fas fa-file-word"></i> Download Refference Letter (Resign)</button>';
                    var dl_request_nidn = '<button id="download_permohonan_nidn" type="button" class="btn dropdown-item" title="Download Permohonan NIDN"><i class="fas fa-file-word"></i> Download Permohonan NIDN Rektor</button>';
                    var dl_lls_butuh_letter = '<button id="download_lolos_butuh" type="button" class="btn dropdown-item" title="Download Lolos Butuh Letter"><i class="fas fa-file-word"></i> Download Lolos Butuh Letter</button>';
                    var btn_download = '<div class="btn-group" role="group">';
                    btn_download += '<button id="btn_group_download" type="button" class="btn btn-sm btn-info" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Download File"><i class="fas fa-download"></i></button>';
                    btn_download += '<div class="dropdown-menu" aria-labelledby="btn_group_download">';
                    btn_download += dl_reff_letter;
                    btn_download += dl_reff_letter_resign;
                    btn_download += dl_request_nidn;
                    btn_download += dl_lls_butuh_letter;
                    btn_download += '</div></div>';
                    html = '<div class="btn-group btn-group-sm" role="group">';
                    // html += '<button id="employee_setting" class="btn btn-sm btn-info"><i class="fas fa-cog"></i></button>';
                    html += btn_download;
                    html += '<button type="button" class="btn btn-sm btn-info" id="btn_sub_dept" title="Sub Department"><i class="fas fa fa-city"></i></button>';
                    html += '</div>';
                    return html;
                }
            }
        ]
    });

    $(function() {
        $('button#filter_employee_button').on('click', function(e) {
            e.preventDefault();
            employee_table.ajax.reload();
        });

        $('table#employee_lists tbody').on('click', 'button#btn_sub_dept', function(e) {
            e.preventDefault();
            let table_data = employee_table.row($(this).parents('tr')).data();

            let employee_sub_dept = table_data.sub_department;
            if (employee_sub_dept !== false) {
                var rowtable = table_employee_department.rows().data();
                $.each(rowtable, function(idx, tablerow) {
                    $.each(employee_sub_dept, function(i, v) {
                        if (tablerow[2] == v.department_abbreviation) {
                            table_employee_department.row(':eq(' + idx + ')').select();
                            console.log(tablerow[2]);
                        }
                    })
                })
            }
            
            $('#employee_dept').val(table_data.employee_id);
            $('#modal_em_dept').modal('show');
        });

        $('table#employee_lists tbody').on('click', 'button#download_reff_letter', function(e) {
            e.preventDefault();
            
            if (confirm('Generate new number ?')) {
                // alert('fungsi sedang dibuat');
                var table_data = employee_table.row($(this).parents('tr')).data();
                window.location.href = "<?=base_url()?>employee/generate_reference_letter/" + table_data.employee_id;
            }
            
        });

        $('table#employee_lists tbody').on('click', 'button#download_lolos_butuh', function(e) {
            e.preventDefault();
            
            if (confirm('Generate new number ?')) {
                // alert('fungsi sedang dibuat');
                var table_data = employee_table.row($(this).parents('tr')).data();
                window.location.href = "<?=base_url()?>employee/generate_lolos_butuh_employee/" + table_data.employee_id;
            }
            
        });

        $('table#employee_lists tbody').on('click', 'button#download_reference_letter_resign', function(e) {
            e.preventDefault();
            
            if (confirm('Generate new number ?')) {
                // alert('fungsi sedang dibuat');
                var table_data = employee_table.row($(this).parents('tr')).data();
                window.location.href = "<?=base_url()?>employee/generate_reference_letter_resign/" + table_data.employee_id;
            }
            
        });

        $('table#employee_lists tbody').on('click', 'button#download_permohonan_nidn', function(e) {
            e.preventDefault();
            
            var table_data = employee_table.row($(this).parents('tr')).data();
            $.blockUI();
            $.post('<?=base_url()?>apps/letter_numbering/generate_permohonan_nidn_rektor', {employee_id: table_data.employee_id}, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!');
                    setTimeout(function() {
                        window.location.href = '<?=base_url()?>file_manager/staff_download/<?=$this->session->userdata('user');?>/' + result.doc_key;
                    } ,1000);
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error processing data!');
            });
        });

        $("#btn_submit_employee_dept").on('click', function(e) {
            e.preventDefault();
            
            var checked = table_employee_department.rows({selected: true});
            var count_checked = checked.count();
            if (count_checked > 0) {
                $.blockUI({ baseZ: 2000 });

                var data_checked = checked.data();
                var a_dept_abbr = [];
                for (let i = 0; i < data_checked.length; i++) {
                    var dept_abbr = data_checked[i][2];
                    a_dept_abbr.push(dept_abbr);
                }

                var a_data = {
                    data: a_dept_abbr,
                    employee_id: $('#employee_dept').val(),
                    // semester_type_id : $('#semester_type_id_search').val()
                }
                
                $.post('<?=base_url()?>employee/submit_employee_department', a_data, function(result) {
                    $.unblockUI();
                    if (result.code != 0) {
                        toastr.warning(result.message, 'Warning!');
                    }
                    else{
                        toastr['success']('Success submit data', 'Success');
                        employee_table.ajax.reload();
                        $('#modal_em_dept').modal('hide');
                    }
                }, 'json').fail(function(a, b, c) {
                    $.unblockUI();
                    toastr.error('Error processing data!', 'Error!');
                });
            }
            else {
                toastr.warning('No selected department!');
            }
        });

        $('#modal_em_dept').on('hidden.bs.modal', function (e) {
            $('#employee_dept').val('');
            table_employee_department.rows().deselect();
        });
    });
</script>