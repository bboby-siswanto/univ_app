<div class="card">
    <div class="card-header">
        Assessment Result
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-4">Period</div>
                    <div class="col-8">: <?=$period;?></div>
                </div>
                <div class="row">
                    <div class="col-4">Lecturer</div>
                    <div class="col-8">: <?=$lecturer_name;?></div>
                </div>
                <div class="row">
                    <div class="col-4">Total Respondent</div>
                    <div class="col-8">: <?=$total_respondent;?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-4">Subject</div>
                    <div class="col-8">: <?=$subject_name;?></div>
                </div>
                <div class="row">
                    <div class="col-4">Study Program</div>
                    <div class="col-8">: <?=$study_program;?></div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <h6>Scores:</h6>
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-7">Question</div>
                            <div class="col-5">: Total Score</div>
                        </div>
                    </li>
                <?php
                $d_total_score = 0;
                $d_total_question = 0;
                if ($question_list) {
                    foreach ($question_list as $o_question_aspect) {
                        $d_total_score += $o_question_aspect->result_assessment;
                        $d_total_question++;
                ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-7"><?=$o_question_aspect->number;?>. <?=$o_question_aspect->question_desc;?></div>
                            <div class="col-5">: <strong><?= $o_question_aspect->result_assessment;?></strong></div>
                        </div>
                    </li>
                <?php
                    }
                }
                ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-7"><strong>Total Score</strong></div>
                            <div class="col-5"><strong>: <?=$d_total_score;?></strong></div>
                        </div>
                    </li>
                <?php
                $d_average = $d_total_score / $d_total_question / $total_respondent;
                $s_grade = $this->grades->lecturer_assessment_grade($d_average);
                ?>
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-7"><strong>Assessment Score</strong></div>
                            <div class="col-5"><strong>: <?=$d_average;?> / <?=$s_grade;?></strong></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <h6>Comment for improvement:</h6>
                <ul class="list-group">
                <?php
                if (count($result_comment) > 0) {
                    $i_numb = 1;
                    foreach ($result_comment as $s_comment) {
                    ?>
                    <li class="list-group-item"><?=$i_numb++;?>. <i><?=$s_comment;?></i></li>
                    <?php
                    }
                }
                else {
                ?>
                    <li class="list-group-item">No comment available.</li>
                <?php
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
</div>