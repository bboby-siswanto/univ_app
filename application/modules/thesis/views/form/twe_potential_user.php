<h4>Value for potential users. Comments maximum 250 characters</h4>
<hr>
<form url='<?=base_url()?>thesis/submit_twe_potential_user' id="form_submit_twe_potential_user" onsubmit="return false">
<input type="hidden" name="thesis_defense_id" value="<?=$thesis_defense[0]->thesis_defense_id;?>">
<ul class="list-group">
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Thesis is applicable and has a value for potential user</div>
            <div class="col-sm-8">
                <textarea name="applicable_for_user" id="applicable_for_user" class="form-control"><?=($evaluation_potential_user_data) ? $evaluation_potential_user_data->applicable_for_user : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">What is benefit for potential user?</div>
            <div class="col-sm-8">
                <textarea name="benefit_for_user" id="benefit_for_user" class="form-control"><?=($evaluation_potential_user_data) ? $evaluation_potential_user_data->benefit_for_user : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Would you employ the student based on his/her thesis?</div>
            <div class="col-sm-8">
                <textarea name="will_employ_student" id="will_employ_student" class="form-control"><?=($evaluation_potential_user_data) ? $evaluation_potential_user_data->will_employ_student : ''?></textarea>
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-4">Grade 0 - 30</div>
            <div class="col-sm-8">
                <input type="number" name="grade_potential_user" id="grade_potential_user" class="form-control w-50" value="<?=($evaluation_potential_user_data) ? $evaluation_potential_user_data->grade : ''?>" max="30">
            </div>
        </div>
    </li>
    <li class="list-group-item">
        <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-left" id="btn_twe_potential_user_prev">Prev Page</button>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btn_twe_potential_user_submit">Next Page</button>
            </div>
        </div>
    </li>
</ul>
</form>
<script>
$(function() {
    $('#btn_twe_potential_user_prev').on('click', function(e) {
        e.preventDefault();

        $('#pills-evaluation-subject-tab').click();
    });
    
    $('#btn_twe_potential_user_submit').on('click', function(e) {
        e.preventDefault();
        $.blockUI();

        var form = $('form#form_submit_twe_potential_user');
        var data = form.serialize();
        var url = form.attr('url');
        
        $.post(url, data, function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                $('#pills-evaluation-content-tab').click();
            }
            else {
                toastr.warning(result.message);
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error('Error processing your data!');
        });
    });
});
</script>