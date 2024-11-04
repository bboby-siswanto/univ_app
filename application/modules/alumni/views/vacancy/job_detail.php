<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">Details Vacancies</div>
            <div class="card-body">
                <h3><?= $o_job_vacancy_data->institution_name ?></h3>
                <span>
                <?php if(!is_null($o_job_vacancy_data->job_vacancy_site)) { ?>
                    <a href="<?= $o_job_vacancy_data->job_vacancy_site?>" target="blank"><i class="fa fa-globe"></i> <?= $o_job_vacancy_data->job_vacancy_site?></a> / 
                <?php } ?>
                <?php if(!is_null($o_job_vacancy_data->job_vacancy_email)) { ?>
                    <a href="mailto:<?= $o_job_vacancy_data->job_vacancy_email?>"><i class="fa fa-envelope"></i> <?= $o_job_vacancy_data->job_vacancy_email?></a>
                <?php } ?>
                </span>
                <p><i class="fa fa-home"></i> &nbsp; <?= $o_job_vacancy_data->address_street.' '.$o_job_vacancy_data->address_city.' '.$o_job_vacancy_data->address_province.' '.strtoupper($o_job_vacancy_data->country_name).' '.$o_job_vacancy_data->address_zipcode ?></p>
                <p>Job Title: <strong><?= $o_job_vacancy_data->ocupation_name;?></strong></p>
                <p>Job Description: <?= $real_job_description ?></p>
                <p>Requirements: <?= $real_job_requirements ?></p>
            </div>
        </div>
    </div>
</div>