<div class="row">
    <div class="col-12">
        <div class="btn-toolbar float-right" role="toolbar" aria-label="Action Button">
            <div class="btn-group mr-2" role="group" aria-label="First Action">
<?php
// print('<pre>');var_dump($access);exit;
if (isset($access)) {
    if (in_array($access, ['review', 'approve', 'finish'])) {
    // if (in_array($access, ['approve', 'finish'])) {
        
        // else {
            if ((is_null($gsr_data->personal_data_id_review)) AND ($access == 'review') AND (is_null($gsr_data->personal_data_id_approved))) {
        ?>
                <button type="button" class="btn btn-success" id="submit_approve_review"><i class="fas fa-check-circle"></i> Submit Review</button>
                <button type="button" class="btn btn-danger" id="submit_reject_review"><i class="fas fa-times-circle"></i> Reject with Note</button>
        <?php
            }
            else if ((is_null($gsr_data->personal_data_id_approved)) AND ($access == 'approve')) {
        ?>
                <button type="button" class="btn btn-success" id="submit_approve_approval"><i class="fas fa-check-circle"></i> Submit Approve</button>
                <button type="button" class="btn btn-danger" id="submit_reject_approval"><i class="fas fa-times-circle"></i> Reject with Note</button>
        <?php
            }
            else if ((is_null($gsr_data->personal_data_id_finishing)) AND ($access == 'finish')) {
        ?>
                <button type="button" class="btn btn-success" id="submit_approve_finish"><i class="fas fa-check-circle"></i> Submit Approve</button>
                <button type="button" class="btn btn-danger" id="submit_reject_finish"><i class="fas fa-times-circle"></i> Reject with Note</button>
        <?php
            }
        // }
    }
    else if ($access == 'request') {
        if ($gsr_data->gsr_allow_update == 'true') {
        ?>
            <a href="<?=base_url()?>apps/gsr/new_request/<?=$gsr_data->gsr_id;?>" class="btn btn-warning" id="edit_gsr"><i class="fas fa-edit"></i> Edit GSR</a>
        <?php
        }
    }
}
?>
            </div>
            <div class="btn-group mr-2" role="group" aria-label="Second Action">
            <a href="<?=base_url()?>apps/gsr/request_list" class="btn btn-success" id="request_list_gsr"><i class="fas fa-list"></i> Request List</a>
                <button id="attachment_group" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    View Attachment
                </button>
                <div class="dropdown-menu" aria-labelledby="attachment_group">
    <?php
    if ($gsr_attachment) {
        foreach ($gsr_attachment as $o_attachment) {
    ?>
                    <a class="dropdown-item" target="_blank" href="<?=base_url().'file_manager/download_files/'.$o_attachment->path_link.'/'.urlencode($o_attachment->document_name);?>"><?=$o_attachment->document_name;?></a>
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
        GSR Note
    </div>
    <div class="card-body">
<?php
if ($gsr_remarks) {
?>
        <ul class="list-unstyled">
<?php
    foreach ($gsr_remarks as $o_remarks) {
?>
            <li class="media mt-3">
                <div class="media-body">
                    <div class="border-bottom">
                        <strong><span class="mt-0 mb-1"><?=ucwords(strtolower($o_remarks->personal_data_name));?> - <?= date('d F Y H:i:s', strtotime($o_remarks->gsr_date_remarks))?></span></strong>
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
        GSR Form
        <div class="card-header-actions">
        <span class="badge badge-info"><?=((isset($last_status)) AND ($last_status)) ? strtoupper($last_status->status_action) : '';?></span>
        </div>
    </div>
    <div class="card-body">
        <?=$gsr_view;?>
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
                    <input type="hidden" name="gsr_id" id="gsr_id" value="<?=$gsr_data->gsr_id;?>">
                    <input type="hidden" name="action_approval" id="action_approval">
                    <div class="row mb-3 d-none" id="input-reject">
                        <div class="col-sm-6">
                            Action for <span id="user_requesting"></span>:
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-check float-right">
                                <input type="checkbox" class="form-check-input" id="request_action" name="request_action" value="repair">
                                <label class="form-check-label" for="request_action">Allow Repair GSR</label>
                            </div>
                            <!-- <select name="request_action" id="request_action" class="form-control">
                                <option value="nothing">Nothing Action</option>
                                <option value="repair">Repair GSR</option>
                                <option value="remake">Remake GSR</option>
                            </select> -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 text-remarks">
                            Remarks for GSR: 
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
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_review');
        $('.title-action-gsr').text('Review GSR');
        $('.text-remarks').html('Remark for <strong class="text-success">Reviewed</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#input-reject').addClass('d-none');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_review').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_review');
        $('.title-action-gsr').text('Reject GSR');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$gsr_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_approve_approval').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('approve');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_approve');
        $('.title-action-gsr').text('GSR Approval');
        $('.text-remarks').html('Remark for <strong class="text-success">Approval</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#user_requesting').html('<?=$gsr_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_approval').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_approve');
        $('.title-action-gsr').text('Reject GSR');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$gsr_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_approve_finish').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('approve');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_finish');
        $('.title-action-gsr').text('Approve GSR');
        $('.text-remarks').html('Remark for <strong class="text-success">Approve</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#user_requesting').html('<?=$gsr_request_data->personal_data_name;?>');

        $('#modal_note').modal('show');
    });

    $('button#submit_reject_finish').on('click', function(e) {
        e.preventDefault();

        $('#action_approval').val('reject');
        $('#form_submit_action').attr('url', '<?=base_url()?>apps/gsr/submit_finish');
        $('.title-action-gsr').text('Reject GSR');
        $('.text-remarks').html('Remark for <strong class="text-danger">Reject</strong> GSR: <?=$gsr_data->gsr_code;?>');
        $('#input-reject').removeClass('d-none');
        $('#user_requesting').html('<?=$gsr_request_data->personal_data_name;?>');

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
                $('#modal_note').modal('hide');
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