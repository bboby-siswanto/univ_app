<table class="table table-bordered">
    <thead>
        <tr>
            <th>Owing By Student</th>
            <th>IDR</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Estimated income from arrears previous semester</td>
            <td class="text-right" id="summ_arrears_prev_semester"></td>
        </tr>
        <tr>
            <td>Estimated income from arrears selected semester (<?=$prev_installment;?>)</td>
            <td class="text-right" id="arrears_current_semester_previous"></td>
        </tr>
<?php
// if ($semester_selected == $current_selected) {
?>
        <tr>
            <td>Estimated income from arrears selected semester (<?=$current_month_installment;?>)</td>
            <td class="text-right" id="arrears_current_semester_current_month"></td>
        </tr>
        <tr>
            <td>Estimated income from arrears selected semester until end semester</td>
            <td class="text-right" id="arrears_current_semester_until_last"></td>
        </tr>
<?php
// }
?>
        <tr>
            <td class="font-weight-bold">Total Owed (Active Student)</td>
            <td class="font-weight-bold text-right" id="total_owed_active"></td>
        </tr>
        <tr>
            <td class="font-weight-bold">Total Owed (Graduated Student)</td>
            <td class="font-weight-bold text-right" id="total_owed_graduated"></td>
        </tr>
        <tr>
            <td class="font-weight-bold">Total Owed</td>
            <td class="font-weight-bold text-right" id="total_owed"></td>
        </tr>
    </tbody>
</table>