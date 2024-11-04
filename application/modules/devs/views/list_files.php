<?php
$lastpath = (substr($lastpath, -1) == '/') ? $lastpath : $lastpath.'/';
$a_path = explode('/', $subdir);
?>
<div class="card">
    <div class="card-body">
    <div class="row">
    <div class="col-sm-8">
        <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url()?>devs/script_list/"><i class="fas fa-home"></i></a></li>
        <?php
        if (count($a_path) > 0) {
            $linkpath = 'devs/script_list/';
            foreach ($a_path as $key => $s_path) {
                $active = ($key == (count($a_path) - 1)) ? 'active' : '';
                $ariaactive = ($key == (count($a_path) - 1)) ? 'aria-current="page"' : '';
                $linkpath .= ($key == 0) ? '?tp='.$s_path : '/'.$s_path;
        ?>
            <li class="breadcrumb-item <?=$active;?>" <?=$ariaactive;?>><?= ($active == 'active') ? $s_path : '<a href="'.base_url().$linkpath.'">'.$s_path.'</a>' ?></li>
        <?php
            }
        }
        ?>
            <!-- <li class="breadcrumb-item"><a href="#">Library</a></li>
            <li class="breadcrumb-item active" aria-current="page">Data</li> -->
        </ol>
        </nav>
    </div>
    <div class="col-sm-4">
        <nav><div class="input-group input-group-sm">
            <input type="text" name="find_text" id="find_text" class="form-control" value="<?=$sc;?>">
            <button type="button" id="btn_find_text" class="btn btn-sm btn-success"><i class="fas fa-search"></i></button>
        </div></nav>
    </div>
</div>
    </div>
</div>
<ul class="list-group list-group-flush mb-3">
<?php
if ((isset($listing)) AND ($listing)) {
    foreach ($listing as $s_text) {
        if (substr($s_text, 0, 1) != '.') {
        ?>
            <li class="list-group-item">
        <?php
        if (is_dir($lastpath.$s_text)) {
            $target = (!empty($subdir)) ? $subdir.'/'.$s_text : $s_text;
            print('<a href="'.base_url().'devs/script_list/?tp='.$target.'">'.$s_text.'</a>');
        }
        else {
            print($s_text);
        }
        ?>
            </li>
        <?php
        }
    }
}
?>
</ul>
<script>
$(function() {
    $('#btn_find_text').on('click', function(e) {
        e.preventDefault();

        var param = $('#find_text').val();
        if (param !== '') {
            param = encodeURI(param);
            console.log(param);
            var get = '<?=base_url()?>devs/script_list/?sc=' + param;
            window.location.href = get;
        }
        console.log(param);
    })
})
</script>