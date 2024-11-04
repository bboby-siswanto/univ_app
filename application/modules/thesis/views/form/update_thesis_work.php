<div class="container">
    <form url="<?=base_url()?>thesis/update_thesis_work" id="form_edit_thesis_work" onsubmit="return false">
        <input type="hidden" name="thesis_id" id="thesis_id" class="v_value">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="thesis_title_update">Thesis Title</label>
                    <textarea name="thesis_title" id="thesis_title_update" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="advisor_1_update">Advisor</label>
                        <select name="advisor_1_update" id="advisor_1_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="advisor_1_institute_update">Institution</label>
                        <input type="text" name="advisor_1_institute" id="advisor_1_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="advisor_2_update">Advisor</label>
                        <select name="advisor_2_update" id="advisor_2_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="advisor_2_institute_update">Institution</label>
                        <input type="text" name="advisor_2_institute" id="advisor_2_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_1_update">Examiner 1</label>
                        <select name="examiner_1_update" id="examiner_1_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="examiner_1_institute_update">Institution</label>
                        <input type="text" name="examiner_1_institute" id="examiner_1_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_2_update">Examiner 2</label>
                        <select name="examiner_2_update" id="examiner_2_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="examiner_2_institute_update">Institution</label>
                        <input type="text" name="examiner_2_institute" id="examiner_2_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_3_update">Examiner 3</label>
                        <select name="examiner_3_update" id="examiner_3_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="examiner_3_institute_update">Institution</label>
                        <input type="text" name="examiner_3_institute" id="examiner_3_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_4_update">Examiner 4</label>
                        <select name="examiner_4_update" id="examiner_4_update" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="examiner_4_institute_update">Institution</label>
                        <input type="text" name="examiner_4_institute" id="examiner_4_institute_update" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <button class="btn btn-info float-right pt-3" type="button" id="submit_update_thesis_work">Submit</button>
            </div>
        </div>
    </form>
</div>