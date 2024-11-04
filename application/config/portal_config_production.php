<?php
$config = array(
	'white_list' => array(
		'localhost', 
		'127.0.0.1'
	),
	'ftp' => array (
		'pmb' => array(
			'hostname' => '',
			'username' => '',
			'password' => '',
			'passive' => false,
			'debug' => TRUE,
			'port' => 1,
			'path_upload' => ''
		)
	),
	'sites' => array(
		'pmb' => 'localhost/pmbapp',
		'siakad' => 'localhost/portal_univ',
	),
	'token' => array(),
	'program_data_id' => array(),
	'email' => array(
		'academic' => array(
			'main' => '',
			'head' => '',
			'members' => array(
				''
			)
		),
		'finance' => array(
			'main' => '',
			'head' => '',
			'payment' => '',
			'members' => array(
				''
			)
		),
		'admission' => array(
			'main' => '',
			'head' => '',
			'members' => array(
				''
			)
		),
		'rectorate' => array(
			'main' => '',
			'rector' => '',
			'vice_of_academic' => '',
			'vice_of_non_academic' => ''
		),
		'hrd' => array(
			'main' => '',
			'head' => '',
			'members' => array(
				''
			)
		),
		'it' => array(
			'main' => '',
			'head' => '',
			'members' => array(
				''
			)
		),
		'alumni_affair' => array(
			'main' => ''
		)
	),
	'student_allowed_status' => array(
		'active',
		'onleave',
		'inactive',
		'graduated'
	),
	'portal_menu' => array(
		'top_bar' => array(
			'finance' => array(
				'title' => 'Finance',
				'url' => site_url('module/set/finance'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'finance_student_list',
						'title' => 'Student List',
						'url' => site_url('student/lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'finance_invoice_list',
						'title' => 'Invoice List',
						'url' => site_url('finance/invoice/lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'finance_payment_list',
						'title' => 'Payment List',
						'url' => site_url('finance/invoice/payment_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'finance_fee_list',
						'title' => 'Fee List',
						'url' => site_url('finance/fee/lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'finance_payment_code_reference',
						'title' => 'Payment Code Reference',
						'url' => site_url('finance/reference/payment_code'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'finance_report',
						'title' => 'Report',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'finance_report_tuitionfee',
								'title' => 'Tuition Fee Report',
								'url' => site_url('finance/report/report_tuition_fee'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'finance_recaps_billing',
								'title' => 'Tuition Fee Recaps',
								'url' => site_url('finance/invoice/billing_student_recaps'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							)
						)
					),
					// array(
					// 	'name' => 'finance_student_aid',
					// 	'title' => 'Student Aid',
					// 	'url' => site_url('finance/student_finance/aid'),
					// 	'allow_param' => false,
					// 	'disallow_status' => array(),
					// 	'show' => true
					// )
				)
			),
			'admission' => array(
				'title' => 'Admission',
				'url' => site_url('module/set/admission'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'student_list',
						'title' => 'Student List',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'addmission_student_list',
								'title' => 'International',
								'url' => site_url('student/lists'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'addmission_student_national',
								'title' => 'National',
								'url' => site_url('student/national'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'addmission_student_karyawan',
								'title' => 'Karyawan',
								'url' => site_url('student/lists_karyawan'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'addmission_student_course',
								'title' => 'Course',
								'url' => site_url('student/lists_course'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					array(
						'name' => 'addmission_institution_list',
						'title' => 'Institution List',
						'url' => site_url('institution/lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					[
						'name' => 'addmission_refferal_list',
						'title' => 'Referral List',
						'url' => site_url('admission/referral/lists'),
						'allow_param' => false,
						'disallow_status' => [],
						'show' => true
					],
					[
						'name' => 'admission_event_list',
						'title' => 'Event List',
						'url' => site_url('event/lists'),
						'allow_param' => false,
						'disallow_status' => [],
						'show' => true
					],
					[
						'name' => 'candidate_questionnaire',
						'title' => 'Candidate Questionnaire',
						'url' => site_url('admission/questionnaire/question_list'),
						'allow_param' => false,
						'disallow_status' => [],
						'show' => true
					],
					[
						'name' => 'entrance_test_online',
						'title' => 'Online Entrance Test',
						'url' => site_url('admission/entrance_test/participant_list'),
						'allow_param' => false,
						'disallow_status' => [],
						'show' => true
					]
				)
			),
			'student_academic' => array(
				'title' => 'My Academic',
				'url' => site_url('module/set/student_academic'),
				'allowed_user_type' => array('alumni','student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'student_transcript',
						'title' => 'Transcript',
						'url' => site_url('student/transcript'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'student_study_plan',
						'title' => 'Study Plan Registration',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array('graduated'),
						'show' => true,
						'child' => array(
							array(
								'name' => 'student_krs_registration',
								'title' => 'KRS Registration',
								'url' => site_url('student/study_plan/registration'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'student_short_semeter_registration',
								'title' => 'Short Semester Registration',
								'url' => site_url('student/short_semester/registration'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'student_ofse_registration',
								'title' => 'OFSE Registration',
								'url' => site_url('student/ofse/registration'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'student_repetition_registration',
								'title' => 'Repetition Registration',
								'url' => site_url('student/repeat/registration'),
								'allow_param' => false,
								'disallow_status' => array('graduated'),
								'show' => true
							),
						)
					),
					array(
						'name' => 'student_achievement_list',
						'title' => 'My Achievements',
						'url' => site_url('student/supplement/page'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'student_document_list',
						'title' => 'Document',
						'url' => site_url('student/academic_document'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false
					),
				)
			),
			'student_finance' => array(
				'title' => 'My Financial',
				'url' => site_url('module/set/student_finance'),
				'allowed_user_type' => array('student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'student_payment_aid',
						'title' => 'Student Aid',
						'url' => site_url('student/finance/aid'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false
					),
					array(
						'name' => 'student_unpaid_invoice',
						'title' => 'Unpaid Invoice',
						'url' => site_url('student/finance/unpaid_invoice'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'payment_history',
						'title' => 'Payment History',
						'url' => site_url('student/finance/payment_history'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'student_invoice',
						'title' => 'Invoice',
						'url' => site_url('student/finance/invoice'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false,
						'child' => array(
							array(
								'name' => 'student_invoice_list',
								'title' => 'Invoice List',
								'url' => site_url('student/finance/invoice_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => false
							),
							array(
								'name' => 'student_enroll_in_payment_plan',
								'title' => 'Enroll in Payment Plan',
								'url' => site_url('student/finance/pending_payment'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => false
							)
						)
					),
					array(
						'name' => 'student_payment_plan',
						'title' => 'Payment Plan',
						'url' => site_url('student/finance/plan'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false
					)
				)
			),
			'student_document' => array(
				'name' => 'student_document_page',
				'title' => 'Document',
				// 'url' => site_url('module/set/student_document'),
				'url' => 'https://drive.google.com/drive/folders/1iSOc3JdteTTHS7VDETOVO6cKz6m_Ml1x?usp=sharing',
				'allowed_user_type' => array('student', 'staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'doc_finance',
						'title' => 'Finance',
						'url' => site_url('student/documents/finance'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'doc_general',
						'title' => 'General',
						'url' => site_url('student/documents/general'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'doc_international_office',
						'title' => 'International Office',
						'url' => site_url('student/documents/international_office'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'doc_template',
						'title' => 'Internship Template',
						'url' => site_url('student/documents/internship'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'doc_labolatory',
						'title' => 'Laboratory',
						'url' => site_url('student/documents/laboratory'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'doc_thesis_template',
						'title' => 'Thesis Template',
						'url' => site_url('student/documents/thesis'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					)
				)
			),
			'student_thesis' => array(
				'title' => 'Thesis',
				'url' => site_url('module/set/student_thesis'),
				'allowed_user_type' => array('student', 'alumni'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'proposal_submission',
						'title' => 'Proposal Submission',
						'url' => site_url('thesis/student_thesis/proposal_submission'),
						// 'url' => '#',
						'allow_param' => false,
						'disallow_status' => array('inactive', 'onleave', 'resign', 'dropout'),
						'show' => true
					),
					array(
						'name' => 'work_submission',
						'title' => 'Work Submission',
						// 'url' => '#',
						'url' => site_url('thesis/student_thesis/work_submission'),
						'allow_param' => false,
						'disallow_status' => array('inactive', 'onleave', 'resign', 'dropout'),
						'show' => true
					),
					array(
						'name' => 'final_submission',
						'title' => 'Final Submission',
						// 'url' => '#',
						'url' => site_url('thesis/student_thesis/final_submission'),
						'allow_param' => false,
						'disallow_status' => array('inactive', 'onleave', 'resign', 'dropout'),
						'show' => true
					),
					array(
						'name' => 'thesis_defense_student',
						'title' => 'Thesis Defense',
						// 'url' => '#',
						'url' => site_url('thesis/student_thesis/thesis_defense'),
						'allow_param' => false,
						'disallow_status' => array('inactive', 'onleave', 'resign', 'dropout', 'graduated'),
						'show' => true
					)
				),
			),
			'student_internship' => array(
				'title' => 'Internship',
				'url' => site_url('module/set/student_internship'),
				'allowed_user_type' => array('student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'internship_submission',
						'title' => 'Internship Submission',
						'url' => site_url('student/internship/page'),
						// 'url' => '#',
						'allow_param' => false,
						'disallow_status' => array('inactive', 'onleave', 'resign', 'dropout', 'graduated'),
						'show' => true
					),
				),
			),
			'student_abroad' => array(
				'title' => 'Abroad',
				'url' => site_url('module/set/student_abroad'),
				'allowed_user_type' => array('student', 'alumni'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'abroad_registration',
						'title' => 'Abroad Application',
						'url' => site_url('student/abroad/registration'),
						'allow_param' => false,
						'disallow_status' => array('inactive', 'resign', 'dropout'),
						'show' => false
					),
					array(
						'name' => 'abroad_submission',
						'title' => 'Study Submission',
						'url' => site_url('student/abroad/submission'),
						'allow_param' => false,
						'disallow_status' => array('inactive', 'resign', 'dropout'),
						'show' => true
					),
				),
			),
			'student_apps' => array(
				'title' => 'Apps',
				'url' => site_url('module/set/student_apps'),
				'allowed_user_type' => array('student'),
				// 'allowed_personal_data_id' => array(),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'kpu',
						'title' => 'KPU',
						'url' => site_url('apps/kpu'),
						'allow_param' => false,
						'allowed_personal_data_id' => array(''),
						'disallow_status' => array('candidate','participant','pending','inactive','dropout','resign','onleave'),
						'show' => true
					),
					array(
						'name' => 'iuli_marketing',
						'title' => 'IULI Marketing',
						'url' => site_url('apps/iuli_marketing'),
						'allow_param' => false,
						'allowed_personal_data_id' => array(''),
						'disallow_status' => array(),
						'show' => true
					),
				)
			),
			
			'student_alumni' => array(
				'title' => 'My Alumni',
				'url' => site_url('module/set/student_alumni'),
				'allowed_user_type' => array('alumni','student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'vacancy',
						'title' => 'Vacancy',
						'url' => site_url('alumni/vacancy/lists_vacancy'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'testimonial',
						'title' => 'Testimonial',
						'url' => site_url('alumni/testimonial/my_testimonial'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'iuli_info',
						'title' => 'IULI Info',
						'url' => site_url('alumni/iuli_info/info_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'dikti_questionaire',
						'title' => 'Dikti Tracer Study',
						'url' => site_url('alumni/dikti_tracer_study'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					)
				)
			),
			'student_ofse' => array(
				'title' => 'OFSE Examination',
				'url' => site_url('student/ofse/exam'),
				'allowed_user_type' => array('student'),
				'show' => true
			),
			'student_assessment' => array(
				'title' => 'Assessment',
				'url' => site_url('module/set/student_assessment'),
				'allowed_user_type' => array('student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'campus_assessment',
						'title' => 'Satisfaction Questionnaire',
						'url' => site_url('student/university_performance'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'tracer_study',
						'title' => 'Tracer Study',
						'url' => site_url('alumni/dikti_tracer_study'),
						'allow_param' => false,
						'disallow_status' => array(),
						'allowed_personal_data_id' => array(''),
						'show' => true
					)
				)
			),
			'academic' => array(
				'title' => 'Academic',
				'url' => site_url('module/set/academic'),
				'allowed_user_type' => array('staff', 'lecturer', 'lect'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'academic_student_list',
						'title' => 'Student Lists',
						'url' => site_url('academic/student_academic/student_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_student_supplement',
						'title' => 'Supplement List',
						'url' => site_url('academic/student_academic/supplement_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_student_activity_dikti',
						'title' => 'Student Activity for Dikti',
						'url' => site_url('academic/activity_study/activity_study_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_semester_list',
						'title' => 'Semester Settings',
						'url' => site_url('academic/semester/semester_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_subject_curriculum',
						'title' => 'Subject Curriculum',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'academic_subject_list',
								'title' => 'Subject',
								'url' => site_url('academic/subject/subject_lists'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'academic_curriculum_list',
								'title' => 'Curriculum',
								'url' => site_url('academic/curriculum/curriculum_lists'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					array(
						'name' => 'academic_offered_subject',
						'title' => 'Offered Subject',
						'url' => site_url('academic/offered_subject/offered_subject_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_class_group_list',
						'title' => 'Class Group',
						'url' => site_url('academic/class_group/class_group_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_ofse_list',
						'title' => 'OFSE',
						'url' => site_url('academic/ofse/ofse_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_krs_approval_student_list',
						'title' => 'KRS Approval',
						'url' => site_url('krs/krs_approval'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'academic_document',
						'title' => 'Document Academic',
						'url' => site_url('academic/document/list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false
					),
					array(
						'name' => 'academic_internship',
						'title' => 'Internship',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'internship_document',
								'title' => 'Internship Document',
								'url' => site_url('student/internship/document_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					array(
						'name' => 'academic_thesis',
						'title' => 'Thesis',
						'url' => '#',
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'thesis_student',
								'title' => 'Thesis Student',
								'url' => site_url('thesis/thesis_student'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'defense_list',
								'title' => 'Defense List',
								'url' => site_url('thesis/defense_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'proposal_submission_academic',
								'title' => 'Proposal Submission',
								'url' => site_url('thesis/proposal_submission'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'work_submission_academic',
								'title' => 'Work Submission',
								'url' => site_url('thesis/work_submission'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'final_submission_academic',
								'title' => 'Final Submission',
								'url' => site_url('thesis/final_submission'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'thesis_defense_academic',
								'title' => 'Thesis Defense',
								'url' => site_url('thesis/thesis_defense'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'advisor_examiner_list_academic',
								'title' => 'Advisor/Examiner List',
								'url' => site_url('thesis/advisor_examiner_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					'lecturer_assessment' =>  array(
						'name' => 'lecturer_assessment',
						'title' => 'Assessment',
						'url' => '#',
						'allowed_user_type' => array('staff'),
						'show' => true,
						'child' => array(
							array(
								'name' => 'list_lecturer',
								'title' => 'Lecturer Assessment',
								'url' => site_url('validation_requirement/lecturer_assesment/list_lecturer'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							// array(
							// 	'name' => 'list_repondent',
							// 	'title' => 'Respondent Lecturer Assessment',
							// 	'url' => site_url('validation_requirement/lecturer_assesment/list_respondent'),
							// 	'allow_param' => false,
							// 	'disallow_status' => array(),
							// 	'show' => true
							// ),
							array(
								'name' => 'student_satisfaction',
								'title' => 'University Assessment',
								'url' => site_url('validation_requirement/university_assessment/assessment_result'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					'accreditation' =>  array(
						'name' => 'data_accreditation',
						'title' => 'PDDikti Data',
						'url' => '#',
						'allowed_user_type' => array('staff'),
						'show' => true,
						'child' => array(
							array(
								'name' => 'list_lecturer',
								'title' => 'TR_AKD Dosen',
								'url' => site_url('accreditation/lecturer_teaching'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'student_status',
								'title' => 'Student Status',
								'url' => site_url('accreditation/student_status'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'tracer_dikti',
								'title' => 'Tracer Study',
								'url' => site_url('accreditation/tracer_dikti'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'list_advisor_examiner',
								'title' => 'Thesis Advisor / Examiner',
								'url' => site_url('accreditation/lecturer_advisor_examiner'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => false
							),
						)
					),
					array(
						'name' => 'list_lecturer',
						'title' => 'List lecturer',
						'url' => site_url('academic/class_group/list_lecturer'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
				)
			),
			'staff_international_office' => array(
				'title' => 'International Office',
				'url' => site_url('module/set/staff_international_office'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'io_student_list',
						'title' => 'Student From Abroad',
						'url' => site_url('admission/international_office/io_student_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'io_student_abroad',
						'title' => 'Student Abroad',
						'url' => site_url('admission/international_office/student_abroad'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
				)
			),
			'staff_thesis' => array(
				'title' => 'Thesis',
				'url' => site_url('module/set/staff_thesis'),
				'allowed_user_type' => array('staff','lect','examiner', 'lecturer'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'proposal_submission',
						'title' => 'Proposal Submission',
						'url' => site_url('thesis/proposal_submission'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'work_submission',
						'title' => 'Work Submission',
						'url' => site_url('thesis/work_submission'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'final_submission',
						'title' => 'Final Submission',
						'url' => site_url('thesis/final_submission'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'thesis_defense',
						'title' => 'Thesis Defense',
						'url' => site_url('thesis/thesis_defense'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'advisor_examiner_list',
						'title' => 'Advisor/Examiner List',
						'url' => site_url('thesis/advisor_examiner_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
				)
			),
			'staff_alumni' => array(
				'title' => 'Alumni',
				'url' => site_url('module/set/staff_alumni'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'alumni_list',
						'title' => 'List Alumni',
						'url' => site_url('alumni/lists_alumni'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'alumni_vacancy_lists',
						'title' => 'Vacancy',
						'url' => site_url('alumni/vacancy/lists_vacancy'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'alumni_testimonial_lists',
						'title' => 'Testimonial',
						'url' => site_url('alumni/testimonial/my_testimonial'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'alumni_iuli_info',
						'title' => 'IULI Info',
						'url' => site_url('alumni/iuli_info/info_lists'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'alumni_dikti_tracer',
						'title' => 'Dikti Tracer Study',
						'url' => site_url('alumni/lists_tracer'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'company_survey',
						'title' => 'Company Survey',
						'url' => site_url('alumni/list_survey'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					)
				)
			),
			'apps' => array(
				'title' => 'IULI Apps',
				'url' => site_url('module/set/apps'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'letter_numbering',
						'title' => 'Letter Number',
						'url' => site_url('apps/letter_numbering/list_number_of_letter'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'gsr',
						'title' => 'GSR',
						'url' => site_url(),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'chart_list',
								'title' => 'Chart of Account',
								'url' => site_url('apps/gsr/chart_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'request_list',
								'title' => 'Request',
								'url' => site_url('apps/gsr/request_list'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					array(
						'name' => 'graduation_registration',
						'title' => 'Graduation Registration',
						'url' => site_url('academic/student_academic/graduation_registration'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
				),
			),
			// 'letter_numbering' => array(
			// 	'title' => 'Letter Number',
			// 	'url' => site_url('apps/letter_numbering/list_number_of_letter'),
			// 	'allowed_user_type' => array('staff'),
			// 	'show' => true,
			// 	'side_bar' => array(
			// 		array(
			// 			'name' => 'letter_numbering',
			// 			'title' => 'Letter Number',
			// 			'url' => site_url('apps/letter_numbering/list_number_of_letter'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => true
			// 		),
			// 		array(
			// 			'name' => 'incoming',
			// 			'title' => 'Incoming Letter',
			// 			'url' => site_url('apps/letter_numbering/incoming_letter'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => false
			// 		),
			// 	)
			// ),
			// 'gsr' => array(
			// 	'title' => 'GSR',
			// 	'url' => site_url('apps/gsr/chart_list'),
			// 	'allowed_user_type' => array('staff'),
			// 	'show' => false,
			// 	'side_bar' => array(
			// 		array(
			// 			'name' => 'chart_list',
			// 			'title' => 'Chart of Account List (COA)',
			// 			'url' => site_url('apps/gsr/chart_list'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => true
			// 		),
			// 		array(
			// 			'name' => 'request_list',
			// 			'title' => 'Request List',
			// 			'url' => site_url('apps/gsr/request_list'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => true
			// 		),
			// 		array(
			// 			'name' => 'df_list',
			// 			'title' => 'Disbursement List',
			// 			'url' => site_url('apps/gsr/df_list'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => true
			// 		),
			// 		array(
			// 			'name' => 'rf_list',
			// 			'title' => 'Receiptment List',
			// 			'url' => site_url('apps/gsr/rf_list'),
			// 			'allow_param' => false,
			// 			'disallow_status' => array(),
			// 			'show' => true
			// 		),
			// 	)
			// ),
			'staff_ofse' => array(
				'title' => 'OFSE Examination',
				'url' => site_url('academic/ofse/examiner'),
				'allowed_user_type' => array('staff', 'lecturer', 'lect'),
				'show' => true,
				'side_bar' => array()
			),
			'profile' => array(
				'title' => null,
				'url' => null,
				'allowed_user_type' => array('student'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'my_personal_data',
						'title' => 'Personal Data',
						'url' => site_url('personal_data/profile'),
						'allow_param' => true,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'my_family',
						'title' => 'Family Data',
						'url' => site_url('personal_data/family/family_lists'),
						'allow_param' => true,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'my_addess_data',
						'title' => 'Address Data',
						'url' => site_url('address/address_lists'),
						'allow_param' => true,
						'disallow_status' => array('candidate','participant','pending','active','inactive','dropout','resign','onleave'),
						'show' => true
					),
					array(
						'name' => 'my_academic_history',
						'title' => 'Academic History',
						'url' => site_url('personal_data/academic/academic_history'),
						'allow_param' => true,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'my_job_history',
						'title' => 'Job History',
						'url' => site_url('personal_data/job_history'),
						'allow_param' => true,
						'disallow_status' => array('candidate','participant','pending','active','inactive','dropout','resign','onleave'),
						'show' => true
					),
					array(
						'name' => 'my_document',
						'title' => 'My Document',
						'url' => site_url('personal_data/document/document_list'),
						'allow_param' => true,
						'disallow_status' => array('graduated'),
						'show' => true
					),
					array(
						'name' => 'covid_vaccine_certificate',
						'title' => 'Covid Vaccine Certificate',
						'url' => site_url('personal_data/covid_certificate'),
						'allow_param' => true,
						'disallow_status' => array(),
						'show' => true
					)
				)
			),
			'hris' =>  array(
				'title' => 'HRIS',
				'url' => site_url('module/set/hris'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'employee_list',
						'title' => 'Employee List',
						'url' => site_url('hris/employee_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'department',
						'title' => 'Department',
						'url' => site_url('hris/department'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'attendance_list',
						'title' => 'Attendance List',
						'url' => site_url('hris/attendance_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => false
					),
				)
			),
			'ite' =>  array(
				'title' => 'Devs',
				'url' => site_url('module/set/ite'),
				'allowed_user_type' => array('staff'),
				'show' => true,
				'side_bar' => array(
					array(
						'name' => 'pages_list',
						'title' => 'Pages',
						'url' => site_url('devs/pages/pages_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'roles_list',
						'title' => 'Roles',
						'url' => site_url('devs/roles/roles_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'employee_list',
						'title' => 'Employee',
						'url' => site_url('devs/devs_employee/employee_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'prodi_list',
						'title' => 'Study Program',
						'url' => site_url('devs/prodi_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					array(
						'name' => 'feeder_report',
						'title' => 'Report Feeder',
						'url' => site_url(),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true,
						'child' => array(
							array(
								'name' => 'kurikulum_matkul_feeder',
								'title' => 'Kurikulum Matkul',
								'url' => site_url('feeder/report/kurikulum_matkul'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
							array(
								'name' => 'student_feeder',
								'title' => 'Student',
								'url' => site_url('feeder/report/student_feeder'),
								'allow_param' => false,
								'disallow_status' => array(),
								'show' => true
							),
						)
					),
					array(
						'name' => 'script_list',
						'title' => 'Starter',
						'url' => site_url('devs/script_list'),
						'allow_param' => false,
						'disallow_status' => array(),
						'show' => true
					),
					// array(
					// 	'name' => 'student_feeder',
					// 	'title' => 'Student',
					// 	'url' => site_url('feeder/report/student_feeder'),
					// 	'allow_param' => false,
					// 	'disallow_status' => array(),
					// 	'show' => true
					// ),
				)
			),
			'e_journal' =>  array(
				'title' => 'eJournal',
				'url' => site_url('apps/electronic_journal/ejournal'),
				'allowed_user_type' => array('staff', 'student', 'alumni'),
				'show' => true
			),
		)
	)
);