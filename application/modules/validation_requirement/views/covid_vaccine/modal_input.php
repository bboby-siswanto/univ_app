<div class="modal" tabindex="-1" role="dialog" id="modal-input-vaccine" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vaccine Covid-19</h5>
            </div>
            <div class="modal-body">
                <?= modules::run('personal_data/covid_certificate', null, null, true);?>
            </div>
        </div>
    </div>
</div>