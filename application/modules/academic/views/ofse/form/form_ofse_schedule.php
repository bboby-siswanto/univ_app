<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group float-right" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-success float-right" id="btn_submit_schedule">
                <i class="fas fa-save"></i> Submit
            </button>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Manage Schedule <?=$ofse_data->ofse_period_name;?>
    </div>
    <div class="card-body">
        <form url="<?=base_url()?>academic/ofse/submit_schedule" id="form_input_schedule" onsubmit="return false">
            <input type="hidden" name="ofse_period_id" id="ofse_period_id" value="<?=$ofse_period_id;?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Exam Date</label>
                        <input type="date" class="form-control" name="input_exam_date" id="input_exam_date" value="<?=($ofse_date) ? $ofse_date : '';?>">
                    </div>
                    <div class="form-group">
                        <label>Exam Room</label>
                        <!-- <input type="text" class="form-control" name="input_exam_room" id="input_exam_room" value="<?=($ofse_room) ? $ofse_room : '';?>"> -->
                        <select name="input_exam_room" id="input_exam_room" class="form-control">
                            <option value="">Please Select</option>
                            <option value="R702" <?=(($ofse_room) AND ($ofse_room == 'R702')) ? 'selected="selected"' : '';?>>R702</option>
                            <option value="R703" <?=(($ofse_room) AND ($ofse_room == 'R703')) ? 'selected="selected"' : '';?>>R703</option>
                            <option value="R704" <?=(($ofse_room) AND ($ofse_room == 'R704')) ? 'selected="selected"' : '';?>>R704</option>
                            <option value="R716" <?=(($ofse_room) AND ($ofse_room == 'R716')) ? 'selected="selected"' : '';?>>R716</option>
                            <option value="R717" <?=(($ofse_room) AND ($ofse_room == 'R717')) ? 'selected="selected"' : '';?>>R717</option>
                            <option value="R718" <?=(($ofse_room) AND ($ofse_room == 'R718')) ? 'selected="selected"' : '';?>>R718</option>
                            <option value="R719" <?=(($ofse_room) AND ($ofse_room == 'R719')) ? 'selected="selected"' : '';?>>R719</option>
                            <option value="R720" <?=(($ofse_room) AND ($ofse_room == 'R720')) ? 'selected="selected"' : '';?>>R720</option>
                            <option value="R721" <?=(($ofse_room) AND ($ofse_room == 'R721')) ? 'selected="selected"' : '';?>>R721</option>
                            <option value="R722" <?=(($ofse_room) AND ($ofse_room == 'R722')) ? 'selected="selected"' : '';?>>R722</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Zoom ID</label>
                        <input type="text" class="form-control" name="input_exam_zoomid" id="input_exam_zoomid" value="<?=($ofse_exam_data) ? $ofse_exam_data[0]->exam_zoom_id : '';?>">
                    </div>
                    <div class="form-group">
                        <label>Passcode</label>
                        <input type="text" class="form-control" name="input_exam_zoompasscode" id="input_exam_zoompasscode" value="<?=($ofse_exam_data) ? $ofse_exam_data[0]->exam_zoom_passcode : '';?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <table id="table_participant_exam" class="table table-bordered table-hover">
                        <thead>
                            <tr class="bg-dark">
                                <th>Time</th>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                    for ($i=8; $i <= 21; $i++) { 
                        $s_time_start = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
                        $s_time_end = str_pad(($i + 1), 2, '0', STR_PAD_LEFT).':00';
                        $o_participant_data = false;
                        if ($ofse_exam_data) {
                            foreach ($ofse_exam_data as $o_exam_participant) {
                                if ($o_exam_participant->exam_time_start == $s_time_start.':00') {
                                    $o_participant_data = $o_exam_participant;
                                }
                            }
                        }
                ?>
                            <tr>
                                <td>
                                    <?=$s_time_start.'-'.$s_time_end;?>
                                    <input type="hidden" name="input_exam_time_start[]" value="<?=$s_time_start;?>">
                                    <input type="hidden" name="input_exam_time_end[]" value="<?=$s_time_end;?>">
                                </td>
                                <td class="w-25">
                                    <select name="input_exam_student_id[]" id="input_exam_student_id_<?=$i;?>" class="form-control">
                                        <option value=""></option>
                            <?php
                            if ($ofse_participant) {
                                foreach ($ofse_participant as $o_participant) {
                                    $selected = (($o_participant_data) AND ($o_participant_data->student_id == $o_participant->student_id)) ? 'selected="selected"' : '';
                            ?>
                                        <option value="<?=$o_participant->student_id;?>" <?=$selected;?>><?=$o_participant->personal_data_name;?> (<?=$o_participant->study_program_abbreviation.'/'.$o_participant->student_batch;?>)</option>
                            <?php
                                }
                            }
                            ?>
                                    </select>
                                </td>
                                <td class="w-50">
                                    <select name="input_exam_score_id[]" id="input_exam_score_id_<?=$i;?>" class="form-control">
                                        <option value=""></option>
                            <?php
                            if ($o_participant_data) {
                            ?>
                                        <option value="<?=$o_participant_data->score_id;?>" selected="selected"><?=$o_participant_data->subject_name;?></option>
                            <?php
                            }
                            ?>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" id="btn_reset_input_row_<?=$i;?>">Reset</button>
                                </td>
                            </tr>
                <?php
                    }
                ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(function() {
    // var subject_data = ["results" : []];
    
    for (let row_table = 8; row_table <= 21; row_table++) {
        $('#input_exam_student_id_' + row_table).select2({
            placeholder: "Please select..",
            theme: "bootstrap"
        });

        $('#input_exam_score_id_' + row_table).select2({
            placeholder: "Please select..",
            theme: "bootstrap"
        });

        $('#input_exam_student_id_' + row_table).on('change', function(e) {
            var option_student_id = $('option:selected', this).val();
            $('#input_exam_score_id_' + row_table).empty().trigger("change");

            var options = new Option('', '', true, true);
            $('#input_exam_score_id_' + row_table).append(options).trigger('change');

            $.post('<?=base_url()?>academic/ofse/get_ofse_subject_participant', {student_id: option_student_id, ofse_period_id: '<?=$ofse_period_id;?>'}, function(result) {
                var data_option = result.data;
                $.each(data_option, function(i, v) {
                    // console.log(v.academic_year_id);
                    var option = new Option(v.subject_name, v.score_id, true, true);
                    $('#input_exam_score_id_' + row_table).append(option);
                });
            }, 'json').fail(function() {
                toastr.error('error retrieve data!');
            });
        });

        $('button#btn_reset_input_row_' + row_table).on('click', function(e) {
            e.preventDefault();

            $('#input_exam_student_id_' + row_table).val(null).trigger('change');
        });
    }

    $('button#btn_submit_schedule').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('#form_input_schedule');
        var data = form.serialize();
        var url = form.attr('url');

        let examdate = $('#input_exam_date').val();
        let examroom = $('#input_exam_room').val();
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success!');
                setTimeout(() => {
                    // location.reload();
                    let newloc = '<?=base_url()?>academic/ofse/manage_ofse_schedule/<?=$ofse_period_id;?>';
                    if (examdate !== '') {
                        newloc = ('<?=$ofse_date;?>' !== '') ? newloc + '/' + '<?=$ofse_date;?>' : newloc + '/' + examdate;
                    }
                    if (examroom !== '') {
                        newloc = ('<?=$ofse_room;?>' !== '') ? newloc + '/' + '<?=$ofse_room;?>' : newloc;
                    }
                    window.location.href = newloc;
                }, 2000);
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!');
        })
    });
});

// function select2asHtml(optionElement) {
//     if (!optionElement.id) { return optionElement.text; }
//     if (optionElement.marked == '0') { return optionElement.text; }
//     var $state = $('<strong>' + optionElement.text + '</strong> ');
//     return $state;
// }
</script>