<?php
if (!isset($paste)) {
    $nomor_urut_paslon = 0;
    $nama_ketua_paslon = '-';
    $nama_wakil_paslon = '-';
    $vision_paslon = '-';
    $mission_paslon = '-';
    $img_ketua_paslon = base_url().'apps/kpu/view_pict';
    $img_wakil_paslon = base_url().'apps/kpu/view_pict';
}
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body border border-primary rounded text-center">
                <h3 id="nomor_urut_view"><?=$nomor_urut_paslon;?></h3>
                <hr class="border-primary">
                <h4 id="ketua_name"><?=$nama_ketua_paslon;?></h4>
                <h5>&</h5>
                <h4 id="wakil_name"><?=$nama_wakil_paslon;?></h4>
                <div class="row">
                    <div class="col-6">
                        <img id="img-ketua" class="img-fluid rounded" src="<?=$img_ketua_paslon?>" alt="Pict">
                    </div>
                    <div class="col-6">
                        <img id="img-wakil" class="img-fluid rounded" src="<?=$img_wakil_paslon?>" alt="Pict">
                    </div>
                </div>
                <h4 class="pt-4">Vision</h4>
                <p id="visi_view" style="white-space: pre-line"><?=$vision_paslon;?></p>
                <h4>Mision</h4>
                <p id="misi_view" style="white-space: pre-line"><?=$mission_paslon;?></p>
            </div>
        </div>
    </div>
</div>