<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body text-justify">
                <h4>
                    Section 2
                    Structure and Written Expression
                </h4><hr>
                <p>
                <strong>Time: 25 minutes</strong> (including the reading of the directions)<br>
                <strong>Now set your clock for 25 minutes.</strong>
                </p>
                <p>This section is designed to measure your ability to recognize language that is appropriate for standard written English. There are two types of questions in this section, with special directions for each type.</p>
                <h5>Structure</h5>
                <p>
                    <strong>Directions:</strong> Questions 1 – 15 are incomplete sentences. Beneath each sentence you will see four words or phrases, marked A, B, C, and D. Choose the one word or phrase that best completes the sentence. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen. Fill in the space so that the letter inside the oval cannot be seen.
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Example I</strong></p>
                        <p>Geysers have often been compared to volcanoes _________ they both emit hot liquids from below the Earth’s surface.</p>
                        <ul class="list-unstyled pl-5">
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1_1" name="example_1" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1_1">A.	due to</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1_2" name="example1_1" value="customEx" checked="checked" disabled>
                                    <label class="custom-control-label" for="ex_1_2">B.	because</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1_3" name="example_1" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1_3">C.	in spite of</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1_4" name="example_1" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1_4">D.	regardless of</label>
                                </div>
                            </li>
                        </ul>
                            <div class="custom-control custom-radio">
                                <span class="pr-5"> Sample Answer:</span>
                                <input type="radio" class="custom-control-input" id="ex_11" name="example11" value="customEx" checked="checked" disabled>
                                <label class="custom-control-label" for="ex_11">B.	because</label>
                            </div>
                        <p>The sentence should read; "Geysers have often been compared to volcanoes because they both emit hot liquids from below the Earth’s surface." there fore, you should choose <strong>B</strong>.</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Example II</strong></p>
                        <p>During the early period of ocean navigation, _________ any need for sophisticated instruments and techniques.</p>
                        <ul class="list-unstyled pl-5">
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2_1" name="example_2" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2_1">A.	so that hardly</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2_2" name="example_2" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2_2">B.	when there hardly was</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2_3" name="example_2" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2_3">C.	hardly was</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2_4" name="example_2" value="customEx" checked="checked" disabled>
                                    <label class="custom-control-label" for="ex_2_4">D.	there was hardly</label>
                                </div>
                            </li>
                        </ul>
                            <div class="custom-control custom-radio">
                                <span class="pr-5"> Sample Answer:</span>
                                <input type="radio" class="custom-control-input" id="ex_21" name="example22" value="customEx" checked="checked" disabled>
                                <label class="custom-control-label" for="ex_21">D.	there was hardly</label>
                            </div>
                        <p>The sentence should read; “During the early period of ocean navigation, there was hardly any need for sophisticated instruments and techniques”. “therefore, you should choose <strong>D</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
