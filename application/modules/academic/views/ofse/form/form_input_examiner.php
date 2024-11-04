<div class="container">
    <form url="<?=base_url()?>academic/ofse/submit_ofse_examiner" id="form_submit_ofse_examiner" onsubmit="return false">
        <input type="hidden" name="score_id" id="score_id" class="v_value">
        <div class="row">
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_1_update">Examiner 1</label>
                        <input type="text" name="examiner_1" id="examiner_1_update" class="form-control v_value">
                        <input type="hidden" name="examiner_1_id" id="examiner_1_id_update" class="v_value">
                    </div>
                    <div class="form-group">
                        <label for="examiner_1_institute_update">Institution</label>
                        <input type="text" name="examiner_1_institute" id="examiner_1_institute_update" class="form-control v_value">
                        <input type="hidden" name="examiner_1_id_institute" id="examiner_1_id_institute_update" class="form-control v_value">
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_2_update">Examiner 2</label>
                        <input type="text" name="examiner_2" id="examiner_2_update" class="form-control v_value">
                        <input type="hidden" name="examiner_2_id" id="examiner_2_id_update" class="form-control v_value">
                    </div>
                    <div class="form-group">
                        <label for="examiner_2_institute_update">Institution</label>
                        <input type="text" name="examiner_2_institute" id="examiner_2_institute_update" class="form-control v_value">
                        <input type="hidden" name="examiner_2_id_institute" id="examiner_2_id_institute_update" class="form-control v_value">
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_3_update">Examiner 3</label>
                        <input type="text" name="examiner_3" id="examiner_3_update" class="form-control v_value">
                        <input type="hidden" name="examiner_3_id" id="examiner_3_id_update" class="form-control v_value">
                    </div>
                    <div class="form-group">
                        <label for="examiner_3_institute_update">Institution</label>
                        <input type="text" name="examiner_3_institute" id="examiner_3_institute_update" class="form-control v_value">
                        <input type="hidden" name="examiner_3_id_institute" id="examiner_3_id_institute_update" class="form-control v_value">
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-1">
                <div class="border rounded p-2">
                    <div class="form-group">
                        <label for="examiner_4_update">Examiner 4</label>
                        <input type="text" name="examiner_4" id="examiner_4_update" class="form-control v_value">
                        <input type="hidden" name="examiner_4_id" id="examiner_4_id_update" class="form-control v_value">
                    </div>
                    <div class="form-group">
                        <label for="examiner_4_institute_update">Institution</label>
                        <input type="text" name="examiner_4_institute" id="examiner_4_institute_update" class="form-control v_value">
                        <input type="hidden" name="examiner_4_id_institute" id="examiner_4_id_institute_update" class="form-control v_value">
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <button class="btn btn-info float-right mt-3" type="button" id="submit_update_examiner">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
$(function() {
    advisor_autocomplete($('input#examiner_1_update'), $('input#examiner_1_id_update'), $('input#examiner_1_institute_update'), $('input#examiner_1_id_institute_update'));
    advisor_autocomplete($('input#examiner_2_update'), $('input#examiner_2_id_update'), $('input#examiner_2_institute_update'), $('input#examiner_2_id_institute_update'));
    advisor_autocomplete($('input#examiner_3_update'), $('input#examiner_3_id_update'), $('input#examiner_3_institute_update'), $('input#examiner_3_id_institute_update'));
    advisor_autocomplete($('input#examiner_4_update'), $('input#examiner_4_id_update'), $('input#examiner_4_institute_update'), $('input#examiner_4_id_institute_update'));

    institute_autocomplete($('input#examiner_1_institute_update'), $('input#examiner_1_id_institute_update'));
    institute_autocomplete($('input#examiner_2_institute_update'), $('input#examiner_2_id_institute_update'));
    institute_autocomplete($('input#examiner_3_institute_update'), $('input#examiner_3_id_institute_update'));
    institute_autocomplete($('input#examiner_4_institute_update'), $('input#examiner_4_id_institute_update'));

    $('button#submit_update_examiner').on('click', function(e) {
        e.preventDefault();

        var form = $('#form_submit_ofse_examiner');
        var url = form.attr('url');
        var data = form.serialize();
        $.blockUI({ baseZ: 2000 });

        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                if ($.fn.DataTable.isDataTable('#ofse_student_krs')){
                    ofse_student_krs.ajax.reload(null, false);
                    $('#modal_input_examiner').modal('hide');
                }
                else if ($.fn.DataTable.isDataTable('#table_pariticipant_list')) {
                    table_pariticipant_list.ajax.reload();
                    $('#modal_update_examiner').modal('hide');
                    $('#modal_view_participant').modal('show');
                }
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing data!', 'Error');
        });
    });
});

function advisor_autocomplete(el, elId, elIns, elInstId){
    el.autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('thesis/get_advisor_by_name')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var arr = [];
                arr = $.map(rtn, function(m){
                    return {
                        label: m.advisor_name,
                        value: m.advisor_name,
                        id: m.advisor_id,
                        institute: m.institution_name,
                        institute_id: m.insitution_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            var institute = ui.item.institute;
            var institute_id = ui.item.institute_id;
            elId.val(id);
            elIns.val(institute);
            elInstId.val(institute_id);
        },
        change: function(event, ui){
            if(ui.item === null){
                elId.val('');
                // el.val('');
                // elIns.val('');
                elInstId.val('');
                // alert('Please use the selection provided');
            }
        }
    });

    el.autocomplete( "option", "appendTo", "#form_submit_ofse_examiner" );
};

function institute_autocomplete(el, elId){
    el.autocomplete({
        minLength: 2,
        source: function(request, response){
            var url = '<?=site_url('institution/get_institutions_ajax')?>';
            var data = {
                term: request.term,
                university: 'true'
            };
            $.post(url, data, function(rtn){
                var list = rtn.data;
                var arr = [];
                arr = $.map(list, function(m){
                    return {
                        label: m.institution_name,
                        value: m.institution_name,
                        id: m.institution_id
                    }
                });
                response(arr);
            }, 'json').fail(function(params) {
                console.log('error');
            });
        },
        select: function(event, ui){
            var id = ui.item.id;
            elId.val(id);
        },
        change: function(event, ui){
            if(ui.item === null){
                elId.val('');
                // el.val('');
                // alert('Please use the selection provided');
            }
        }
    });

    el.autocomplete( "option", "appendTo", "#form_submit_ofse_examiner" );
};
</script>