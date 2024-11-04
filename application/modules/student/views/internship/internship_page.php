<!-- <div class="row mt-5">
    <div class="col-12 mb-3">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action button groups">
            <div class="btn-group" role="group" aria-label="First group">
                <button type="button" class="btn btn-success" id="btn_submit_internship" data-toggle="tooltip" data-placement="bottom" title="Submit to Dean">
                    <i class="fas fa-save"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div> -->
<div class="card">
    <div class="card-header">
        Internship Form
    </div>
    <div class="card-body">
        <form url="<?=base_url()?>student/internship/submit_internship" onsubmit="return false" id="form_internship">
            <input type="hidden" name="student_id" id="student_id" value="<?= ($student_data) ? $student_data->student_id : '' ?>">
            <input type="hidden" name="internship_id" id="internship_id" value="<?= ($internship_data) ? $internship_data->internship_id : ''; ?>">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="internship_company" class="required_text">Company Name</label>
                        <input type="text" class="form-control" name="internship_company" id="internship_company" value="<?= ($internship_data) ? $internship_data->institution_name : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="internship_department" class="required_text">Department</label>
                        <input type="text" class="form-control" name="internship_department" id="internship_department" value="<?= ($internship_data) ? $internship_data->department : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="internship_supervisor" class="required_text">Supervisor</label>
                        <input type="text" class="form-control" name="internship_supervisor" id="internship_supervisor" value="<?= ($internship_data) ? $internship_data->supervisor_name : ''; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12"><hr></div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="pt-1 pl-1 required_text" id="label_file_assessment">IULI Internship Assessment</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="file_assessment" name="file_assessment" aria-describedby="label_file_assessment">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <label class="pl-1 pt-1"><?= ($assessment_doc) ? $assessment_doc->document_name.' <a href="'.base_url().'student/internship/view_doc/'.$assessment_doc->internship_id.'/'.$assessment_doc->document_type.'" target="_blank" class="btn btn-link btn-sm"><i class="fas fa-download"></i></a>' : ''; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="pt-1 pl-1 required_text" id="label_file_logsheet">IULI Internship Logsheet</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="file_logsheet" name="file_logsheet" aria-describedby="label_file_logsheet">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <label class="pl-1 pt-1"><?= ($logshet_doc) ? $logshet_doc->document_name.' <a href="'.base_url().'student/internship/view_doc/'.$logshet_doc->internship_id.'/'.$logshet_doc->document_type.'" target="_blank" class="btn btn-link btn-sm"><i class="fas fa-download"></i></a>' : ''; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="pt-1 pl-1 required_text" id="label_file_report">IULI Internship Report</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="file_report" name="file_report" aria-describedby="label_file_report">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <label class="pl-1 pt-1"><?= ($report_doc) ? $report_doc->document_name.' <a href="'.base_url().'student/internship/view_doc/'.$report_doc->internship_id.'/'.$report_doc->document_type.'" target="_blank" class="btn btn-link btn-sm"><i class="fas fa-download"></i></a>' : ''; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="pt-1 pl-1" id="label_file_other_1">IULI Internship Other File 1</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="file_other_1" name="file_other_1" aria-describedby="label_file_other_1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <label class="pl-1 pt-1"><?= ($other1_doc) ? $other1_doc->document_name.' <a href="'.base_url().'student/internship/view_doc/'.$other1_doc->internship_id.'/'.$other1_doc->document_type.'" target="_blank" class="btn btn-link btn-sm"><i class="fas fa-download"></i></a>' : ''; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="pt-1 pl-1" id="label_file_other_2">IULI Internship Other File 2</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="form-control" id="file_other_2" name="file_other_2" aria-describedby="label_file_other_2">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                        <label class="pl-1 pt-1"><?= ($other2_doc) ? $other2_doc->document_name.' <a href="'.base_url().'student/internship/view_doc/'.$other2_doc->internship_id.'/'.$other2_doc->document_type.'" target="_blank" class="btn btn-link btn-sm"><i class="fas fa-download"></i></a>' : ''; ?></label>
                        </div>
                    </div>
                </div>
                <div class="col-12"><hr></div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-success btn-block" id="btn_submit_internship" data-toggle="tooltip" data-placement="bottom" title="Submit data">
                        <i class="fas fa-save"></i> Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$(function() {
    $('#btn_submit_internship').on('click', function(e) {
        e.preventDefault();

        $.blockUI();
        var form = $('#form_internship');
        var form_data = new FormData(form[0]);
        var uri = form.attr('url');
        
        $.ajax({
            url: uri,
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: function (xhr, status, error) {
                $.unblockUI();
                toastr.error('Error processing data!', 'Error');
                console.log(xhr.responseText);
            },
            success: function(rtn){
                $.unblockUI();
                if (rtn.code == 0) {
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                    toastr.success('Success!', 'Success!');
                }else{
                    toastr.warning(rtn.message, 'Warning!');
                }
            }
        });
    });
})
</script>