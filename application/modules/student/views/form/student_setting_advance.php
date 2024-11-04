<ul class="list-group">
    <li class="list-group-item active">Advance Settings</li>
    <li class="list-group-item">
        <div class="row">
            <div class="col">
                <label>Portal Blocked</label>
            </div>
            <div class="col">
                <div class="pull-right">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="student_block" name="student_block" <?= (($student_data) AND ($student_data->student_portal_blocked == 'TRUE')) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="student_block"></label>
                    </div>
                </div>
            </div>
        </div>
        <div id="input_blocked_message" class="row mt-2 <?= (($student_data) AND ($student_data->student_portal_blocked == 'TRUE')) ? '' : 'd-none' ?>">
            <div class="col-md-5">
                <label>Blocked Message</label>
            </div>
            <div class="col-md-7">
                <input type="text" name="blocked_message" id="blocked_message" class="form-control" value="<?= (($student_data) AND (!is_null($student_data->student_portal_blocked))) ? $student_data->student_portal_blocked_message : '' ?>">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col">
                <label>Send Transcript</label>
            </div>
            <div class="col">
                <div class="pull-right">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="student_send_transcript" name="student_send_transcript" <?= (($student_data) AND ($student_data->student_send_transcript == 'TRUE')) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="student_send_transcript"></label>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>