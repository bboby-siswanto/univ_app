<?php
class Api extends Api_core
{
    public function __construct()
	{
		parent::__construct();
		$this->load->model('admission/Admission_model', 'Adm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Family');
		$this->load->model('student/Student_model', 'Student');
		$this->load->model('finance/Invoice_model', 'Im');
		$this->load->model('finance/Bni_model', 'Bm');
        $this->load->model('partner/Partner_student_model', 'Psm');
	}

    public function receive_data()
    {
        $a_post_data = $this->a_api_data;
        $s_partner_id = $a_post_data['partner_id'];
        $a_post_personal_data = $a_post_data['personal_data'];
        $a_post_student_data = $a_post_data['student_data'];

        $mba_partner_student_data = $this->Psm->get_partner_student_data([
            'pd.personal_data_email' => $a_post_personal_data['personal_data_email']
        ]);

        $a_personal_data = [
            'personal_data_id' => $a_post_personal_data['personal_data_id'],
            'personal_data_name' => $a_post_personal_data['personal_data_name'],
            'personal_data_email' => $a_post_personal_data['personal_data_email'],
            'personal_data_phone' => $a_post_personal_data['phone'],
            'personal_data_cellular' => $a_post_personal_data['phone'],
            'personal_data_password' => $a_post_personal_data['code'],
            'personal_data_marital_status' => 'single',
            'personal_data_email_confirmation' => 'yes'
        ];
        
        $a_partner_student_data = [
            'student_partner_id' => $a_post_student_data['candidate_id'],
            'program_study_program_id' => $a_post_student_data['partner_study_program_id'],
            'partner_period_id' => $a_post_student_data['reg_date_id'],
            'personal_data_id' => $a_post_student_data['personal_data_id'],
            'partner_program_id' => $a_post_student_data['partner_program_code'],
            'student_partner_status' => $a_post_student_data['candidate_status'],
            'student_partner_number' => $a_post_student_data['candidate_number'],
            'student_partner_note' => $a_post_student_data['note_status'],
            'student_partner_enrollment_date' => $a_post_student_data['enrollment_date']
        ];

        if ($mba_partner_student_data) {
            // update
        }
        else {
            $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $a_personal_data['personal_data_id']]);
            $mba_student_data = $this->General->get_where('dt_student_partner', ['student_partner_id' => $a_post_student_data['candidate_id']]);
            if (!$mba_personal_data) {
                $this->Pdm->create_new_personal_data($a_personal_data);
            }

            if (!$mba_student_data) {
                $this->Psm->create_new_partner_student($a_partner_student_data);
            }
        }

        $a_return = ['code' => 0, 'message' => 'success'];

        $this->return_json($a_return);
    }
}
