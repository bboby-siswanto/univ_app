<div class="table-responsive">
    <table id="curriculum_transfer_credit" class="table table-bordered table-striped table-hover">
        <thead class="bg-dark">
            <tr>
                <th>Subject Code</th>
                <th>Subject Name</th>
                <th>Credit (SKS)</th>
                <th>Study Program</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div id="modal_input_score_credit" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Score</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_score_transfer_credit" onsubmit="return false">
                    <input type="hidden" name="student_id" id="student_id" value="<?= $student_id?>">
                    <input type="hidden" name="curriculum_subject_id" id="curriculum_subject_id">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="form-group">
                                <label>Origin Subject Code</label>
                                <input type="text" id="transfer_subject_code" name="transfer_subject_code" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <label class="required_text">Origin Subject Name</label>
                                <input type="text" id="transfer_subject_name" name="transfer_subject_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="required_text">Origin Credit</label>
                                <input type="number" id="transfer_credit" name="transfer_credit" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="required_text">Converted Score</label>
                                <input type="number" id="transfer_score" name="transfer_score" class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_score_transfer">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var table_curriculum_transfer = $('table#curriculum_transfer_credit').DataTable({
    ajax: {
        url: '<?= base_url()?>academic/transfer_credit/get_subject_curriculum',
        type: 'POST',
        data: {student_id: '<?= $student_id;?>'}
    },
    columns: [
        {
            data: 'subject_code',
            render: function(data, type, row) {
                return data + '<input type="hidden" value="' + row['curriculum_subject_id'] + '">';
            }
        },
        {data: 'subject_name'},
        {data: 'curriculum_subject_credit'},
        {data: 'study_program_abbreviation'},
        {
            data: 'curriculum_subject_id',
            render: function(data, type, row) {
                var btn_transfer = '<button id="btn_transfer_credit_student" name="btn_transfer_credit_student" type="button" class="btn btn-info btn-sm" title="Transfer Subject"><i class="fas fa-long-arrow-alt-right"></i></button>';
                return btn_transfer;
            }
        }
    ]
});
$(function(){
    //modal_input_score_credit
    $('table#curriculum_transfer_credit tbody').on('click', 'button[name="btn_transfer_credit_student"]', function(e) {
        e.preventDefault();
        var row_data = table_curriculum_transfer.row($(this).parents('tr')).data();
        // console.log(row_data);
        $('input#curriculum_subject_id').val(row_data.curriculum_subject_id);
        $('input#transfer_subject_code').val(row_data.subject_code);
        $('input#transfer_subject_name').val(row_data.subject_name);
        $('input#transfer_credit').val(row_data.curriculum_subject_credit);
        $('div#modal_input_score_credit').modal('show');
    });

    $('button#btn_save_score_transfer').on('click', function(e) {
        e.preventDefault();
        var data = $('form#form_score_transfer_credit').serialize();
        url = '<?= base_url()?>academic/transfer_credit/save_score';
        $.blockUI({ baseZ: 2000 });
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Success!');
                $('div#modal_input_score_credit').modal('hide');
                table_subject_transfer.ajax.reload();

                $('#curriculum_subject_id').val('');
                $('#transfer_subject_code').val('');
                $('#transfer_subject_name').val('');
                $('#transfer_credit').val('');
                $('#transfer_score').val('');
            }else{
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
        });
    });
});
</script>