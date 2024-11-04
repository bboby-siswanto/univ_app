<?php
foreach($this->config->item('app_environment') as $key => $val){
?>
<a href="<?=site_url('setting/set_environment/'.$val)?>">Set <?=$val?></a>
<?php
}	
?>