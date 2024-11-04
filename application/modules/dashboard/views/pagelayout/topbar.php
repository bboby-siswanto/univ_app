<ul class="nav navbar-nav <?=($layout_bar == 'topmenu') ? 'd-md-down-none' : '' ?>">
<?php
if ($topbar) {
    foreach($topbar as $value){
?>
        <li class="nav-item px-3">
            <a class="nav-link" href="<?=$value['url']?>"><?=$value['title']?></a>
        </li>
<?php
    }
}
?>
</ul>