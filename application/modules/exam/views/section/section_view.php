<?php
if ($section) {
    $question_section_id = array();
    $sect = $section;
    $sound_media = $sect->media;
    if ($sound_media) {
?>
<div class="row fixed-top" style="margin-top: 70px;">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <audio controls class="form-control" autoplay>
                    <source src="<?= $sound_media['url'];?>" type="audio/mpeg">
                </audio> 
                <small class="text-danger">Click the PLAY button above for the audio.</small>
            </div>
        </div>
    </div>
</div>
<?php
}
    if ($sect->description != '') {
?>
<div class="row" <?= ($sound_media) ? 'style="margin-top: 100px;"' : '' ?>>
<div class="col">
    <div class="card">
        <div class="card-body text-justify">
            <?= $sect->description;?>
        </div>
    </div>
</div>
</div>
<?php
    }
    $a_part = $sect->part;
    if ($a_part) {
        foreach ($a_part as $part) {
            if ($part->example != '') {
?>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body text-justify">
                                <?= $part->example;?>
                            </div>
                        </div>
                    </div>
                </div>
<?php
            }
?>
<div class="row">
<?php
            $a_question = $part->question;
            if ($a_question) {
                foreach ($a_question as $question) {
                    if (!in_array($question->exam_question_id, $question_section_id)) {
                        array_push($question_section_id, $question->exam_question_id);
                    }
                    $question_desc = $this->Etm->clean_html($question->exam_question_number, $question->exam_question_description);
?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p><strong><?=$question_desc;?></strong></p>
                <div class="row">
    <?php
                    $a_option = $question->option;
                    if ($a_option) {
                        foreach ($a_option as $opt) {
                            $option_desc = $this->Etm->clean_html($opt['exam_question_option_number'], $opt['question_option_description']);
    ?>
                    <div class="col-md-6">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="<?=$opt['question_option_id'];?>" name="<?=$question->exam_question_id;?>" value="<?=$opt['question_option_id'];?>">
                            <label class="custom-control-label" for="<?=$opt['question_option_id'];?>"><?=$option_desc;?></label>
                        </div>
                    </div>
    <?php
                        }
                    }
    ?>
                </div>
            </div>
        </div>
    </div>
<?php
                }
            }
?>
</div>
<?php
        }
    }
?>
<button id="submit_this_section" class="btn btn-block btn-success mb-5">Finish Section <?=$sect->exam_section_id;?></button>
<!-- <div class="countdown">
    <div class="timer">
        <div>
            <span class="hours" id="hour"></span> 
            <div class="smalltext">Hours</div>
        </div>
        <div>
            <span class="minutes" id="minute"></span> 
            <div class="smalltext">Minutes</div>
        </div>
        <div>
            <span class="seconds" id="second"></span> 
            <div class="smalltext">Seconds</div>
        </div>
        <p id="time-up"></p>
    </div>
</div> -->
<script>
    let sec_question_a_id = JSON.parse('<?= json_encode($question_section_id)?>');
    if (sec_question_a_id.length > 0) {
        $.each(sec_question_a_id, function(i, v) {
            $("input[name='" + v + "']").change(function() {
                var value = $("input[name='" + v + "']:checked").val();
                sessionStorage.setItem(v, value);
            });

            $("input[name='" + v + "']").on('click', function() {
                var value = $("input[name='" + v + "']:checked").val();
                submit_one_answer(v, value);
            });

            if (sessionStorage.getItem(v) != null) {
                $('input[name="' + v + '"]').filter("[value='" + sessionStorage.getItem(v) + "']").click();
                // console.log(sessionStorage.getItem(v));
            }
        });
    }

    function submit_one_answer(question_id, question_option_id) {
        var exam_candidate_id = '<?= $exam_candidate_id;?>';

        var data = {
            exam_candidate_id: exam_candidate_id,
            question_id: question_id,
            question_option_id: question_option_id
        }
        // console.log(question_option_id);
        $.post('<?=base_url()?>exam/entrance_test/save_exam_option_answer', data, function (result) {
            console.log(result);
        }, 'json');
    }
</script>
<?php
}
?>