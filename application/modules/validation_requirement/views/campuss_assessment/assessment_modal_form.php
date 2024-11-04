<div class="modal" tabindex="-1" role="dialog" id="modal_staff_form_assessment" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assessment Form</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button> -->
            </div>
            <div class="modal-body">
                <p>Mohon meluangkan waktu untuk mengisi penilaian berikut.<br>
                    Identitas anda akan kami jaga kerahasiaannya dalam penilaian ini.</p>
                <form id="form_staff_assessment" url="<?=base_url()?>validation_requirement/university_assessment/submit_assessment" onsubmit="return false">
                    <input type="hidden" name="assessment_id" value="<?=$assessment_id;?>">
                    <h5 class="text-primary">Silakan beri skor untuk setiap pertanyaan: <?= (count($option_list_name) > 0) ? implode(', ', $option_list_name) : '';?></h5>
                    <p></p>
                    <ul class="list-group">
                        <?php
                        if ($list_question) {
                            foreach ($list_question as $question) {
                                $s_question_name = $question->question_name;
                                $s_question_name .= (!empty($question->question_name_eng)) ? '<br><i>'.$question->question_name_eng.'</i>' : '';
                        ?>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-8 required_text">
                                        <?=$question->question_number;?>. <?=$s_question_name;?>
                                    </div>
                                    <div class="col-sm-4">
                                        <!-- <nav class="nav nav-justified"> -->
                                            <?php
                                            if ($list_option) {
                                                foreach ($list_option as $option) {
                                                    $s_option_name = $option->option_name;
                                                    $s_option_name .= (!empty($option->option_name_eng)) ? ' (<i>'.$option->option_name_eng.'</i>)' : '';
                                            ?>
                                            <!-- <span class="nav-item"> -->
                                                <div class="custom-control custom-radio">
                                                    <input class="custom-control-input" type="radio" name="result_question_<?=$question->question_id;?>" id="result_question_<?=$question->question_id;?>_<?=$option->assessment_option_id;?>" value="<?=$option->assessment_option_id;?>">
                                                    <label class="custom-control-label" for="result_question_<?=$question->question_id;?>_<?=$option->assessment_option_id;?>">
                                                        <?=$s_option_name;?>
                                                    </label>
                                                </div>
                                            <!-- </span> -->
                                            <?php
                                                }
                                            }
                                            ?>
                                        <!-- </nav> -->
                                    </div>
                                </div>
                            </li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block" id="btn_submit_assessment_univ">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $('#modal_staff_form_assessment').modal('show');
    $('#btn_submit_assessment_univ').on('click', function(e) {
        e.preventDefault();
        $.blockUI({ baseZ: 2000 });

        var form = $('#form_staff_assessment');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                $('#modal_staff_form_assessment').modal('hide');
                toastr.success("", 'thank you for participating this assessment');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Failed to submit assessment!');
        });
    })
})
</script>