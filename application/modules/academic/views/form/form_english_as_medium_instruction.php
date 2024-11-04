<form method="post" onsubmit="return false" id="form_english_medium_letter">
    <input type="hidden" name="student_id" id="student_english_medium_letter">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <label>Letter Number</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">L</span>
                    </div>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="english_medium_fac"></span>
                        <input type="hidden" name="letter_fac" id="english_medium_fac_input">
                    </div>
                    <input type="text" class="form-control" placeholder="XXXX" name="letter_number_numb">
                    <input type="text" class="form-control" placeholder="M" name="letter_number_month" value="<?= (isset($month_roman)) ? $month_roman : '' ?>">
                    <input type="text" class="form-control" placeholder="YYYY" name="letter_number_year" value="<?= (isset($year_data)) ? $year_data : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_letter_english_program">Date of Letter</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="letter_date" id="date_letter_english_program" value="<?= (isset($date_now)) ? $date_now : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="letter_english_request_by">Request By</label>
                <div class="custom-control custom-checkbox float-right">
                    <input type="checkbox" class="custom-control-input" id="letter_english_request_by_student" name="english_request_student" checked>
                    <label class="custom-control-label" for="letter_english_request_by_student">Student</label>
                </div>
                <div id="letter_english_request_name" class="form-group d-none">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <select name="request_gender" id="letter_english_request_gender" class="custom-select">
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                            </select>
                        </div>
                        <input type="text" class="form-control" name="request_by" id="letter_english_request_by">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="letter_english_request_purpose">Request Purpose</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="request_purpose" id="letter_english_request_purpose">
                </div>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <button type="button" class="btn btn-primary" name="btn_download_english_medium" id="btn_download_english_medium">Generate and Download</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $(function() {

        // $('#date_receipt_temporary_graduation').datepicker({
        //     dateFormat: 'MM yy',
        //     changeYear: true,
        //     changeMonth: true,
        //     showButtonPanel: true,
        //     onClose: function(dateText, inst) { 
        //         $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        //     }
        // }).on('focus', function () {
        //     $(".ui-datepicker-calendar").hide();
        //     $("#ui-datepicker-div").position({
        //         my: "top",
        //         at: "bottom",
        //         of: $(this)
        //     });
        // });

        $('input#letter_english_request_by_student').on('change', function() {
            if ($('#letter_english_request_by_student').is(':checked')) {
                $('#letter_english_request_name').addClass('d-none');
            }else{
                $('#letter_english_request_name').removeClass('d-none');
            }
        });

        $('button#btn_download_english_medium').on('click', function(e) {
            e.preventDefault();

            // $.blockUI({baseZ: 9999});
            var data = $('form#form_english_medium_letter').serialize();
            // console.log(data);
            $.post('<?=base_url()?>download/doc_download/generate_english_medium_instruction', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#temporary_graduation_letter_modal').modal('hide');
                    
                    document.location.href = "<?=base_url()?>download/doc_download/download_academic_file/" + result.file_name + "/english_medium_letter";
                    // console.log(result);
                }else{
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $.unblockUI();
                toastr.error('Error retrieve data!', 'Error');
            });
        });

    });
</script>