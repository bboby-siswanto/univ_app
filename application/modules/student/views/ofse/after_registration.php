<div class="card">
    <div class="card-body">
        <p>Please make sure you have chosen the right subjects right.</p>
        <p>These are the subjects that have been selected::</p>
        <p id="subject_list">
            <ol>
        <?php
        if ((isset($ofse_data)) AND ($ofse_data)) {
            foreach ($ofse_data as $o_ofse) {
        ?>
                <li><?=$o_ofse->subject_name;?></li>
        <?php
            }
        }
        ?>
            </ol>
        </p>
        <br>
        <p>
            <a href="<?=base_url()?>student/ofse/registration/true">Click here</a> for update
        </p>
    </div>
</div>