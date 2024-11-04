<div class="sidebar text-uppercase pb-5">
    <nav class="sidebar-nav">
        <ul class="nav">
        <?php
        foreach($sidebar as $key => $value){
            $a_child = (array_key_exists('child', $value)) ? $value['child'] : [];

            if (count($a_child) > 0) {
        ?>
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#">
                        <i class="cui-lightbulb mr-2"></i> <?=$value['title']?>
                    </a>
                    <ul class="nav-dropdown-items">
                    <?php
                    foreach($value['child'] as $k => $v){
                    ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=$v['url']?>">
                                <i class="nav-icon icon-note"></i> <?=$v['title']?>
                            </a>
                        </li>
                    <?php
                    }
                    ?>
                    </ul>
                </li>
        <?php
            }
            else {
        ?>
            <li class="nav-item">
                <a class="nav-link" href="<?=$value['url']?>"><i class="cui-lightbulb mr-2"></i><?=$value['title']?></a>
            </li>
        <?php
            }
        }
            ?>
        </ul>
    </nav>
</div>