<?php
if (count($section_2A) > 0) {
    foreach ($section_2A as $sec_2A) {
?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <p><strong><?=$sec_2A->exam_question_number;?>. <?=$sec_2A->exam_question_description;?></strong></p>
                    <div class="row">
                <?php
                if (count($sec_2A->option) > 0) {
                    $a_option = $sec_2A->option;
                    foreach ($a_option as $opt) {
                ?>
                        <div class="col-md-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="<?=$opt['question_option_id'];?>" name="<?=$sec_2A->exam_question_id;?>" value="<?=$opt['question_option_id'];?>">
                                <label class="custom-control-label" for="<?=$opt['question_option_id'];?>"><?=$opt['exam_question_option_number'];?>. <?=$opt['question_option_description'];?></label>
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
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body text-justify">
                <h4>
                    Section 2
                    Written Expression
                </h4><hr>
                <p>This section is designed to measure your ability to recognize language that is appropriate for standard written English. There are two types of questions in this section, with special directions for each type.</p>
                <h5>Structure</h5>
                <p>
                    <strong>Directions:</strong> Questions 16 – 40 each sentence has four underlined words or phrases. The four underlined parts of the sentence are marked A, B, C, and D. Identify the one underlined word of phrase that must be changed in order for the sentence to be correct. Then, on your answer sheet, find the number of the question and fill in the space that corresponds to the letter of the answer you have chosen.
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Example I</strong></p>
                        <p>Guppies are sometimes <u>call (A)</u> rainbow <u>fish (B)</u> <u>because (C)</u> of the males’ <u>bright (D)</u> colors.</p>
                        <ul class="list-unstyled pl-5">
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1A_1" name="example_1A" value="customEx" checked="checked" disabled>
                                    <label class="custom-control-label" for="ex_1A_1">A.	call</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1A_2" name="example1_1A" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1A_2">B.	fish</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1A_3" name="example_1A" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1A_3">C.	because</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_1A_4" name="example_1A" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_1A_4">D.	bright</label>
                                </div>
                            </li>
                        </ul>
                            <div class="custom-control custom-radio">
                                <span class="pr-5"> Sample Answer:</span>
                                <input type="radio" class="custom-control-input" id="ex_11" name="example11" value="customEx" checked="checked" disabled>
                                <label class="custom-control-label" for="ex_11">A.	call</label>
                            </div>
                        <p>The sentence should read; “Guppies are sometimes called rainbow fish because of the males’ bright colors. “therefore, you should choose <strong>A</strong>.</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Example II</strong></p>
                        <p><u>Serving (A)</u> several <u>term (B)</u> in Congress, Shirley Chrisholm became an <u>important (C)</u> United States <u>politician (D)</u>.</p>
                        <ul class="list-unstyled pl-5">
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2B_1" name="example_2B" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2B_1">A.	Serving</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2B_2" name="example_2B" value="customEx" checked="checked" disabled>
                                    <label class="custom-control-label" for="ex_2B_2">B.	term</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2B_3" name="example_2B" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2B_3">C.	important</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="ex_2B_4" name="example_2B" value="customEx" disabled>
                                    <label class="custom-control-label" for="ex_2B_4">B.	politician</label>
                                </div>
                            </li>
                        </ul>
                            <div class="custom-control custom-radio">
                                <span class="pr-5"> Sample Answer:</span>
                                <input type="radio" class="custom-control-input" id="ex_21" name="example22" value="customEx" checked="checked" disabled>
                                <label class="custom-control-label" for="ex_21">B.	term</label>
                            </div>
                        <p>The sentence should read; “Serving several terms in Congress, Shirley Chisholm become an important United States Political”. “therefore, you should choose <strong>B</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
<?php
if (count($section_2B) > 0) {
    foreach ($section_2B as $sec_2B) {
?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <p><strong><?=$sec_2B->exam_question_number;?>. <?=$sec_2B->exam_question_description;?></strong></p>
                    <div class="row">
                <?php
                if (count($sec_2B->option) > 0) {
                    $a_option = $sec_2B->option;
                    foreach ($a_option as $opt) {
                ?>
                        <div class="col-md-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="<?=$opt['question_option_id'];?>" name="<?=$sec_2B->exam_question_id;?>" value="<?=$opt['question_option_id'];?>">
                                <label class="custom-control-label" for="<?=$opt['question_option_id'];?>"><?=$opt['exam_question_option_number'];?>. <?=$opt['question_option_description'];?></label>
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
<div class="row footer_end">
    <div class="col-md-6 mt-3 mb-3">
        <button type="button" class="btn btn-block btn-primary" id="prev_section"><i class="fas fa-angle-double-left"></i> Prev Section</button>
    </div>
    <div class="col-md-6 mt-3 mb-3">
        <button type="button" class="btn btn-block btn-success" id="submit_quiz"><i class="fas fa-spell-check"></i> Finish</button>
    </div>
</div>
<script>
    // sessionStorage.clear();
    let sec_question_id = JSON.parse('<?= json_encode($question_section_id)?>');
    if (sec_question_id.length > 0) {
        $.each(sec_question_id, function(i, v) {
            $("input[name='" + v + "']").change(function() {
                var value = $("input[name='" + v + "']:checked").val();
                sessionStorage.setItem(v, value);
            });

            if (sessionStorage.getItem(v) != null) {
                $('input[name="' + v + '"]').filter("[value='" + sessionStorage.getItem(v) + "']").click();
                // console.log(sessionStorage.getItem(v));
            }
        });
    }
</script>