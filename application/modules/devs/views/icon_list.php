<h3>Icon List</h3>
<hr>
<div class="row">
<?php
if ((isset($fontlist)) AND (is_array($fontlist))) {
    foreach ($fontlist as $fa => $s_icon) {
?>
    <div class="col-1 mt-1">
        <i class="<?=$s_icon;?> fa-4x"></i><br>
        <code><?=trim($s_icon);?></code>
    </div>
<?php
    }
}
?>
</div>