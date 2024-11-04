<div class="row">
<?php
if ($paslon_list) {
    foreach ($paslon_list as $o_paslon_list) {
?>
<div class="col-md-6 mt-4">
    <?= modules::run('apps/kpu/paslon_view', $o_paslon_list->paslon_id); ?>
    <input type="hidden" name="paslon_key" value="<?=$o_paslon_list->paslon_id;?>">
    <button type="button" class="btn btn-vote btn-block btn-success" data-target="<?=$o_paslon_list->paslon_id;?>">Vote</button>
</div>
<?php
    }
}
?>
</div>