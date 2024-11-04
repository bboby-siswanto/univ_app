<div class="modal" tabindex="-1" role="dialog" id="modal_assesment" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Lecturer Assesment</h5><br>
            </div>
            <div class="modal-body">
                <p>Please take a moment to complete the assessment of the lecturer.</p>
                <div class="table-repsonsive">
                    <table id="list_lecturer_subject" class="table table-hover">
                        <thead class="bg-dark">
                            <tr>
                                <th>Lecturer</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div> -->
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_form_question">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Lecturer Assesment
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <h4 class="text-primary">Subject: <span id="subject_assessment"></span></h4>
            <h4 class="text-primary">Lecturer: <span id="lecturer_assessment"></span></h4>
            <form id="form_submit_assessment" onsubmit="return false" url="<?=base_url()?>validation_requirement/lecturer_assesment/submit_assessment" class="mt-2">
                <input type="hidden" name="score_id_assessment" id="score_id_assessment">
                <input type="hidden" name="employee_id_assessment" id="employee_id_assessment">
                <h5 class="text-primary">Please score to each question: Excellent, Good, Satisfactory, Poor or Fail</h5>
                <p></p>
                <ul class="list-group">
                    <?php
                    if ($question_list) {
                        foreach ($question_list as $question) {
                    ?>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-sm-6 required_text">
                                    <?=$question->number;?>. <?=$question->question_desc;?>
                                </div>
                                <div class="col-sm-6">
                                    <nav class="nav nav-justified">
                                        <?php
                                        if ($score_option) {
                                            foreach ($score_option as $option) {
                                        ?>
                                        <span class="nav-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="result_question_<?=$question->question_id;?>" id="result_question_<?=$question->question_id;?>_<?=$option->score_result_id;?>" value="<?=$option->score_result_id;?>">
                                                <label class="form-check-label" for="result_question_<?=$question->question_id;?>_<?=$option->score_result_id;?>">
                                                    <?=$option->score_name;?>
                                                </label>
                                            </div>
                                        </span>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </nav>
                                </div>
                            </div>
                        </li>
                    <?php
                        }
                    }
                    ?>
                </ul>
                <h5 class="text-primary mt-3 pb-2">Write you comment for improvement</h5>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-12">
                                Comment
                                <textarea name="result_comment" id="result_comment" class="form-control"></textarea>
                            </div>
                        </div>
                    </li>
                </ul>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn_submit_assessment">Submit</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>
<script>
$('#modal_assesment').modal('show');

$(function() {
    var list_lecturer_subject = $('table#list_lecturer_subject').DataTable({
        ordering: false,
        searching: false,
        info: false,
        paging: false,
        processing: true,
        ajax: {
            url: '<?=base_url()?>validation_requirement/lecturer_assesment/get_lecturer_subject',
            type: 'POST',
            data: function(d){
                d.student_id = '<?=$student_id;?>';
            },
        },
        columns: [
            {data: 'lecturer_name'},
            {data: 'subject_name'},
            {
                data: 'has_submited',
                render: function(data, type, row) {
                    return (data) ? '<span class="badge badge-success">Submitted</span>' : '';
                }
            },
            {
                data: 'score_id',
                render: function(data, type, row) {
                    var html = '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">';
                    html += '<button type="button" class="btn btn-info" id="btn_show_assesment"><i class="fas fa-file-alt"></i> Open Assesment Form</button>';
                    html += '</div>';
                    return html;
                }
            },
        ]
    });
    
    $('table#list_lecturer_subject tbody').on('click', 'button#btn_show_assesment', function(params) {
        var data_list = list_lecturer_subject.row($(this).parents('tr')).data();
        $('#employee_id_assessment').val(data_list.employee_id);
        $('#score_id_assessment').val(data_list.score_id);

        $('#subject_assessment').html(data_list.subject_name);
        $('#lecturer_assessment').html(data_list.lecturer_name);
        $('#modal_form_question').modal('show');
    });

    $('#modal_form_question').on('hidden.bs.modal', function () {
        $('#modal_assesment').modal('hide');
        list_lecturer_subject.ajax.reload();

        $('#result_comment').val('');
        $('#employee_id_assessment').val('');
        $('#score_id_assessment').val('');
        $("#form_submit_assessment input:radio").checked = false;
        $("input[type=radio]").prop('checked', false);

        $('#modal_assesment').modal('show');
    });

    $('button#btn_submit_assessment').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });
        
        var form = $('#form_submit_assessment');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success', 'Succes!');
                setInterval(function () {location.reload();}, 3000);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!, please contact IT Dept.');
        });
    });
})
</script>