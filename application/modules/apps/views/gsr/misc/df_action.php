<div class="row">
    <div class="col-12">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action Button">
            <div class="btn-group mr-2" role="group" aria-label="First Action">
<?php
if (isset($access)) {
    if (in_array($access, ['review', 'approve', 'finish'])) {
        if (($current_status == 'requested') AND ($access == 'review')) {
?>
                <button type="button" class="btn btn-success" id="submit_approve_review"><i class="fas fa-check-circle"></i> Submit Checking</button>
                <button type="button" class="btn btn-danger" id="submit_reject_review"><i class="fas fa-times-circle"></i> Reject with Note</button>
<?php
        }
        // else if ($current_status == 'reviewed') {
        else if ($current_status == 'requested') {
            if ($access == 'review') {
?>
                <button type="button" class="btn btn-info" disabled><i class="fas fa-user-check"></i> Checked</button>
<?php
            }
            else if($access == 'approve') {
?>
                <button type="button" class="btn btn-success" id="submit_approve_approval"><i class="fas fa-check-circle"></i> Approve</button>
                <button type="button" class="btn btn-danger" id="submit_reject_approval"><i class="fas fa-times-circle"></i> Reject with Note</button>
<?php
            }
        }
        else if ($current_status == 'approved') {
            if ($access == 'review') {
?>
                <button type="button" class="btn btn-info" disabled><i class="fas fa-user-check"></i> Checked</button>
<?php
            }
            else if ($access == 'approve') {
?>
                <button type="button" class="btn btn-info" disabled><i class="fas fa-user-check"></i> Approved</button>
<?php
            }
            else if ($access == 'finish') {
?>
                <button type="button" class="btn btn-success" id="submit_approve_finish"><i class="fas fa-check-circle"></i> Approve</button>
                <button type="button" class="btn btn-danger" id="submit_reject_finish"><i class="fas fa-times-circle"></i> Reject with Note</button>
<?php
            }
        }
    }
    else if (($access == 'request') AND ($df_data->df_allow_update == 'true')) {
?>
                <a href="<?=base_url()?>apps/gsr/new_request/<?=$df_data->df_id;?>" class="btn btn-warning" id="submit_edit_gsr"><i class="fas fa-edit"></i> Edit</a>
<?php
    }
}
?>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Second Action">
            <a href="<?=base_url()?>apps/gsr/df_list" class="btn btn-success" id="request_list_df"><i class="fas fa-list"></i> <?=$df_type;?> List</a>
                <button id="attachment_group" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    View Attachment
                </button>
                <div class="dropdown-menu" aria-labelledby="attachment_group">
    <?php
    if ($df_attachment) {
        foreach ($df_attachment as $o_attachment) {
    ?>
                    <a class="dropdown-item" target="_blank" href="<?=base_url().'apps/gsr/view_attachment/'.urlencode(base64_encode($o_attachment->gsr_file_id.'|'.$o_attachment->document_link));?>"><?=$o_attachment->document_name;?></a>
    <?php
        }
    }
    ?>
                    <!-- <a class="dropdown-item" href="#">Dropdown link</a>
                    <a class="dropdown-item" href="#">Dropdown link</a> -->
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-2">
    <div class="card-header">
        <?=$df_type;?> Note
    </div>
    <div class="card-body">
<?php
if ($df_remarks) {
?>
        <ul class="list-unstyled">
<?php
    foreach ($df_remarks as $o_remarks) {
?>
            <li class="media mt-3">
                <div class="media-body">
                    <div class="border-bottom">
                        <strong><span class="mt-0 mb-1"><?=ucwords(strtolower($o_remarks->personal_data_name));?> - <?= date('d F Y H:i:s', strtotime($o_remarks->df_date_remarks))?></span></strong>
                    </div>
                    <?=$o_remarks->note;?>
                </div>
            </li>
<?php
    }
?>
        </ul>
<?php
}
?>
    </div>
