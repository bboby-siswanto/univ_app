<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header bg-dark">Job Vacancy Lists</div>
        </div>
    </div>
</div>
<?php
foreach ($o_job_lists as $job_lists) {
    $s_requirements = '';
    if (!is_null($job_lists->requirements)) {
        $a_requirements = explode(PHP_EOL, $job_lists->requirements);
        $s_requirements .= '<ul>';
        foreach ($a_requirements as $requirements) {
            $s_requirements .= '<li>'.$requirements.'</li>';
        }
        $s_requirements .= '</ul>';
    }

    $s_jobdesc = '';
    if (!is_null($job_lists->job_description)) {
        $a_description = explode(PHP_EOL, $job_lists->job_description);
        $s_jobdesc .= '<ul>';
        foreach ($a_description as $description) {
            $s_jobdesc .= '<li>'.$description.'</li>';
        }
        $s_jobdesc .= '</ul>';
    }
?>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header"><?= $job_lists->institution_name ?></div>
                <div class="card-body table-responsive">
                    <input type="hidden" value="<?= $job_lists->job_vacancy_id;?>">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="bg-dark" width="20%">Company Name</td>
                                <td><?= $job_lists->institution_name?></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Company Address</td>
                                <td><?= $job_lists->address_street?></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Job Title</td>
                                <td><?= $job_lists->ocupation_name?></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Job Description</td>
                                <td><?= $s_jobdesc?></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Requirements</td>
                                <td><?= $s_requirements ?></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Sites</td>
                                <td><a href="<?= $job_lists->job_vacancy_site?>" target="blank"><?= $job_lists->job_vacancy_site?></a></td>
                            </tr>
                            <tr>
                                <td class="bg-dark">Email</td>
                                <td><a href="mailto:<?= $job_lists->job_vacancy_email?>"><?= $job_lists->job_vacancy_email?></a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>