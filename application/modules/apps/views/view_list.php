<div class="row">
    <div class="col-12">
        <div class="btn-group pb-2 float-right" role="group" aria-label="Basic example">
            <button type="button" id="new_request_number" class="btn btn-success">
                <i class="fas fa-plus"></i> Request New Number
            </button>
        <?php
        if (isset($user_allowed_template) AND (in_array($this->session->userdata('user'), $user_allowed_template))) {
        ?>
            <a href="<?=base_url()?>apps/letter_numbering/letter_type" class="btn btn-primary">
                <i class="fas fa-list"></i> Letter List
            </a>
        <?php
        }
        ?>
        </div>
    </div>
</div>
<?=$list_table;?>
<div class="modal" tabindex="-1" role="dialog" id="modal_request">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('apps/letter_numbering/form_generate');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_select_template">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="template_list" class="required_text">Select a file template</label>
                            <select name="template_list" id="template_list" class="form-control"></select>
                        </div>
                        *) <small class="text-danger">if the template is not found please contact SPMI or IT Dept.</small>
                    </div>
                    <div class="col-sm-12 list_template_modal">
                        <div id="tform_0" class="form_template d-none">
                            <hr>
                            <?=modules::run('apps/letter_numbering/form_generated');?>
                        </div>
                        <div id="tform_1" class="form_template d-none">
                            <hr>
                            <?=modules::run('apps/letter_numbering/form_apl_internship_student');?>
                        </div>
                        <div id="tform_4" class="form_template d-none">
                            <hr>
                            <?=modules::run('apps/letter_numbering/form_asl_advisor_thesis');?>
                        </div>
                        <div id="tform_7" class="form_template d-none">
                            <hr>
                            <?=modules::run('apps/letter_numbering/modal_assignment_letter_lecturing');?>
                        </div>
                        <div id="tform_17" class="form_template d-none">
                            <hr>
                            <?=modules::run('apps/letter_numbering/modal_assignment_letter_community');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal" tabindex="-1" role="dialog" id="result_number_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Result</h5>
            </div>
            <div class="modal-body">
                <h5 class="result_number"></h5>
                <p><br></p>
                <p id="a_template_file"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->
<script>
    $('select#template_list').select2({
        allowClear: true,
        placeholder: "Please select..",
        theme: "bootstrap",
        cache: false
    });

    $(function() {
        $('select#template_list').on('change', function(e) {
            var select_form = $('select#template_list');
            var data_force = $('option:selected', this).attr('data-force');
            $('.form_template').addClass('d-none');
            var form_found = $('.list_template_modal').find('#tform_' + select_form.val());
            // console.log(select_form.val());

            if (data_force == 'yes') {
                console.log(select_form.val());
                if (form_found.length == 0) {
                    $('#tform_0').removeClass('d-none');
                }
                else{
                    $('#tform_' + select_form.val()).removeClass('d-none');
                }
            }
            else {
                $('#tform_0').removeClass('d-none');
            }
        });

        $('button#new_request_number').on('click', function(e) {
            e.preventDefault();
            $('#backdate').attr('disabled', 'disabled');
            $('form#form_letter_number')[0].reset();
            $('select#letter_type').val(null).trigger("change");
            $('select#department').val(null).trigger("change");
            $('div#modal_request').modal('show');
        });
    })
</script>