<div class="modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" id="modal_input_history_job">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Job Data</h5>
            </div>
            <div class="modal-body">
                <form id="form_job_alumni" onsubmit="return false">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="institution_name" class="required_text">Company/Institution Name</label>
                                <input type="text" name="institution_name" id="institution_name" placeholder="Company Name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="personal_data_supervisor" class="required_text">Supervisor Name</label>
                                <input type="text" name="personal_data_supervisor" id="personal_data_supervisor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="institution_phone_number" class="required_text">Company Phone Number</label>
                                <input type="text" class="form-control" name="institution_phone_number" id="institution_phone_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="institution_email" class="required_text">Company Email</label>
                                <input type="text" class="form-control" name="institution_email" id="institution_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_start_date" class="required_text">Start Working</label>
                                <input type="date" class="form-control" name="company_start_date" id="company_start_date">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submit_job_alumni" class="btn btn-primary">Submit</button>
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('button#submit_job_alumni').on('click', function(e) {
            e.preventDefault();

            var data = $('form#form_job_alumni').serialize();
            $.post('<?=base_url()?>alumni/job_history/submit_job_alumni', data, function(result) {
                if (result.code == 0) {
                    toastr.success('Thank You!');
                    $('div#modal_input_history_job').modal('hide');
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                toastr.error('Cannt processing your data right now, please contact IT Team in employee@company.ac.id!', 'Error!');
            });
        });
    });
</script>