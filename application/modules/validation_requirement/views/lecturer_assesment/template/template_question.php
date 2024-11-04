<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .text-primary {color: #20a8d8 !important;}
        h4 {font-size: 1.3125rem;}
        .list-group {display: -ms-flexbox;display: flex;-ms-flex-direction: column;flex-direction: column;padding-left: 0;margin-bottom: 0;list-style-type: none;}
        .list-group-item:first-child {border-top-left-radius: 0.25rem;border-top-right-radius: 0.25rem;}
        .list-group-item {position: relative;display: block;padding: 0.75rem 1.25rem;margin-bottom: -1px;background-color: #fff;border: 1px solid rgba(0, 0, 0, 0.125);}
        .row {display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;margin-right: -15px;margin-left: -15px;}
        .col-6 {-ms-flex: 0 0 50%;flex: 0 0 50%;max-width: 50%;}
        .col-12 {-ms-flex: 0 0 100%;flex: 0 0 100%;max-width: 100%;}
        .nav {display: -ms-flexbox;display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;padding-left: 0;margin-bottom: 0;list-style: none;}
        .nav-justified .nav-item {-ms-flex-preferred-size: 0;flex-basis: 0;-ms-flex-positive: 1;flex-grow: 1;text-align: center;}
        .form-check {position: relative;display: block;padding-left: 1.25rem;}
        .form-control {height: calc(1.8048438rem + 2px);padding: 0.25rem 0.5rem;font-size: 0.765625rem;font-weight: 400;line-height: 1.5;color: #5c6873;background-color: #fff;background-clip: padding-box;border: 1px solid #e4e7ea;border-radius: 0.2rem;transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;display: block;width: 100%;}
        .btn {display: inline-block;font-weight: 400;color: #23282c;text-align: center;vertical-align: middle;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-color: transparent;border: 1px solid transparent;border-top-color: transparent;border-right-color: transparent;border-bottom-color: transparent;border-left-color: transparent;padding: 0.375rem 0.75rem;font-size: 0.875rem;line-height: 1.5;border-radius: 0.25rem;transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;}
        .btn-primary {color: #fff;background-color: #20a8d8;border-color: #20a8d8;}
    </style>
</head>
<body>
<h4 class="text-primary">Subject: <span id="subject_assessment"></span></h4>
<h4 class="text-primary">Lecturer: <span id="lecturer_assessment"></span></h4>
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
                <div class="required_text" style="width: 100%">
                    <?=$question->number;?>. <?=$question->question_desc;?>
                <!-- </div> -->
                <!-- <div class="col-6"> -->
                    <div style="float:right">
                        <?php
                        if ($score_option) {
                            foreach ($score_option as $option) {
                        ?>
                        <!-- <span class="nav-item">
                            <div class="form-check"> -->
                                <input class="form-check-input" type="radio" name="result_question_<?=$question->question_id;?>" id="result_question_<?=$question->question_id;?>_<?=$option->score_result_id;?>" value="<?=$option->score_result_id;?>">
                                <label class="form-check-label" for="result_question_<?=$question->question_id;?>_<?=$option->score_result_id;?>">
                                    <?=$option->score_name;?>
                                </label>
                            <!-- </div>
                        </span> -->
                        <?php
                            }
                        }
                        ?>
                    </div>
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
            <div class="col-12">
                Comment <br>
                <!-- <textarea name="result_comment" id="result_comment" class="form-control" width="100px" height="50px"></textarea> -->
                <!-- <textarea name="" id="" cols="30" rows="10"></textarea> -->
                <input type="text" style="width: 100%; height: 150px !important;">
            </div>
        </div>
    </li>
</ul>
<hr>
<!-- <button type="button" class="btn btn-primary" id="btn_submit_assessment">Submit</button> -->
<input type="submit" class="btn btn-primary" style="float: right" value="Submit">
</body>
</html>