<div class="table-responsive">
    <table id="lists_ofse_member" class="table table-bordered table-hover table-striped">
        <thead class="bg-dark">
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Study Program</th>
                <th>Final Score</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div id="modal_input_score" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">OFSE Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= modules::run('academic/ofse/form_input_score');?>
            </div>
            <div class="modal-footer">
                <button id="submit_ofse_score" type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        var table_lists_ofse_member = $('table#lists_ofse_member').DataTable({
            paging: false,
            bInfo: false,
            ajax : {
                url : '<?= base_url()?>academic/ofse/get_member_details',
                type : 'POST',
                data : {
                    class_group_id: '<?= $class_group_id?>'
                }
            },
            columns: [
                {data: 'student_number'},
                {data: 'personal_data_name'},
                {data: 'study_program_abbreviation'},
                {data: 'score_final_exam'},
                {data: 'score_grade'},
                {data: 'score_id'}
            ],
            columnDefs: [
                {
                    targets: -1,
                    orderable: false,
                    render: function(data, type, row) {
                        var html = '<div class="btn-group" role="group" aria-label="">';
                        html += '<?= $btn_html;?>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });

        $('button#submit_ofse_score').on('click', function(e) {
            e.preventDefault();
            $.blockUI({baseZ: 2000});

            let form = $('form#ofse_input_score');
            var data = form.serialize();

            $.post('<?=base_url()?>academic/ofse/save_ofse_score', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    toastr['success']('Success save data', 'Success');
                    $('div#modal_input_score').modal('hide');
                    table_lists_ofse_member.ajax.reload(null, false);
                }else{
                    toastr['warning'](result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr['error']('Error saving data', 'Error');
            })
        });

        $('table#lists_ofse_member tbody').on('click', 'button[name="btn_input_score_ofse"]', function(e) {
            e.preventDefault();
            
            var score_data = table_lists_ofse_member.row($(this).parents('tr')).data();
            try {
                var s_score_examiner = score_data.score_examiner;
                var o_score_examiner = JSON.parse(score_data.score_examiner);
                var s_score_examiner_1 = o_score_examiner.score_examiner_1;
                var s_score_examiner_2 = o_score_examiner.score_examiner_2;
                
                $('input#ofse_score_1').val(s_score_examiner_1);
                $('input#ofse_score_2').val(s_score_examiner_2);
            } catch (error) {}

            $('input#score_id').val(score_data.score_id);
            $('div#modal_input_score').modal('show');
        });
    });
</script>