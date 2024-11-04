<div class="row">
    <div class="col-12">
        <div class="w-50 pb-3">
            <button type="button" id="edit_evote" class="btn btn-success" data-toggle="modal" data-target="#modal_update_vote">Settings / Update</button>
            <a href="<?=base_url()?>apps/kpu/view_result" target="_blank" class="btn btn-info">Live Count</a>
        </div>
    </div>
</div>
<?php
if ((isset($vote_period)) AND ($vote_period)) {
    $o_vote = $vote_period[0];
?>
<div class="card">
    <div class="card-body">
    <?php
        if ((isset($view_mode)) AND ($view_mode == 'update')) {
    ?>
        <input type="text" class="form-control h-10" name="voting_period_name" id="voting_period_name" placeholder="Voting Name" value="<?=$o_vote->period_name;?>">
    <?php
        }
        else {
    ?>
        <h2><?= $o_vote->period_name;?></h2>
    <?php
        }
    ?>
        <hr>
        <div class="row">
            <div class="col-sm-2">
                Voting Datetime:
            </div>
            <div class="col-sm-7">
                : <?= date('d F Y H:i', strtotime($o_vote->period_voting_start)).' - '.date('d F Y H:i', strtotime($o_vote->period_voting_end)); ?>
    <?php
        if ((isset($view_mode)) AND ($view_mode == 'update')) {
    ?>
                
    <?php
        }
    ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                Candidate
            </div>
            <div class="col-sm-7">:
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="paslon_list" class="table">
                        <thead class="bg-dark">
                            <tr>
                                <th>No.</th>
                                <th>Chairman</th>
                                <th>Vice-Chairman</th>
                                <th>Vision</th>
                                <th>Mision</th>
                                <th>Action</th>
                            </tr>
                        </thead>
            <?php
            if ((isset($paslon_list)) AND ($paslon_list)) {
                print('<tbody>');
                foreach ($paslon_list as $o_paslon) {
            ?>
                        <tr>
                            <td><?=$o_paslon->nomor_urut;?></td>
                            <td><?=(is_null($o_paslon->chairman_personal_name)) ? 'Kotak Kosong' : $o_paslon->chairman_personal_name;?></td>
                            <td><?=(is_null($o_paslon->vice_chairman_personal_name)) ? 'Kotak Kosong' : $o_paslon->vice_chairman_personal_name;?></td>
                            <td><?=(is_null($o_paslon->vision)) ? 'N/A' : $o_paslon->vision;?></td>
                            <td><?=(is_null($o_paslon->mision)) ? 'N/A' : $o_paslon->mision;?></td>
                            <td>
                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <button type="button" id="btn-action-paslon" class="btn btn-success" data-uniqid="<?=$o_paslon->paslon_id;?>">Edit</button>
                                <button type="button" id="btn-action-view" class="btn btn-info" data-uniqid="<?=$o_paslon->paslon_id;?>">view</button>
                            </div>
                            </td>
                        </tr>
            <?php
                }
                print('</tbody>');
            }
            ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
else {
?>
<div class="card">
    <div class="card-body">
        <h2 class="text-center">No active voting</h2>
    </div>
</div>
<?php
}
?>
<div class="modal" tabindex="-1" role="dialog" id="modal_update_vote">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Voting</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form url="<?=base_url()?>apps/kpu/update_voting" id="form_voting_update" onsubmit="return false">
                    <input type="hidden" name="dataid" value="<?=$o_vote->period_id;?>">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="voting_period_name">Title</label>
                                <input type="text" class="form-control h-10" name="voting_period_name" id="voting_period_name" placeholder="Voting Name" value="<?=$o_vote->period_name;?>">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Voting Datetime</label>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="date" class="form-control" placeholder="Start Voting Date" name="period_vote_start" id="period_vote_start" value="<?=date('Y-m-d', strtotime($o_vote->period_voting_start));?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group mb-3">
                                                    <select name="period_vote_hour_start" id="period_vote_hour_start" class="form-control">
                                                <?php
                                                for ($h=0; $h < 24; $h++) { 
                                                    $selected_hour = ((!is_null($o_vote->period_voting_start)) AND (date('H', strtotime($o_vote->period_voting_start)) == $h)) ? 'selected="selected"' : '';
                                                    $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
                                                    print('<option value="'.$hour.'" '.$selected_hour.'>'.$hour.'</option>');
                                                }
                                                ?>
                                                    </select>
                                                    <select name="period_vote_minute_start" id="period_vote_minute_start" class="form-control w-20">
                                                <?php
                                                for ($m=0; $m < 60; $m++) { 
                                                    $selected_minute = ((!is_null($o_vote->period_voting_start)) AND (date('i', strtotime($o_vote->period_voting_start)) == $m)) ? 'selected="selected"' : '';
                                                    $minute = str_pad($m, 2, '0', STR_PAD_LEFT);
                                                    print('<option value="'.$minute.'"'.$selected_minute.'>'.$minute.'</option>');
                                                }
                                                ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        to
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="date" class="form-control" placeholder="End Voting Date" name="period_vote_end" id="period_vote_end" value="<?=date('Y-m-d', strtotime($o_vote->period_voting_end));?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group mb-3">
                                                    <select name="period_vote_hour_end" id="period_vote_hour_end" class="form-control">
                                                <?php
                                                for ($h=0; $h < 24; $h++) { 
                                                    $selected_hour = ((!is_null($o_vote->period_voting_end)) AND (date('H', strtotime($o_vote->period_voting_end)) == $h)) ? 'selected="selected"' : '';
                                                    $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
                                                    print('<option value="'.$hour.'" '.$selected_hour.'>'.$hour.'</option>');
                                                }
                                                ?>
                                                    </select>
                                                    <select name="period_vote_minute_end" id="period_vote_minute_end" class="form-control">
                                                <?php
                                                for ($m=0; $m < 60; $m++) { 
                                                    $selected_minute = ((!is_null($o_vote->period_voting_end)) AND (date('i', strtotime($o_vote->period_voting_end)) == $m)) ? 'selected="selected"' : '';
                                                    $minute = str_pad($m, 2, '0', STR_PAD_LEFT);
                                                    print('<option value="'.$minute.'" '.$selected_minute.'>'.$minute.'</option>');
                                                }
                                                ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_period_vote">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal-paslon">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chairman and Vice-Chairman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('apps/kpu/paslon_form');?>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal-view-paslon">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chairman and Vice-Chairman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('apps/kpu/paslon_view');?>
            </div>
        </div>
    </div>
</div>
<script>
var table_paslon_list = $('table#paslon_list').DataTable({
    paging: false,
    bInfo: false,
    dom: 'Bfrtip',
    ordering: false,
    buttons: [
        {
            text: 'Add Candidate',
            action: function ( e, dt, node, config ) {
                $('input#paslon_id').val('');
                $('input#ketua_file_text').val('');
                $('input#wakil_file_text').val('');
                $('select#ketua_student_id').val('').trigger('change');
                $('select#wakil_student_id').val('').trigger('change');
                $('input#nomor_urut').val('');
                $('textarea#vision').val('');
                $('textarea#mission').val('');
                $('#modal-paslon').modal('show');
            }
        }
    ]
});
$(function() {
    $('table#paslon_list tbody').on('click', 'button#btn-action-paslon', function(e) {
        e.preventDefault();

        var data_unique = $(this).attr('data-uniqid');
        $.post('<?=base_url()?>apps/kpu/get_paslon', {data_uniq: data_unique}, function(result) {
            if (result) {
                $('input#paslon_id').val(result.paslon_id);
                $('input#ketua_file_text').val(result.paslon_chairman_pict);
                $('input#wakil_file_text').val(result.paslon_vice_chairman_pict);
                $('select#ketua_student_id').val(result.paslon_chairman).trigger('change');
                $('select#wakil_student_id').val(result.paslon_vice_chairman).trigger('change');
                $('input#nomor_urut').val(result.nomor_urut);
                $('textarea#vision').val(result.vision);
                $('textarea#mission').val(result.mision);
                $('#modal-paslon').modal('show');
            }
            else {
                toastr.warning('Paslon data not found!');
            }
        }, 'json').fail(function(params) {
            toastr.error('Error processing data!');
        });
    });

    $('table#paslon_list tbody').on('click', 'button#btn-action-view', function(e) {
        e.preventDefault();

        var data_unique = $(this).attr('data-uniqid');
        $.post('<?=base_url()?>apps/kpu/get_paslon', {data_uniq: data_unique}, function(result) {
            if (result) {
                var img_default = "<?=base_url()?>apps/kpu/view_pict";
                var nomor = (result.nomor_urut == null) ? '0' : result.nomor_urut;
                var chairman_personal_name = (result.chairman_personal_name == null) ? 'Kotak Kosong' : result.chairman_personal_name;
                var vice_chairman_personal_name = (result.vice_chairman_personal_name == null) ? 'Kotak Kosong' : result.vice_chairman_personal_name;
                var paslon_chairman_pict = (result.paslon_chairman_pict == null) ? img_default : img_default + '/' + result.period_id + '/' + result.paslon_chairman_pict;
                var paslon_vice_chairman_pict = (result.paslon_vice_chairman_pict == null) ? img_default : img_default + '/' + result.period_id + '/' +  result.paslon_vice_chairman_pict;
                var visi = (result.vision == null) ? 'N/A' : result.vision;
                var misi = (result.mision == null) ? 'N/A' : result.mision;
                $('#nomor_urut_view').text(nomor);
                $('#ketua_name').text(chairman_personal_name);
                $('#wakil_name').text(vice_chairman_personal_name);
                $('#img-ketua').attr('src', paslon_chairman_pict);
                $('#img-wakil').attr('src', paslon_vice_chairman_pict);
                $('#visi_view').text(visi);
                $('#misi_view').text(misi);
                $('#modal-view-paslon').modal('show');
            }
            else {
                toastr.warning('Paslon data not found!');
            }
        }, 'json').fail(function(params) {
            toastr.error('Error processing data!');
        });
    });

    $('button#submit_period_vote').on('click', function(e) {
        var form = $('#form_voting_update');
        var url = form.attr('url');
        var data = form.serialize();

        $.post(url, data, function(result) {
            if (result.code == '0') {
                $('#modal_update_vote').modal('hide');
                toastr.success('Success!');
                setTimeout( function(){ 
                    location.reload(); 
                }  , 3000 );
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            toastr.error('Error processing your data!');
        });
    });
})
</script>