</div>
<div class="card mt-2">
    <div class="card-header">
        <?=$df_type;?> Data
    </div>
    <div class="card-body">
        <?=$df_view;?>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="modal_note">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-action-gsr"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" onsubmit="return false" id="form_submit_action" url="<?=base_url()?>">
                    <input type="hidden" name="df_id" id="df_id" value="<?=$df_data->df_id;?>">
                    <input type="hidden" name="action_approval" id="action_approval">
                    <div class="row mb-3 d-none" id="input-reject">
                        <div class="col-sm-6">
                            Action for <span id="user_requesting"></span>:
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-check float-right">
                                <input type="checkbox" class="form-check-input" id="request_action" name="request_action" value="repair">
                                <label class="form-check-label" for="request_action">Allow Repair <?=$df_type;?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 text-remarks">
                            Remarks for DF: 
                        </div>
                        <div class="col-sm-6">
                            <button id="add_remarks" class="btn btn-info float-right" type="button"><i class="fas fa-plus"></i> Add Note</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="table_remarks_action">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td class="w-25"></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit_action_form">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var table_remarks_action = $('#table_remarks_action').DataTable({
    "searching": false,
    "paging": false,
    "info": false,
    "ordering": false,
    "language": {
        'emptyTable': "Add note"
    }
});

$(function() {
    if ('<?=$with_action;?>' == 'approve') {
        $("#submit_approve_approval").ready(function() {
            $('button#submit_approve_approval').trigger('click');
        });
    }
    else if ('<?=$with_action;?>' == 'reject') {
        $("#submit_reject_approval").ready(function() {
            $('#submit_reject_approval').trigger('click');
        });
    }
    else if ('<?=$with_action;?>' == 'approve_review') {
        $("#submit_approve_review").ready(function() {
            $('button#submit_approve_review').trigger('click');
        });
    }
    else if ('<?=$with_action;?>' == 'reject_review') {
        $("#submit_reject_review").ready(function() {
            $('button#submit_reject_review').trigger('click');
        });
    }
    else if ('<?=$with_action;?>' == 'approve_finish') {
        $("#submit_approve_finish").ready(function() {
            $('button#submit_approve_finish').trigger('click');
        });
    }
    else if ('<?=$with_action;?>' == 'reject_finish') {
        $("#submit_reject_finish").ready(function() {
            $('button#submit_reject_finish').trigger('click');
        });
    }

    $('#add_remarks').on( 'click', function (e) {
        e.preventDefault();

        table_remarks_action.row.add( [
            '<textarea name="gsr_note[]" id="gsr_note" class="form-control"></textarea>',
            '<button id="remarks_remove_row" type="button" class="btn btn-sm btn-danger"><i class="fas fa-minus"></i></button>'
        ] ).draw(false);
    });
    $('#add_remarks').click();

    $('table#table_remarks_action tbody').on('click', 'button#remarks_remove_row', function(e) {
        e.preventDefault();
        table_remarks_action.row($(this).parents('tr')).remove().draw();
    });
    
    $('button#submit_approve_review').on('click', function(e) {
        e.preventDefault();
        
        $('#action_approval').val('approve');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_checking_df');
        $('.title-action-gsr').text('Checking <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-success">Checked</strong> Vch no: <?=$df_data->df_number;?>');
        $('#input-reject').addClass('d-none');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_review').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_checking_df');
        $('.title-action-gsr').text('Reject <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> Vch no: <?=$df_data->df_number;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$df_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_approve_approval').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('approve');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_approve');
        $('.title-action-gsr').text('Approve <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-success">Approve</strong> Vch no: <?=$df_data->df_number;?>');
        $('#user_requesting').html('<?=$df_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_approval').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_approve');
        $('.title-action-gsr').text('Reject <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> Vch no: <?=$df_data->df_number;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$df_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_approve_finish').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('approve');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_finish');
        $('.title-action-gsr').text('Approve <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-success">Approve</strong> Vch no: <?=$df_data->df_number;?>');
        $('#user_requesting').html('<?=$df_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_finish').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_finish');
        $('.title-action-gsr').text('Reject <?=$df_type;?>');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> Vch no: <?=$df_data->df_number;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$df_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('#submit_action_form').on('click', function(e) {
        e.preventDefault();
        $.blockUI({baseZ: 9000});

        var form = $("#form_submit_action");
        var url = form.attr('url');
        
        $.post(url, form.serialize(), function(result) {
            $.unblockUI();
            if (result.code == 0) {
                toastr.success('Success');
                setTimeout( function(){
                    window.location.href = '<?=base_url()?>apps/gsr/request_list';
                }  , 2000 );
            }
            else {
                toastr.warning(result.message, 'Warning!');
            }
        }, 'json').fail(function(params) {
            $.unblockUI();
            toastr.error("error processing data");
        });
    });
})
</script>