<h3 style="margin-top: 20px;">Lecturer Assessment Result</h3>
<table style="width: 100%;">
    <tr>
        <td style="width: 50%;" valign="top">
            <table>
                <tr>
                    <td  valign="top">Period</td>
                    <td valign="top">:</td>
                    <td valign="top"><?=$period;?></td>
                </tr>
                <tr>
                    <td valign="top">Lecturer</td>
                    <td valign="top">:</td>
                    <td valign="top"><?=$lecturer_name;?></td>
                </tr>
                <tr>
                    <td valign="top">Total Respondent</td>
                    <td valign="top">:</td>
                    <td valign="top"><?=$total_respondent;?></td>
                </tr>
            </table>
        </td>
        <td style="width: 50%;" valign="top">
            <table>
                <tr>
                    <td valign="top">Subject</td>
                    <td valign="top">:</td>
                    <td valign="top"><?=$subject_name;?></td>
                </tr>
                <tr>
                    <td valign="top">Study Program</td>
                    <td valign="top">:</td>
                    <td valign="top"><?=$study_program;?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<hr>
<div class="row">
    <div class="col-12">
        <p><b>Scores</b></p>
    </div>
    <div class="col-md-7">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;" valign="top">
                    <span>Score to the question:</span>
                    <table style="width: 100%; border-collapse: collapse;" border="1">
                        <tr>
                            <th rowspan="2">No.</th>
                            <th colspan="<?=($question_list) ? count($question_list) : 1?>">Question Number</th>
                            <th rowspan="2">Total</th>
                        </tr>
                        <tr>
                    <?php
                    if ($question_list) {
                        foreach ($question_list as $o_question) {
                    ?>
                            <th><?=$o_question->number;?></th>
                    <?php
                        }
                    }
                    else {
                        echo "<th></th>";
                    }
                    ?>
                        </tr>
                        <?php
                        $answer_question = [];
                        $d_total = 0;
                        if ($assessment_result) {
                            $i_no = 1;
                            foreach ($assessment_result as $o_result) {
                                $d_total_result = 0;
                        ?>
                            <tr>
                                <td align="center"><?=$i_no++;?></td>
                            <?php
                                if ($o_result->question_details) {
                                    foreach ($o_result->question_details as $o_question) {
                                        if (array_key_exists($o_question->number, $answer_question)) {
                                            array_push($answer_question[$o_question->number], $o_question->value_question_answer);
                                        }
                                        else {
                                            $answer_question[$o_question->number] = [$o_question->value_question_answer];
                                        }
                                        $d_total_result += $o_question->value_question_answer;
                            ?>
                                <td align="center"><?=$o_question->value_question_answer;?></td>
                            <?php
                                    }
                                }
                            ?>
                                <td align="center"><?=$d_total_result;?></td>
                            </tr>
                        <?php
                                $d_total += $d_total_result;
                            }
                        }
                        ?>
                        <tr>
                        <td><b>Total</b></td>
                        <?php
                    if ($question_list) {
                        foreach ($question_list as $o_question) {
                    ?>
                            <td align="center"><b>
                        <?php
                            if (array_key_exists($o_question->number, $answer_question)) {
                                echo array_sum($answer_question[$o_question->number]);
                            }
                        ?>
                            </b></td>
                    <?php
                        }
                    }
                    else {
                        echo "<td></td>";
                    }
                    $result_assessment = ($question_list) ? $d_total/count($question_list)/$total_respondent : 0;
                    $d_result_assessment = number_format($result_assessment, 2);
                    $s_grade = $this->grades->lecturer_assessment_grade($d_result_assessment);
                    ?>
                            <td align="center"><b><?=$d_total;?></b></td>
                        </tr>
                    </table>
                    <p style="margin-top: 20px;">
                        <strong>
                            Assessment Score
                            <span class="mt-3 pl-1 h4"> <?=$d_result_assessment;?> / <?=$s_grade;?></span>
                        </strong>
                    </p>
                </td>
                <td></td>
                <td style="width: 38%;" valign="top">
                    <span>Question List:</span>
                    <table style="width: 100%;">
                        <tr>
                            <td><b>No</b></td>
                            <td><b>Question</b></td>
                        </tr>
                        <?php
                        if ($question_list) {
                            foreach ($question_list as $o_question) {
                        ?>
                            <tr>
                                <td><?=$o_question->number;?>.</td>
                                <td><?=$o_question->question_desc;?></td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mt-5">
    <div class="col-12">
        <p><b>Comment for improvement</b></p>
        <ol>
        <?php
        if (count($result_comment) > 0) {
            foreach ($result_comment as $s_comment) {
            ?>
            <li><i><?=$s_comment;?></i></li>
            <?php
            }
        }
        else {
        ?>
            <li>No comment available.</li>
        <?php
        }
        ?>
        </ol>
    </div>
</div>