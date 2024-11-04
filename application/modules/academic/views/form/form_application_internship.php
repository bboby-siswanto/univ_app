<form method="post" id="input_application_internship">
    <input type="hidden" name="student_id_internship" id="student_id_internship">
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label for="date_letter_internship">Date of Letter</label>
                <input type="date" class="form-control" name="date_letter_internship" id="date_letter_internship">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="company_name_internship">Company Name</label>
                <input type="text" class="form-control" name="company_name_internship" id="company_name_internship">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="dept_internship">Dept. of Internship</label>
                <input type="text" class="form-control" name="dept_internship" id="dept_internship">
                <small class="text-danger">*ex: the Operation Center Department</small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="company_address_internhip">Company Addess</label>
                <textarea name="company_address_internhip" id="company_address_internhip" class="form-control" cols="30" rows="3"></textarea>
                <!-- <input type="text" class="form-control" name="company_address_internhip" id="company_address_internhip"> -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="month_start_internship">Start of Internship</label>
                <input type="text" name="month_start_internship" id="month_start_internship" class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="month_end_internship">End of Internship</label>
                <input type="text" name="month_end_internship" id="month_end_internship" class="form-control">
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="float-right">
                <button type="button" class="btn btn-primary" name="btn_download_application_internship" id="btn_download_application_internship">Generate and Download</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $('button#btn_download_application_internship').on('click', function(e) {
            e.preventDefault();
            // var data = $('form#input_application_internship').serializeArray();
            $.blockUI({baseZ: 9999});
            var data = $('form#input_application_internship').serialize();
            $.post('<?=base_url()?>download/doc_download/generate_application_internship', data, function(result) {
            // $.post('<?=base_url()?>download/pdf_download/generate_template_of_ref_letter', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#appliation_letter_for_internship_modal').modal('hide');
                    
                    document.location.href = "<?=base_url()?>download/doc_download/download_academic_file/" + result.file_name + "/internship_letter";
                    // console.log(result);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error retrieve data!', 'Error');
            });
        });
        
        var datepicker_start = $('#month_start_internship').datepicker({
            dateFormat: 'MM yy',
            changeYear: true,
            changeMonth: true,
            showButtonPanel: true,
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        }).on('change', function() {
            datepicker_end.datepicker( "option", "minDate",  $(this).datepicker('getDate') );
            datepicker_end.datepicker('setDate', '');
        }).on('focus', function () {
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "top",
                at: "bottom",
                of: $(this)
            });
        });

        var datepicker_end = $('#month_end_internship').datepicker({
            dateFormat: 'MM yy',
            changeYear: true,
            changeMonth: true,
            showButtonPanel: true,
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        }).on('focus', function () {
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "top",
                at: "bottom",
                of: $(this)
            });
        });
    });
</script>