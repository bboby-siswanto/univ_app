<form id="siblings_setting" onsubmit="return false">
    <ul class="list-group">
        <li class="list-group-item active">Siblings Settings
        </li>
        <li class="list-group-item">
            <div class="row">
                <div class="col-md-5">
                    <label>Relation Type</label>
                </div>
                <div class="col-md-7">
                    <select name="sibling_type" id="sibling_type" class="form-control">
                        <option value="">Please select...</option>
                <?php
                    if ($sibling_type) {
                        foreach ($sibling_type as $type) {
                ?>
                        <option value="<?= $type?>" <?= (($student_scholarship) AND ($student_scholarship->sibling_type == $type)) ? 'selected' : '' ?>><?= strtoupper($type) ?></option>
                <?php
                        }
                    }
                ?>
                    </select>
                </div>
            </div>
        </li>   
        <li class="list-group-item">
            <div id="student_type" class="row mt-2 <?= (($student_scholarship) AND ($student_scholarship->sibling_type == 'student')) ?  '' : 'd-none' ?>">
                <div class="col-md-5">
                    <label>Choose Siblings Person</label>
                </div>
                <div class="col-md-7">
                    <select name="student_personal_siblings_id" id="student_personal_siblings_id" class="form-control"></select>
                    <!-- <input type="hidden" name="personal_data_id_siblings_with_student" id="personal_data_id_siblings_with_student"> -->
                </div>
            </div>
            <div id="employee_type" class="row mt-2 <?= (($student_scholarship) AND ($student_scholarship->sibling_type == 'employee')) ?  '' : 'd-none' ?>">
                <div class="col-md-5">
                    <label>Choose Siblings Person</label>
                </div>
                <div class="col-md-7">
                    <select name="employee_personal_siblings_id" id="employee_personal_siblings_id" class="form-control"></select>
                    <!-- <input type="hidden" name="personal_data_id_siblings_with_employee" id="personal_data_id_siblings_with_employee"> -->
                </div>
            </div>
        </li>
        <li class="list-group-item">
            <div class="float-right">
                <button type="button" id="button_save_siblings" class="btn btn-info">Save</button>
            </div>
        </li>
    </ul>                
</form>

<script>
var $newOption = $("<option selected='selected'></option>").val('<?= ($sibling_data) ? $sibling_data[0]->personal_data_id : ""; ?>').text('<?= ($sibling_data) ? $sibling_data[0]->personal_data_name : ""; ?>');
<?php
if (($student_scholarship) AND (!is_null($student_scholarship->personal_data_id_sibling_with))) {
    
    if ($student_scholarship->sibling_type == 'student') {
?>
    $("select#student_personal_siblings_id").append($newOption).trigger("change");
<?php
    }
    else if ($student_scholarship->sibling_type == 'employee') {
?>
    $("select#employee_personal_siblings_id").append($newOption).trigger("change");
<?php
    }
}
?>
    $('select#student_personal_siblings_id').select2({
        minimumInputLength: 3,
		allowClear: true,
		placeholder: "Please select",
		theme: "bootstrap",
		ajax: {
			url: '<?=base_url()?>student/get_student_by_name',
			type: "POST",
			dataType: 'json',
			data: function (params) {
				return {
					keyword: params.term,
                    status: 'active'
				}
			},
			processResults: function(result) {
				data = result.data;
				return {
					results: $.map(data, function (item) {
						return {
							text: item.personal_data_name + ' - ' + item.study_program_abbreviation + '/' + item.academic_year_id,
							id: item.personal_data_id
						}
					})
				}
			}
		}
	});

    $('select#employee_personal_siblings_id').select2({
        minimumInputLength: 3,
		allowClear: true,
		placeholder: "Please select",
		theme: "bootstrap",
		ajax: {
			url: '<?=base_url()?>employee/get_employee_by_name',
			type: "POST",
			dataType: 'json',
			data: function (params) {
				return {
					keyword: params.term,
                    status: 'active'
				}
			},
			processResults: function(result) {
				data = result.data;
				return {
					results: $.map(data, function (item) {
						return {
							text: item.personal_data_name,
							id: item.personal_data_id
						}
					})
				}
			}
		}
	});

    $('#sibling_type').on('change', function(e) {
        e.preventDefault();
        if ($("#sibling_type").val() == 'student') {
            $('#student_type').removeClass('d-none');
        }else{
            $('#student_type').addClass('d-none');
            $("#student_type_siblings").val('');
        }

        if ($("#sibling_type").val() == 'employee') {
            $('#employee_type').removeClass('d-none');
        }else{
            $('#employee_type').addClass('d-none');
            $("#employee_type_siblings").val('');
        }
    });

	$('#button_save_siblings').on('click', function(e) {
        e.preventDefault();

        if (confirm("Are you sure!")) {
            $.blockUI();
            var data = $('#siblings_setting').serialize();
            data += '&personal_data_id=<?= $personal_data_id;?>';
            // console.log(data);
            $.post('<?= base_url()?>admission/save_siblings', data, function(result) {
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
</script>