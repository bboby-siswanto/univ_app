<div class="modal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" id="modal_voting">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?= modules::run('apps/kpu/vote_view'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $('#modal_voting').modal('show');

    $(function() {
        $('button.btn-vote').on('click', function(e) {
            e.preventDefault();
            $(this).attr('disabled', 'disabled');
            $.blockUI({ baseZ: 2000 });
            var data_key = $(this).attr('data-target');

            $.post('<?=base_url()?>apps/kpu/submit_vote', {target: data_key}, function(result) {
                $(this).removeAttr('disabled');
                $.unblockUI();

                if (result.code == 0) {
                    toastr.success('Thanks!', 'success');

                    $('#modal_voting').modal('hide');
                    $("#modal_voting").remove();
                }
                else {
                    toastr.warning(result.message, 'Warning!');
                }
            }, 'json').fail(function(params) {
                $(this).removeAttr('disabled');
                $.unblockUI();
                toastr.error('Error Processing data!');
            });
        });
    });
</script>