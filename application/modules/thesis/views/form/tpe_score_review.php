<div class="card">
    <div class="card-header">
    Evaluation Criteria
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td>Presentation Performance</td>
                    <td class="w-50"><?=($thesis_presentation) ? $thesis_presentation[0]->presentation_remarks : '';?></td>
                </tr>
                <tr>
                    <td>Presentation Performance Score </td>
                    <td><?=($thesis_presentation) ? $thesis_presentation[0]->presentation_score : '';?></td>
                </tr>
                <tr>
                    <td>Argumentation Performance</td>
                    <td><?=($thesis_presentation) ? $thesis_presentation[0]->argumentation_remarks : '';?></td>
                </tr>
                <tr>
                    <td>Argumentation Performance Score</td>
                    <td><?=($thesis_presentation) ? $thesis_presentation[0]->argumentation_score : '';?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>