<form method="post" onsubmit="return false" id="form_temporary_graduation">
    <input type="hidden" name="student_id" id="student_temporary_graduation">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <label>Letter Number</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">L</span>
                    </div>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="temporary_graduation_fac"></span>
                        <input type="hidden" name="letter_fac" id="temporary_graduation_fac_input">
                    </div>
                    <input type="text" class="form-control" placeholder="XXXX" name="letter_number_numb">
                    <input type="text" class="form-control" placeholder="M" name="letter_number_month" value="<?= (isset($month_roman)) ? $month_roman : '' ?>">
                    <input type="text" class="form-control" placeholder="YYYY" name="letter_number_year" value="<?= (isset($year_data)) ? $year_data : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_letter_program">Date of Letter</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="letter_date" value="<?= (isset($date_now)) ? $date_now : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_comprehensive_examination_temporary_graduation">Date of passed the comprehensive examination</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="date_comprehensive_examination" id="date_comprehensive_examination_temporary_graduation">
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_receipt_temporary_graduation">Date of receipt of the original certificate</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="date_receipt" id="date_receipt_temporary_graduation">
                </div>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <button type="button" class="btn btn-primary" name="btn_download_temporary_graduation" id="btn_download_temporary_graduation">Generate and Download</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $(function() {

        $('#date_receipt_temporary_graduation').datepicker({
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

        $('button#btn_download_temporary_graduation').on('click', function(e) {
            e.preventDefault();
            // var data = $('form#input_application_internship').serializeArray();
            $.blockUI({baseZ: 9999});
            var data = $('form#form_temporary_graduation').serialize();
            $.post('<?=base_url()?>download/doc_download/generate_temporary_graduation_letter', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#temporary_graduation_letter_modal').modal('hide');
                    
                    document.location.href = "<?=base_url()?>download/doc_download/download_academic_file/" + result.file_name + "/temporary_graduation";
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