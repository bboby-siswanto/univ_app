<div class="row">
<?php
if ($paslon_list) {
    foreach ($paslon_list as $o_paslon_list) {
?>
<div class="col-md-6 mb-4 text-center">
<h3 id="result_vote_paslon_<?=$o_paslon_list->paslon_id;?>" class="bg-success p-2"><?=$o_paslon_list->paslon_result;?></h3>
<?= modules::run('apps/kpu/paslon_view', $o_paslon_list->paslon_id); ?>
</div>
<?php
    }
?>
<script>
    setInterval(function(){
        get_result();
    }, 3000);
    
    function get_result() {
        $.post('<?=base_url()?>apps/kpu/get_result', {period_id: '<?=$vote_period->period_id;?>'}, function(result) {
            if (result.data) {
                $.each(result.data, function(i, v) {
                    $('#result_vote_paslon_' + v.paslon_id).text(v.paslon_result);
                });
            }
            // console.log(result.data.length);
        }, 'json');
    }
</script>
<?php
}
?>
</div>