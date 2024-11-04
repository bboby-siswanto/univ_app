<!DOCTYPE html>
<html>
<head>
	<title>IULI Portal</title>
	<style type="text/css">
        @page {
            margin: 0mm;
            margin-header: 0mm !important;
            margin-footer: 0mm;
        }
        html {
            background-color: #ffffff;
        }
		.pdt {
			padding-top: 2.5mm !important;
		}
        .ftb {
            position: absolute;
            bottom: 16px;
            z-index: 0;
        }
        .font-call {
            font-family: calibrib;
        }
		.header_letter {
            width: 100%;
            font-size: 13.3px;
            margin-top: 5mm;
            margin-left: 24.5mm;
            /* text-align:justify; */
		}
        .header_letter td {
            padding :0px !important;
        }
        .text-center {
            width: 100%;
            text-align: center;
        }
        .head_body {
            margin-top: 21mm;
            letter-spacing: 0.05mm;
        }
        .body_letter {
            z-index: 999;
            width: 100%;
            font-size: 13.3px;
            margin-top: 9mm;
            margin-left: 24.5mm;
            text-align:justify;
        }
        .body_letter_personal_top {
            padding-bottom: 9mm;
        }
        .body_letter_personal {
            padding-left: 13mm;
            padding-top: 1.2mm;
            padding-bottom: 1.2mm;
        }
        .body_letter_personal_fill {
            padding-top: 1.2mm;
            padding-bottom: 1.2mm;
        }
        .body_letter_personal_bottom {
            padding-top: 6mm;
            padding-right: 29mm;
            line-height: 1.8;
        }
        .footer_letter_personal_bottom {
            padding-top: 22mm;
        }
        .left_letter {
            margin-left: 25.5mm;
        }
        .footer_deans {
            padding-top: 25mm;
            /* margin-left: 25.5mm; */
            font-size: 13px;
        }
        .footer_letter {
            margin-left: 25.5mm;
            color: #706f74;
            font-size: 11.4px;
            letter-spacing: 0.5px;
            padding-top: 24mm;
        }
	</style>
</head>
<body>
	<div class="pdt">
		<img src="<?=base_url()?>assets/img/header_of_letter.jpg" width="100%">
	</div>
	<div class="font-call">
		<table class="header_letter">
			<tr>
				<td width="85px">No</td>
				<td>: <?= $number_input;?></td>
			</tr>
			<tr>
				<td>Date/Rev.</td>
				<td>: <?= $date_input;?></td>
			</tr>
			<tr>
				<td>Page</td>
				<td>: 1 of 1</td>
			</tr>
			<tr>
				<td>Re</td>
				<td>: Reference Letter </td>
			</tr>
		</table>
        <div class="text-center head_body">
            <center><strong>TO WHOM IT MAY CONCERN</strong></center>
        </div>
        <table class="body_letter">
            <tr>
                <td colspan="2" class="body_letter_personal_top">
                    The Head of Study Program, International University Liaison Indonesia certifies that,
                </td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Name</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? $student_data->personal_data_name : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Student ID No.</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? $student_data->student_number : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Study Program</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? $student_data->study_program_name : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Faculty</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? str_replace('Faculty of ', '', $student_data->faculty_name) : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Gender</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? (($student_data->personal_data_gender == 'M') ? 'Male' : 'Female') : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Place/Date of Birth</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? $student_data->personal_data_place_of_birth : '-' ?> / <?= ($student_data) ? date('d F Y', strtotime($student_data->personal_data_date_of_birth)) : '-' ?></td>
            </tr>
            <tr>
                <td class="body_letter_personal ft-14" width="45mm">Citizenship</td>
                <td class="body_letter_personal_fill">: <?= ($student_data) ? $student_data->citizenship_country_name : '-' ?></td>
            </tr>
            <tr>
                <td colspan="2" class="body_letter_personal_bottom">
                    <p>is  registered  as  a student  in  International  University Liaison  Indonesia  (IULI)  from  academic year <?= ($student_data) ? $student_data->academic_year_id.'/'.(intval($student_data->academic_year_id) + 1) : '' ?> up  to  now.
                    If  you  have any further questions, please contact us in IULI.</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="body_letter_personal_bottom">
                    <p>Thank you very much for your kind attention.</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="footer_letter_personal_bottom">Sincerely,</td>
            </tr>
        </table>
        <div class="footer_deans left_letter">
            <div class="deans"><strong><?= ($hod_data) ? $hod_name : '' ?></strong></div>
            <div class="faculty">Head of Study Program of <?= ($student_data) ? $student_data->study_program_name : '' ?></div>
            <div class="deans_mail">email. 
                <?= ($hod_data) ? '<a href="mailto:'.$hod_data->employee_email.'">'.$hod_data->employee_email.'</a>' : '' ?>
            </div>
        </div>
        <div class="footer_letter">Page <strong>1</strong>of <strong>1</strong></div>
	</div>
	<div class="ftb">
		<img src="<?=base_url()?>assets/img/footer_of_letter.png" width="100%">
	</div>
</body>
</html>