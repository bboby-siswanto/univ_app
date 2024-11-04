<div class="card">
    <div class="card-header">
        University Assessment
    </div>
    <div class="card-body">
        <form id="form_assessment" url="<?=base_url()?>validation_requirement/university_assessment/submit_assessment" onsubmit="return false">
            <input type="hidden" name="assessment_id" value="<?=$assessment_id;?>">
            <h5 class="text-primary">Please score to each question: Excellent, Good, Satisfactory or Poor</h5>
            <p></p>
            <ul class="list-group">
                <?php
                if ($list_question) {
                    foreach ($list_question as $question) {
                ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-sm-6 required_text">
                                <?=$question->question_number;?>. <?=$question->question_name_eng;?>
                            </div>
                            <div class="col-sm-6">
                                <nav class="nav nav-justified">
                                    <?php
                                    if ($list_option) {
                                        foreach ($list_option as $option) {
                                    ?>
                                    <span class="nav-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="result_question_<?=$question->question_id;?>" id="result_question_<?=$question->question_id;?>_<?=$option->assessment_option_id;?>" value="<?=$option->assessment_option_id;?>">
                                            <label class="form-check-label" for="result_question_<?=$question->question_id;?>_<?=$option->assessment_option_id;?>">
                                                <?=$option->option_name_eng;?>
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
            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-info btn-block" id="btn_submit_assessment" name="btn_submit_assessment">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(function() {
    $('#btn_submit_assessment').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_assessment');
        var data = form.serialize();
        var url = form.attr('url');

        $.post(url, data, function(result) {
            if (result.code == 0) {
                toastr.success("", 'thank you for participating');
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            toastr.error('Failed to submit assessment!');
        });
    })
})
</script>