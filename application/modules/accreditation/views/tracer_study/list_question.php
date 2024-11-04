<div class="card">
    <div class="card-header">
        Filter Data
    </div>
    <div class="card-body">
        <form id="form_filter_accreditation" onsubmit="return false">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="academic_year">Batch</label>
                        <select name="academic_year" id="academic_year" class="form-control">
                            <option value=""></option>
                            <option value="all" selected="selected">All</option>
                <?php
                if ($batch) {
                    foreach ($batch as $o_year) {
                ?>
                            <option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="graduation_year">Graduation Year</label>
                        <select class="form-control selectpicker" name="graduation_year[]" id="graduation_year"  multiple data-live-search="true" data-actions-box="true">
                        <!-- <select name="graduation_year" id="graduation_year" class="form-control"> -->
                            <!-- <option value=""></option> -->
                            <!-- <option value="all" selected="selected">All</option> -->
                <?php
                if ($batch) {
                    foreach ($batch as $o_year) {
                ?>
                            <option value="<?=$o_year->academic_year_id;?>"><?=$o_year->academic_year_id;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="study_program">Study Program</label>
                        <select name="study_program" id="study_program" class="form-control">
                            <option value=""></option>
                            <option value="all" selected="selected">All</option>
                <?php
                if ($study_program) {
                    foreach ($study_program as $o_prodi) {
                ?>
                            <option value="<?=$o_prodi->study_program_id;?>"><?=$o_prodi->study_program_name_feeder;?></option>
                <?php
                    }
                }
                ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button id="btn_filter_accreditation" class="btn btn-info float-right" type="button">Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-header">
        List Question Tracer Study Dikti
    </div>
    <div class="card-body">
        <!-- <h4>
            Total Alumni: 
            <a href="<?=base_url()?>alumni/lists_alumni" title="View Details"><i class="fas fa-eye"></i></a>
        </h4>
        <h4>
            Total Responden (alumni yang mengisi): 
            <a href="<?=base_url()?>alumni/lists_tracer" title="View Details"><i class="fas fa-eye"></i></a>
        </h4> -->
        <ul class="list-group">
            <li class="list-group-item active">Question</li>
            <li class="list-group-item"></li>
    <?php
    if ($dikti_question) {
        foreach ($dikti_question as $o_question) {
            if ($o_question->question_id == 'f13') {
                $o_question->question_child = false;
            }
    ?>
            
    <?php
            if ($o_question->question_child) {
        ?>
            <li class="list-group-item">
                <?= $o_question->question_number ;?>. <?=trim($o_question->question_name);?> 
                <!-- (<?=$o_question->question_id;?>) -->
                <br>
                <small><?= (!is_null($o_question->question_english_name)) ? '<i>'.trim($o_question->question_english_name).'</i>' : '' ?></small>
                <!-- <br> -->
            </li>
        <?php
                foreach ($o_question->question_child as $o_question_child) {
            ?>
            <button type="button" class="btn btn_question list-group-item list-group-item-action pl-5 text-dark" qvalue="<?=$o_question_child->question_id;?>">
                <?= $o_question_child->question_number ;?>. <?=trim($o_question_child->question_name);?> 
                <!-- (<?=$o_question_child->question_id;?>) -->
                <br>
                <small><?= (!is_null($o_question_child->question_english_name)) ? '<i>'.trim($o_question_child->question_english_name).'</i>' : '' ?></small>
                <!-- <br> -->
                <i class="fas fa-eye float-right" title="View Result"></i>
            </button>
            <!-- <a href="<?=base_url()?>accreditation/tracer_result/<?=$o_question_child->question_id;?>" target="_blank" class="list-group-item list-group-item-action pl-5 text-dark">
                
            </a> -->
            <?php
                }
            }
            else {
        ?>
            <button type="button" class="btn btn_question list-group-item list-group-item-action text-dark" qvalue="<?=$o_question->question_id;?>">
                <?= $o_question->question_number ;?>. <?=trim($o_question->question_name);?>
                <!-- (<?=$o_question->question_id;?>) -->
                <br>
                <small><?= (!is_null($o_question->question_english_name)) ? '<i>'.trim($o_question->question_english_name).'</i>' : '' ?></small>
                <!-- <br> -->
                <i class="fas fa-eye float-right" title="View Result"></i>
            </button>
            <!-- <a href="<?=base_url()?>accreditation/tracer_result/<?=$o_question->question_id;?>" target="_blank" class="list-group-item list-group-item-action text-dark">
                
            </a> -->
        <?php
            }
        }
    }
    ?>
        </ul>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_chart_result">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Result Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body_modal_chart">
                <!--  -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    var selectmulti = $('#graduation_year').selectpicker();
    $('#academic_year, #study_program').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap"
    });

    $('.btn_question').on('click', function(e) {
        e.preventDefault();
        $.blockUI();
        let qid = $(this).attr('qvalue');
        let form = $('#form_filter_accreditation');
        var data = form.serialize();
        // console.log(attr);
        
        $.post('<?=base_url()?>accreditation/tracer_result/' + qid, data, function(result) {
            // console.log(result);
            $('#body_modal_chart').html(result.html);
            $('#modal_chart_result').modal('show');
            $.unblockUI();
        }, 'json').fail(function(params) {
            $.unblockUI();
        });
    });
})
</script>