<?php
class Candidate extends App_core
{
    public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Admission_model', 'Am');
		$this->load->model('student/Student_model', 'Sm');
		$this->load->model('personal_data/Personal_data_model', 'Pdm');
		$this->load->model('personal_data/Family_model', 'Fm');
		$this->load->model('File_manager_model', 'Fmam');
		$this->load->model('academic/Academic_year_model', 'Aym');
		$this->load->model('study_program/Study_program_model', 'Spm');
	}

    public function personal_data()
    {
        # code...
    }
}
