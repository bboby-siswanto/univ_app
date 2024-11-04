<?php
class Text_template extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('finance/Invoice_model', 'Im');
    }

    public function additional_coupon_billing($a_param)
    {
        $s_invoice_number = $a_param['invoice_number'];
        $s_personal_data_name = $a_param['personal_data_name'];
        $s_study_program_name = $a_param['study_program_name'];
        $s_vanumber = implode(' ', str_split($a_param['sub_invoice_details_va_number'], 4));
        $s_payment_amount = number_format($a_param['sub_invoice_details_amount_total'], 0, ',', '.');
        $s_deadline = date('d F Y', strtotime($a_param['sub_invoice_details_deadline']));

        $s_desc = '';
        if ($a_param['invoice_details']) {
            $s_desc .= '<ul>';
            foreach ($a_param['invoice_details'] as $o_invoice_detail) {
                $s_desc.= '<li>'.$o_invoice_detail->fee_description.((!is_null($o_invoice_detail->fee_alt_description)) ? ' ('.$o_invoice_detail->fee_alt_description.')' : '').'</li>';
            }
            $s_desc .= '</ul>';
        }
        // print('<pre>');var_dump($a_param);exit;

        $s_html = <<<TEXT
Dear {$s_personal_data_name},
Greetings from IULI.

Kindly remind you for your next payment:
Invoice Number: {$s_invoice_number}
Student Name: {$s_personal_data_name}
Study Program: {$s_study_program_name}
Description: {$s_desc}

Beneficiary Name: {$s_personal_data_name}
Bank: BNI 46 Virtual Account.
Account Number: {$s_vanumber}
Amount: Rp. {$s_payment_amount},-
Payment Deadline: {$s_deadline}

Note:
•	BNI will reject payment which is not at the exact amount and account as stated above
•	You must bring your receipt payment / payment screen captured in exchange for your Coupon
•	Non refundable Invoice.

Best regards,
IULI Finance Dept.
TEXT;

        return $s_html;
    }

    function rules_payment($s_payment_type_code = '02') {
        $a_rules = [
            "BNI will reject payment if it's not the exact <b>minimum payment amount</b> and account as stated above.",
            "If payment for the Full Payment Method is past due date or the first payment is less than the full payment amount, the system will automatically use the installment method.",
            "If students make a payment without using a virtual account, then we assume no payment has been made.",
            "Please contact the finance department if you have already paid."
        ];
        
        if ($s_payment_type_code == '02') {
            array_unshift($a_rules, "Failure to pay Tuition Fee on time, will lead to a penalty of IDR. 500.000,- per month.");
        }
        
        return $a_rules;
    }

    public function graduation_billing($a_param)
    {
        $s_invoice_number = $a_param['invoice_number'];
        $s_personal_data_name = $a_param['personal_data_name'];
        $s_study_program_name = $a_param['study_program_name'];
        $s_vanumber = implode(' ', str_split($a_param['sub_invoice_details_va_number'], 4));
        $s_payment_amount = number_format($a_param['sub_invoice_details_amount_total'], 0, ',', '.');
        $s_deadline = date('d F Y', strtotime($a_param['sub_invoice_details_deadline']));

        $s_desc = '';
        if ($a_param['invoice_details']) {
            $s_desc .= '<ul>';
            foreach ($a_param['invoice_details'] as $o_invoice_detail) {
                $s_desc.= '<li>'.$o_invoice_detail->fee_description.((!is_null($o_invoice_detail->fee_alt_description)) ? ' ('.$o_invoice_detail->fee_alt_description.')' : '').'</li>';
            }
            $s_desc .= '</ul>';
        }
        // print('<pre>');var_dump($a_param);exit;

        $s_html = <<<TEXT
Dear {$s_personal_data_name},
Greetings from IULI.

Kindly remind you for your next payment:
Invoice Number: {$s_invoice_number}
Student Name: {$s_personal_data_name}
Study Program: {$s_study_program_name}
Description: {$s_desc}

Beneficiary Name: {$s_personal_data_name}
Bank: BNI 46 Virtual Account.
Account Number: {$s_vanumber}
Amount: Rp. {$s_payment_amount},-
Payment Deadline: {$s_deadline}

Note:
•	BNI will reject payment which is not at the exact amount and account as stated above
•	You must bring your receipt payment / payment screen captured in exchange for your Toga
•	Non refundable Invoice.

Best regards,
IULI Finance Dept.
TEXT;

        return $s_html;
    }

    public function force_send_email()
    {
        $s_text = <<<TEXT
Dear FADHIL ATHALLAH SAPUTRA,
Aviation Management - 2019


Full Payment Short Semester AVM Batch 2019 - 2022/ODD payment confirmation.
Thank you for your payment on 2023-01-26 11:07:37 for amount of Rp. 12.400.000,-.

Regards,
Finance
TEXT;

        $s_mail_to = 'fadhil.saputra@stud.iuli.ac.id';
        $a_mail_cc = ['employee@company.ac.id', 'employee@company.ac.id', 'mulyasaputra@gmail.com'];
        $a_mail_bcc = ['employee@company.ac.id'];

        $this->email->from('employee@company.ac.id', 'IULI Finance Team');
        // $this->email->to('employee@company.ac.id');
        $this->email->to($s_mail_to);
        $this->email->cc($a_mail_cc);
        $this->email->bcc($a_mail_bcc);
        $this->email->subject('[IULI] Payment Confirmation');
        $this->email->message($s_text);

        $this->email->send();
    }

    public function short_semester_billing($a_param)
    {
        // $a_curriculum_subject_skipped = [
		// 	'1f780c4a-13e7-4fcf-8d3d-0041f935d966', //Virtual Factory Automation (CSE)
		// 	'7d2924bd-a8bd-4bf0-a154-2a4fc1328c68', //Engineering Design, Design of Punching / Blanking Tool (AUE)
		// 	'4b8236e7-a26b-44c8-8e23-620e9e0ec0bd', //Software Based PCB Manufacturing (INE)
		// 	'442ca46c-3011-4567-bb7c-cc4629bda9d8', //Virtual Factory Automation (INE)
		// 	'881df644-a234-4c92-b83a-11e87d9c6203', //Engineering Design, Design of Punching / Blanking Tool (INE)
		// 	'b879101d-fd80-4f18-9829-8c6fe0b92bea', //Software Based PCB Manufacturing (MTE)
		// 	'66bed173-3e67-4356-88ac-faea295923ea', //Virtual Factory Automation (MTE)
		// 	'25b5fcbd-2b91-4f38-b9e1-557bac85288b', //Engineering Design, Design of Punching / Blanking Tool (MTE)
		// ]; // untuk short semester 2020 - 8
        
        $s_subject_list = '';
        $i_number = 0;
        $o_invoice_data = $this->General->get_where('dt_invoice', ['invoice_id' => $a_param['invoice_id']]);
        $mbo_invoice_data = $this->Im->student_has_invoice_data($o_invoice_data[0]->personal_data_id, [
            'df.fee_amount_type' => 'main',
            'di.invoice_id' => $a_param['invoice_id']
        ]);

        if (count($a_param['a_score_id'])) {
            foreach ($a_param['a_score_id'] as $s_score_id) {
                $o_score_details = $this->Scm->get_score_by_id($s_score_id)[0];
                $d_subject_fee = 0;
                // if ($o_score_details->student_id == '5bfb3427-f247-4a1f-9c37-070d4f171893') {
                //     $d_subject_fee = $mbo_invoice_data->fee_amount * $o_score_details->curriculum_subject_credit;
                // }
                // else if (!in_array($o_score_details->curriculum_subject_id, $a_curriculum_subject_skipped)) {
                    $d_subject_fee = $mbo_invoice_data->fee_amount * $o_score_details->curriculum_subject_credit;
                // }

                $d_subject_fee = number_format($d_subject_fee, 0, ',', '.');
                
                $i_number++;
                $s_subject_list.= "{$i_number}. {$o_score_details->subject_name} (Rp {$d_subject_fee}) \n";
            }
        }

        $transfer_amount = number_format($a_param['transfer_amount'], 0, ',', '.');
        $va_number = implode(' ', str_split($a_param['va_number'], 4));
		$s_string = <<<TEXT
Dear {$a_param['personal_data_name']},

This email is to confirm your short semester registration. You have registered the following {$a_param['subjects_count']} subject(s):

{$s_subject_list}

Total short semester fee is Rp {$transfer_amount}

Please transfer the Short Semester Fee in the amount of Rp {$transfer_amount} with the deadline on {$a_param['payment_deadline']} to:
Beneficiary Name: {$a_param['personal_data_name']}
Bank: BNI 46
Virtual Account Number: {$va_number}

Terms and Conditions:
1. Please transfer in the exact amount stated above.
2. Unmatched payment will be rejected by virtual account BNI.
3. Registration will be considered fail if the payment has not been received
by {$a_param['payment_deadline']}
4. The Short Semester schedule will be announced in the IULI website a week before short semester starts.
5. Should you have any inquiry regarding:
5.1. Registration Errors, please email to employee@company.ac.id
5.2. Short Semester, please email to employee@company.ac.id
5.3. Payment, please email to employee@company.ac.id

Thank you for your registration and cooperation to transfer in the exact amount stated above.

Academic Services Centre
International University Liaison Indonesia - IULI.
TEXT;

        return $s_string;
    }

    public function halfway_transcript($a_param)
    {
        $s_string = <<<TEXT
Dear {$a_param['student_name']} ({$a_param['student_email']})
Dear Mr/Mrs. {$a_param['parent_name']} ({$a_param['parent_email']})<br>
Please find the attached Transcript of Academic accumulated and latest-score updated from semester one up to semester five. If you find any discrepancy, please contact the Academic Service as soon as possible. 

The latest GPA calculated in this transcript should be used for self-evaluation prior to Oral Final Study Examination (OFSE) scheduled on 4 – 9 June 2018. The GPA, statistically, reflects the probability of the student to pass the OFSE. Intuitively, from past experience, student with GPA less than 2,70 should plan an extra effort to be successful in OFSE.

OFSE should be prepared well in advance in order to increase student’s confidence level which is important to be successful in such oral examination. For requirements of OFSE, you may refer to the Academic Regulation and further relevant information that will be announced in due time.

We hope you can manage your time and study and wish you good luck in this running semester.

Best Regards,
Tutuko Prajogo
Rector
TEXT;
        return $s_string;
    }

    public function final_transcript($a_param)
    {
        $s_string = <<<TEXT
Dear student
<br>
Please find attached your transcript report for {$a_param['semester_type_name']} semester. This is a computer generated file; no signature is required. If you need a hard copy transcript with a Dean signature, feel free to contact the Academic Services Centre.
<br>
We would like to inform you about the repetition examination information for those who need it:
<br>
1. Legal basis: Academic Regulation No: REG/01/IULI/X/2015, article no:
4.4.5.1 Subjects with grade “C” or above may be repeated on a voluntary base
4.4.5.2 Subjects with grade “D” are highly recommended to be repeated
4.4.5.3 Subjects with grade “F” have to be repeated
4.4.6.2 There is a fee for the repetition exam
<br>
2. Registration for the Repetition Examination Bachelor’s degree 
Students who wish to take the repetition examination MUST register online via the IULI student portal (portal.iuli.ac.id) starting from:
<br>
Tuesday – Saturday, 23 – 27 June 2020
<br>
No registration will be accepted before and after the date mentioned above.
<br>
Login using your own user name and password. If you have forgotten your account/user name and password please contact:
<br>
Budi Siswanto
Phone : 0818 557 441
email : employee@company.ac.id
<br>
3. Fee and Payment
The fee for the repetition examination will be announced in your IULI student portal when you register online. Payment must be transferred at the latest on Saturday, 27 June 2020 to Virtual Account generated by system.
Failure to fulfill this requirement will cancel the registration for the repetition examination.
<br>
4. Schedule
The Repetition Examination will be held from Monday, 29 June 2020 to Friday, 3 July 2020. Time table for the repetition examination will be announced on IULI web on Friday, 26 June 2020.
<br>
<br>
Wish you a happy holiday.
<br>
Best Regards,
Academic Service Centre
Chandra Hendrianto
Phone: +62 878 4403 3093
<br>
International University Liaison Indonesia
Associate Tower 7th Floor.
Intermark Indonesia BSD
Jl. Lingkar Timur BSD Serpong
Tangerang Selatan 15310
Indonesia     
<br>    
TEXT;
        return $s_string;
    }
}
