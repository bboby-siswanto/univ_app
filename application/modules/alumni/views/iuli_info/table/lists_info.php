<div class="table-responsive">
    <table id="table_lists_info" class="table">
        <thead class="d-none">
            <tr><th></th></tr>
        </thead>
        <tbody>
    <?php
        if ($o_info_data) {
            foreach ($o_info_data as $info) {
    ?>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-header"><?= $info->info_title;?></div>
                        <div class="card-body">
                            <small>
                                <i class="fa fa-user"></i> <?= $info->personal_data_name;?>, <i class="fa fa-clock-o"></i> <?= $info->date_added;?>
                            </small>
                            <p><?= nl2br($info->info_message);?></p>
                        </div>
                    </div>
                </td>
            </tr>
    <?php
            }
        }
    ?>
        </tbody>
    </table>
</div>
<script>
    $(function() {
        var table_info = $('#table_lists_info').DataTable({
            lengthChange: false
        });
    });
</script>