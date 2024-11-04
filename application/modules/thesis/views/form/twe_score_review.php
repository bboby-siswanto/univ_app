<?php
$s_evaluation_grade = ($evaluation_format) ? $evaluation_format[0]->grade : 0;
$s_evaluation_working_process_grade = ($evaluation_working_process) ? $evaluation_working_process[0]->grade : 0;
$s_evaluation_subject_grade = ($evaluation_subject) ? $evaluation_subject[0]->grade : 0;
$s_evaluation_potential_user_grade = ($evaluation_potential_user) ? $evaluation_potential_user[0]->grade : 0;
$s_evaluation_content_grade = ($evaluation_content) ? $evaluation_content[0]->grade : 0;

$s_total_grade = $s_evaluation_grade + $s_evaluation_working_process_grade + $s_evaluation_subject_grade + $s_evaluation_potential_user_grade + $s_evaluation_content_grade;
?>
<h4>Total Score: <?=$s_total_grade;?></h4>
<hr>
<div class="card">
    <div class="card-header">
        Format (format Aspects, Layout)
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Text Style</td>
                    <td class="w-50"><?=($evaluation_format) ? $evaluation_format[0]->text_style : '';?></td>
                </tr>
                <tr>
                    <td>Summary (complete)</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->summary : '';?></td>
                </tr>
                <tr>
                    <td>Chapter Structure</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->chapter_structur : '';?></td>
                </tr>
                <tr>
                    <td>Citations</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->citations : '';?></td>
                </tr>
                <tr>
                    <td>Table and Figure</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->table_figure : '';?></td>
                </tr>
                <tr>
                    <td>Layout</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->layout : '';?></td>
                </tr>
                <tr>
                    <td>References</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->reference : '';?></td>
                </tr>
                <tr>
                    <td>Score</td>
                    <td><?=($evaluation_format) ? $evaluation_format[0]->grade : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
    Working Process (based on the thesis log)
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Identification of difficulty/problems</td>
                    <td class="w-50"><?=($evaluation_working_process) ? $evaluation_working_process[0]->identification_problem : '';?></td>
                </tr>
                <tr>
                    <td>Independence</td>
                    <td><?=($evaluation_working_process) ? $evaluation_working_process[0]->independence : '';?></td>
                </tr>
                <tr>
                    <td>Progress</td>
                    <td><?=($evaluation_working_process) ? $evaluation_working_process[0]->progress : '';?></td>
                </tr>
                <tr>
                    <td>Score</td>
                    <td><?=($evaluation_working_process) ? $evaluation_working_process[0]->grade : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
    Subject of Thesis
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Identification of Aims and Objectives</td>
                    <td class="w-50"><?=($evaluation_subject) ? $evaluation_subject[0]->identification_objective : '';?></td>
                </tr>
                <tr>
                    <td>Thesis reflects a solid understanding of the specific topic</td>
                    <td><?=($evaluation_subject) ? $evaluation_subject[0]->understanding_specific_topic : '';?></td>
                </tr>
                <tr>
                    <td>Method and Project Plan</td>
                    <td><?=($evaluation_subject) ? $evaluation_subject[0]->method_project_plan : '';?></td>
                </tr>
                <tr>
                    <td>Dificulty of Thesis (low, middle, high)</td>
                    <td><?=($evaluation_subject) ? $evaluation_subject[0]->thesis_dificulty : '';?></td>
                </tr>
                <tr>
                    <td>Have similar theses presented earlier?</td>
                    <td><?=($evaluation_subject) ? $evaluation_subject[0]->similar_thesis : '';?></td>
                </tr>
                <tr>
                    <td>Score</td>
                    <td><?=($evaluation_subject) ? $evaluation_subject[0]->grade : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
    Value for potential users
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Thesis is applicable and has a value for potential user</td>
                    <td class="w-50"><?=($evaluation_potential_user) ? $evaluation_potential_user[0]->applicable_for_user : '';?></td>
                </tr>
                <tr>
                    <td>What is benefit for potential user?</td>
                    <td><?=($evaluation_potential_user) ? $evaluation_potential_user[0]->benefit_for_user : '';?></td>
                </tr>
                <tr>
                    <td>Would you employ the student based on his/her thesis?</td>
                    <td><?=($evaluation_potential_user) ? $evaluation_potential_user[0]->will_employ_student : '';?></td>
                </tr>
                <tr>
                    <td>Score</td>
                    <td><?=($evaluation_potential_user) ? $evaluation_potential_user[0]->grade : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header">
    Content (Academic Value)
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Problem Statement/Introdution</td>
                    <td class="w-50"><?=($evaluation_content) ? $evaluation_content[0]->problem_statement : '';?></td>
                </tr>
                <tr>
                    <td>Objectives/Research questions</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->research_question : '';?></td>
                </tr>
                <tr>
                    <td>Theoritical/Analitycal framework</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->analytical_framework : '';?></td>
                </tr>
                <tr>
                    <td>Methods</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->methods : '';?></td>
                </tr>
                <tr>
                    <td>Results</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->result : '';?></td>
                </tr>
                <tr>
                    <td>Discussions</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->discussion : '';?></td>
                </tr>
                <tr>
                    <td>Conclusion</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->conclusion : '';?></td>
                </tr>
                <tr>
                    <td>Literature</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->literature : '';?></td>
                </tr>
                <tr>
                    <td>Have existing infrastructure in IULI been used?</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->iuli_infrastructure : '';?></td>
                </tr>
                <tr>
                    <td>Score</td>
                    <td><?=($evaluation_content) ? $evaluation_content[0]->grade : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>