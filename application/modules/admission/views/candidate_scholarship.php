<form id="scholarship_setting" onsubmit="return false">
    <input type="hidden" name="student_id" value="<?=$student_data->student_id;?>">
    <input type="hidden" name="student_number" value="<?= (($student_data) AND (!is_null($student_data->student_number))) ? '1' : '0' ?>">
    <ul class="list-group">
        <li class="list-group-item active">Candidate Scholarship</li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Scholarship</label>
                </div>
                <div class="col-md-7">
                    <select name="scholarship_id" id="scholarship_id" class="form-control">
                        <option value="">Please select...</option>
                    <?php
                        if ($scholarship_id) {
                            foreach ($scholarship_id as $scholarship) {
                        ?>
                                <option value="<?= $scholarship->scholarship_id;?>" <?= (($student_data) AND ($student_data->scholarship_id == $scholarship->scholarship_id)) ? 'selected' : '' ?>><?=$scholarship->scholarship_name.' ('.$scholarship->scholarship_description.')';?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </li>
        <!-- <li class="list-group-item scholarship_fee">
            <div class="row">
                <div class="col-md-5">
                    Semester Fee (IDR)
                </div>
                <div class="col-md-7">
                    <input type="text" name="semester_fee" id="semester_fee" class="form-control form-number" value="<?= (($student_data) AND ($student_data->semester_fee != '')) ? $student_data->semester_fee : ''; ?>">
                </div>
            </div>
        </li> -->
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Scholarship Status </label>
                </div>
                <div class="col-md-7">
                    <select name="scholarship_status" id="scholarship_status_id" class="form-control">
                        <option value="">None</option>
                        <?php
                            if ($scholarship_status) {
                                foreach ($scholarship_status as $status) {
                                    if (in_array($status, $status_scholarship)) {
                        ?>
                                <option value="<?= $status?>" <?= (($student_data) AND ($student_data->scholarship_status == $status)) ? 'selected' : '' ?>><?= strtoupper($status) ?></option>
                        <?php
                            }
                        }
                    }
                ?>
                    </select>
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="float-right">
                <button type="button" id="button_save_scholarship" class="btn btn-info">Save</button>
            </div>
        </li>
    </ul>                
</form>

<script>
    $('.form-number').number(true, 0);
    $('#scholarship_id, #scholarship_status_id').select2({
        allowClear: true,
        placeholder: 'Please select...',
        theme: "bootstrap",
    });
    $(function() {
        $('#button_save_scholarship').on('click', function(e) {
            e.preventDefault();

            if (confirm("Are you sure!")) {
                $.blockUI();
                var data = $('#scholarship_setting').serialize();
                data += '&personal_data_id=<?= $personal_data_id;?>';
                console.log(data);
                $.post('<?= base_url()?>admission/save_candidate_scholarship', data, function(result) {
                    $.unblockUI();
                    if (result.code == 0) {
                        toastr['success']('Success saving setting data', 'Success');
                    }else{
                        toastr['warning'](result.message, 'Warning!');
                    }
                }, 'json').fail(function(params) {
                    console.log(params.responseText);
                    $.unblockUI();
                });
            }
        });
    })
</script>