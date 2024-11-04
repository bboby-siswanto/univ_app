<form method="post" onsubmit="return false" id="form_ref_letter_program">
    <input type="hidden" name="student_id" id="student_letter_program">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <label>Letter Number</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">L</span>
                    </div>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="fac_abbreviation"></span>
                        <input type="hidden" name="letter_number_fac" id="letter_number_fac">
                    </div>
                    <input type="text" class="form-control" name="letter_number_numb">
                    <input type="text" class="form-control" name="letter_number_month" value="<?= (isset($month_roman)) ? $month_roman : '' ?>">
                    <input type="text" class="form-control" name="letter_number_year" value="<?= (isset($year_data)) ? $year_data : '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_letter_program">Date of Letter</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="letter_date" value="<?= (isset($date_now)) ? $date_now : '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label for="academic_year_letter_program">Academic Year</label>
                <div class="form-group">
                    <select name="academic_year_id" id="academic_year_letter_program" class="form-control">
<?php
    foreach ($academic_year_list as $o_academic_year) {
?>
                        <option value="<?=$o_academic_year->academic_year_id.'/'.(intval($o_academic_year->academic_year_id)+1);?>"><?=$o_academic_year->academic_year_id.'/'.(intval($o_academic_year->academic_year_id)+1);?></option>
<?php
    }
?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="program_letter_program">Program</label>
                <div class="form-group">
                    <select name="program" id="program_letter_program" class="form-control">
                        <option value="ijd" selected>International Join Degree</option>
                        <option value="dd" selected>Double Degree</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <label for="university_letter_program">University</label>
                <div class="form-group">
                    <select name="university" id="university_letter_program" class="form-control">
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <label for="arrive_date_letter_program">Date Arrival</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="date_arrival">
                </div>
            </div>
            <div class="col-md-6">
                <label for="return_date_letter_program">Date Return</label>
                <div class="form-group">
                    <input type="date" class="form-control" name="date_return">
                </div>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <button type="button" class="btn btn-primary" name="btn_download_german_letter" id="btn_download_german_letter">Generate and Download</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#university_letter_program').select2({
            minimumInputLength: 3,
            allowClear: true,
            placeholder: "Please select",
            theme: "bootstrap",
            ajax: {
                url: '<?=base_url()?>institution/get_institutions_ajax',
                type: "POST",
                dataType: 'json',
                data: function (params) {
                    return {
                        term: params.term,
                        university: 'true'
                    };
                },
                processResults: function(result) {
                    data = result.data;
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.institution_name,
                                id: item.institution_id
                            }
                        })
                    }
                }
            },
            language: {
                noResults: function(term) {
                    return "No results found <button onclick='new_university()' class='btn btn-link'>+ Add University</button>";
                }
            },
            escapeMarkup: function(markup) {
                return markup;
            }
        });

        $('button#btn_download_german_letter').on('click', function(e) {
            e.preventDefault();
            // var data = $('form#input_application_internship').serializeArray();
            $.blockUI({baseZ: 9999});
            var data = $('form#form_ref_letter_program').serialize();
            $.post('<?=base_url()?>download/doc_download/generate_german_letter', data, function(result) {
                $.unblockUI();
                if (result.code == 0) {
                    $('#ref_letter_to_germany_modal').modal('hide');
                    
                    document.location.href = "<?=base_url()?>download/doc_download/download_academic_file/" + result.file_name + "/german_letter";
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

    function new_university() {
        console.log('clicked');

        $('#add_institution_modal').modal('show');
    }
</script>