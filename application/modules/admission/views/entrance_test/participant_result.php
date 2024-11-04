<?php
    if ($result) {
?>
<div class="card">
    <div class="card-header">
        <?=$participant_data->personal_data_name;?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div><label>Start Time: </label><span> <?=$participant_data->start_time;?></span></div>
                <div><label>End Time: </label><span> <?=$participant_data->end_time;?></span></div>
                <div><label>Processing Time: </label><span> <?=$participant_data->total_time;?></span></div>
                <div><label>Total Correct Answer: </label><span> <?=$participant_data->correct_answer;?></span></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
        <?php
        if ($result_section) {
            foreach ($result_section as $section) {
        ?>
                <div><label><strong><?=$section->exam_section_name;?></strong></label></div>
                <div><label>Correct Answer: <span><?=$section->correct_answer;?></span></label></div>
        <?php
            }
        }
        ?>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Entrance Test Result
    </div>
    <div class="card-body">
        <h5>Section 1 Listening Comprehension</h5>
        <strong>PART A</strong>
        <div class="row">
    <?php
        $i= 0;
        
        $max_num_row = (count($result_sess_1)/2) + 1;
        $c_idx = 0;
        for ($z=0; $z < 2; $z++) { 
            print('<div class="col-md-6">');
            foreach ($result_sess_1 as $idx => $res_sess_1) {
                if (($c_idx == $idx) AND ($res_sess_1->exam_question_number <= $max_num_row)) {
                    $c_idx++;
                    $o_answer = $res_sess_1->o_answer;

                    if ($res_sess_1->exam_question_number == 30) {
                        echo '<div><strong>PART B</strong></div>';
                    }else if ($res_sess_1->exam_question_number == 38) {
                        echo '<div><strong>PART C</strong></div>';
                    }
                    print ($res_sess_1->exam_question_number.'.) ');
                    if ($o_answer != 'false') {
                ?>
                        <?=$o_answer->exam_question_option_number;?>. <?=$o_answer->question_option_description;?>
                <?php
                        if ($o_answer->option_this_answer == 'TRUE') {
                            echo ' <i class="fas fa-check-circle"></i>';
                        }
                    }

                    echo '<br>';
                }
            }
            $max_num_row +=$max_num_row;
            print('</div>');
        }
    ?>
        </div>
        <h5>Section 2</h5>
        <div class="row">
    <?php
        $result_sess_2 = array_merge($result_sess_2A, $result_sess_2B);
        $result_sess_2 = array_values($result_sess_2);
        // print('<pre>');var_dump($result_sess_2);exit;

        $max_num_row = (count($result_sess_2)/2) + 1;
        $c_idx = 0;
        for ($z=0; $z < 2; $z++) { 
            print('<div class="col-md-6">');
            foreach ($result_sess_2 as $idx => $res_sess_2A) {
                if (($c_idx == $idx) AND ($res_sess_2A->exam_question_number <= $max_num_row)) {
                    $c_idx++;
                    $o_answer = $res_sess_2A->o_answer;

                    if ($res_sess_2A->exam_question_number == 30) {
                        echo '<div><strong>PART B</strong></div>';
                    }else if ($res_sess_2A->exam_question_number == 38) {
                        echo '<div><strong>PART C</strong></div>';
                    }
                    print ($res_sess_2A->exam_question_number.'.) ');
                    if ($o_answer != 'false') {
                ?>
                        <?=$o_answer->exam_question_option_number;?>. <?=$o_answer->question_option_description;?>
                <?php
                        if ($o_answer->option_this_answer == 'TRUE') {
                            echo ' <i class="fas fa-check-circle"></i>';
                        }
                    }

                    echo '<br>';
                }
            }
            $max_num_row +=$max_num_row;
            print('</div>');
        }
    ?>
        </div>
        <h5>Section 3. Mathematics</h5>
        <div class="row">
    <?php
        $i= 0;
        
        $max_num_row = (count($result_sess_3)/2) + 1;
        $c_idx = 0;
        for ($z=0; $z < 2; $z++) { 
            print('<div class="col-md-6">');
            foreach ($result_sess_3 as $idx => $res_sess_3) {
                if (($c_idx == $idx) AND ($res_sess_3->exam_question_number <= $max_num_row)) {
                    $c_idx++;
                    $o_answer = $res_sess_3->o_answer;

                    print ($res_sess_3->exam_question_number.'.) ');
                    if ($o_answer != 'false') {
                        $s_answer_desc = $this->Etm->clean_html($o_answer->exam_question_option_number, $o_answer->question_option_description);
                        $s_answer_desc = str_replace('<p>', '', $s_answer_desc);
                        $s_answer_desc = str_replace('</p>', '', $s_answer_desc);
                ?>
                        <?=$s_answer_desc;?>
                <?php
                        if ($o_answer->option_this_answer == 'TRUE') {
                            echo ' <i class="fas fa-check-circle"></i>';
                        }
                    }

                    echo '<br>';
                }
            }
            $max_num_row +=$max_num_row;
            print('</div>');
        }
    ?>
        </div>
    </div>
</div>
<?php
    }else{
?>
    <div class="card">
        <div class="card-body">
            <h4>Participant hasn't filled in an answer</h4>
        </div>
    </div>
<?php
    }
?>