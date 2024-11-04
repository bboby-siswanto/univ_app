<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            Pages Lists
            <div class="card-header-actions">
                <button class="card-header-action btn btn-link" id="btn_new_pages">
                    <i class="fa fa-plus"></i> New Pages
                </button>
            </div>
        </div>
        <div class="card-body">
            <?=modules::run('devs/pages/pages_table');?>
        </div>
    </div>
</div>

<div id="pages_input" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Pages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?=modules::run('devs/pages/form_input');?>
            </div>
            <div class="modal-footer">
                <button id="save_pages" type="button" class="btn btn-primary">Save changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('button#btn_new_pages').on('click', function(e) {
            e.preventDefault();
            $('#pages_id').val('');
            $('#pages_name').val('');
            $('#pages_uri').val('');
            $('div#pages_input').modal('show');
        });
    });
</script>