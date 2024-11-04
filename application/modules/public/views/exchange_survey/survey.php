<div class="card ml-5 mr-5">
    <div class="card-header" style="background-color: #001489 !important;">
        <div class="row text-light">
            <div class="col-12 col-sm-6 col-lg-3">
                <img src="<?=base_url();?>assets/img/iuli_logo.png" class="img-fluid d-block w-75 d-sm-none">
                <img src="<?=base_url();?>assets/img/iuli.png" class="img-fluid d-none w-75 d-sm-block">
            </div>
            <div class="col-12 col-sm-6 col-lg-9 align-self-end">
                <h3>STUDENT EXCHANGE PROGRAM</h3>
                <h3>STUDENT SURVEY</h3>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?= $form_survey; ?>
    </div>
    <div class="card-footer bg-white p-4">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-success" id="btn_submit_survey" name="btn_submit_survey">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    $('#input_homeuniv').autocomplete({
        minLength: 3,
        source: function(request, response){
            var url = '<?=site_url('public/survey/get_institutions')?>';
            var data = {
                term: request.term
            };
            $.post(url, data, function(rtn){
                var arr = [];
                arr = $.map(rtn.data, function(m){
                    return {
                        id: m.institution_id,
                        value: m.institution_name
                    }
                });
                response(arr);
            }, 'json');
        },
        select: function(event, ui){
            var id = ui.item.id;
            $('#input_homeuniv').val(ui.item.value);
        }
    });

    $('#input_student').autocomplete({
        minLength: 3,
        source: function(request, response){
            var url = '<?=site_url('public/survey/get_student_exchange')?>';
            var data = {
                student_name: request.term
            };
            $.post(url, data, function(rtn){
                var arr = [];
                arr = $.map(rtn, function(m){
                    return {
                        id: m.student_id,
                        value: m.student_name,
                        exchange_id: m.exchange_id,
                        institution_id: m.institution_id,
                        institution_name: m.institution_name,
                        study_program_name: m.study_program_name,
                        finance_year_id: m.finance_year_id
                    }
                });
                response(arr);
            }, 'json');
        },
        select: function(event, ui){
            var id = ui.item.id;
            $('#input_student_id').val(id);
            $('#input_homeuniv').val(ui.item.institution_name);
            $('#input_period').val(ui.item.finance_year_id);
            $('#input_prodi').val(ui.item.study_program_name);
        },
        change: function(event, ui){
            if(ui.item === null){
                $('#input_student_id').val('');
                $('#input_student').val('');
            }
        }
    });

    $('button#btn_submit_survey').on('click', function(e) {
        e.preventDefault();

        let url = '<?=base_url()?>public/survey/submit_exchange_survey';
        var form = $('#form_survey_exchange_student');
        var data = form.serialize();

        $.post(url, data, function(result) {
            if (result.code == 0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thank You..',
                    text: 'We have received your answer'
                }).then(function () {
                    window.location.reload();
                });
            }
            else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: result.message,
                });
            }
        }, 'json').fail(function(params) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Sorry your data could not be sent!, please contact the IT Department',
            });
        })
    })
})
</script>