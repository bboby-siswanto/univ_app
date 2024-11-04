<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                Repeat Score (<?=$academic_year_id;?> / <?=$semester_type_name;?>)
            </div>
            <div class="card-body">
                <div class="table-responsive" id="score-student-view">
                    <div class="table-responsive">
                        <table id="table_score_list" class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Quiz</th>
                                    <th>Final Exam</th>
                                    <th>Final Score</th>
                                    <th>Absence</th>
                                    <th>Grade Point</th>
                                    <th>Credit / SKS</th>
                                    <th>Merit</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <p>Payment must be fulfilled 3 days prior to the examination date.</p>
            </div>
            <div class="card-footer">
                <button type="button" id="btn_submit_repeat" class="btn btn-info">Register these subjects for repetition</button>
            </div>
        </div>
    </div>
</div>
<div id="confirmation_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Repetition Registration Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <p>You have registered <span id="number_total_subject"></span> subject for repeatition</p>
                    <table class="table" id="confirmation_table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Subject Credit</th>
                                <th>Fee</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total Fee</strong></td>
                                <td><strong id="total_fee_repeat">0</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="process_registration" class="btn btn-primary">Proceed</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var a_score_data_id = [];
    $(function() {
        dt_table = $('table#table_score_list').DataTable({
            searching: false,
            info: false,
            paging: false,
            processing: true,
            ordering: false,
            ajax: {
                url: '<?= base_url()?>academic/score/filter_score_student',
                type: 'POST',
                data: function(d){
                    d.student_id = '<?= $student_id;?>';
                    d.academic_year_id = '<?=$academic_year_id;?>';
                    d.semester_type_id = '<?=$semester_type_id;?>';
                    d.curriculum_subject_type = 'all';
                }
            },
            columns: [
                {
                    data: 'score_id',
                    className: 'select-checkbox',
                    render: function(data, type, row) {
                        return '';
                    }
                },
                {
                    data: 'subject_code',
                    render: function ( data, type, row, meta ) {
                        return data + '<span class="d-none">' + row.score_id + '</span>';
                    }
                },
                {
                    data: 'subject_name',
                    // render: function(data, type, row) {
                    //     return data + ' <br>(' + row.lecturer_class + ')';
                    // }
                },
                {data: 'score_quiz'},
                {
                    data: 'score_final_exam',
                    render: function ( data, type, row, meta ) {
                        return Number(data).toFixed(2);
                    }
                },
                {data: 'score_sum', render: function(data, type, row) {
                    return Number(data).toFixed(2);
                }},
                {
                    data: 'score_absence',
                    render: function ( data, type, row, meta ) {
                        return Number(data).toFixed(2) + ' %';
                    }
                },
                {data: 'score_grade_point'},
                {
                    data: 'curriculum_subject_credit'
                },
                {data: 'score_merit'},
                {data: 'score_grade'},
            ],
            createdRow: function( row, data, dataIndex){
                if ((data.score_grade == 'F') || (data.score_grade == 'D')) {
                    $(row).addClass('bad-grade');
                }
                
                // if(data.score_absence > 25){
	            //     // $(row).addClass('bad-grade');
                //     var td = row.children[0];
                //     $(td).removeClass('select-checkbox');
                // }
            },
            select: {
                style:    'multi',
                selector: 'td.select-checkbox'
            },
        });

        console.log(a_score_data_id);

        var dt_table_confirmation = $('table#confirmation_table').DataTable({
            searching: false,
            info: false,
            paging: false,
            processing: true,
            ordering: false,
            ajax: {
                url: '<?= base_url()?>academic/score/get_score_list_by_id',
                type: 'POST',
                // data: {a_score_id: a_score_data_id},
                data: function(d){
                    if (a_score_data_id.length == 0) {
                        d.a_score_id = false
                    }else{
                        d.a_score_id = a_score_data_id
                    }
                }
            },
            columns: [
                {
                    data: 'subject_code',
                    render: function ( data, type, row, meta ) {
                        return data + '<span class="d-none">' + row.score_id + '</span>';
                    }
                },
                {
                    data: 'subject_name',
                    // render: function(data, type, row) {
                    //     return data + ' <br>(' + row.lecturer_class + ')';
                    // }
                },
                { data: 'curriculum_subject_credit' },
                {
                    data: 'score_grade',
                    render: function(data, type, row) {
                        return '400.000 ,-';
                    }
                },
            ]
        });

        $('button#process_registration').on('click', function(e) {
            e.preventDefault();

            let data = {
                student_id: '<?=$student_id;?>',
                score_id: a_score_data_id
            }

            $.blockUI({ baseZ: 2000 });

            $.post('<?=base_url()?>student/repeat/registration', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr.success('Success!', 'Success');
                    setTimeout(function(){
						window.location.href = '<?=base_url()?>';
					}, 3000);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error submiting your data!');
            });
        });

        $('button#btn_submit_repeat').on('click', function(e) {
            e.preventDefault();

            var checked = dt_table.rows( { selected: true } );
            
            a_score_data_id = [];
            if (checked.count() > 0) {
                // $.blockUI();
                var count = 0;
                
                $.each(checked.data(), function(i, v) {
                    a_score_data_id.push(v.score_id);
                    count++;
                });

                var total_fee = count * 400000;
                $('#number_total_subject').html(count);
                $('#total_fee_repeat').html('Rp. ' + number_format(total_fee, 0, ',', '.') + ",-");
                
                dt_table_confirmation.ajax.reload();
                $('div#confirmation_modal').modal('show');
            }else{
                toastr.warning('Please select one or more subject!', 'Warning!');
            }
        });
    });

    number_format = function (number, decimals, dec_point, thousands_sep) {
        number = number.toFixed(decimals);

        var nstr = number.toString();
        nstr += '';
        x = nstr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? dec_point + x[1] : '';
        var rgx = /(\d+)(\d{3})/;

        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

        return x1 + x2;
    }
</script>