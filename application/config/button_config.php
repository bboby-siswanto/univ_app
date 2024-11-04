<?php

$config['btn_config'] = array(
    'btn_set_draft' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'data_id' => '\'+data+\'',
            'data_status' => '\'+row.info_status+\'',
            'id' => 'btn_set_draft',
            'name' => 'btn_set_draft',
            'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Set draft',
			'content' => '<i class="fa fa-power-off"></i>'
        )
    ),
    'btn_set_active' =>array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'data_id' => '\'+data+\'',
            'id' => 'btn_set_active',
            'data_status' => '\'+row.status+\'',
            'data_type' => '\'+row.personal_address_type+\'',
            'data_academic_main' => '\'+row.academic_history_main+\'',
            'name' => 'btn_set_active',
            'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Inactive',
			'content' => '<i class="fa fa-power-off"></i>'
        )
    ),
    'btn_remove_transfer_credit' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'id' => 'btn_remove_transfer_credit',
            'name' => 'btn_remove_transfer_credit',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'title' => 'Remove transfer credit'
        )
    ),
    'btn_transfer_credit_student' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'id' => 'btn_transfer_credit_student',
            'name' => 'btn_transfer_credit_student',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-long-arrow-alt-right"></i>',
            'title' => 'Transfer Subject'
        )
    ),
    'btn_moving_student' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'id' => 'btn_moving_student',
            'name' => 'btn_moving_student',
            'data-status' => '\'+row.student_status+\'',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-chalkboard-teacher"></i>',
            'title' => 'Move Class'
        )
    ),
    'btn_edit_student' => array(
        'type' => 'link',
        'target' => '#',
        'properties' => array(
            'id' => 'btn_edit_student',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'title' => 'Edit Student'
        )
    ),
    'btn_delete_academic_history' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'name' => 'btn_delete_academic_history',
            'type' => 'button',
            'data_id' => '\'+data+\'',
            'data_main' => '\'+row.academic_history_main+\'',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'title' => 'Remove data'
        )
    ),
    'btn_edit_academic_history' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
	        'data_id' => '\'+data+\'',
            'name' => 'btn_edit_academic_history',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'title' => 'Edit Data'
        )
    ),
    'btn_delete_address_data' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'name' => 'btn_delete_address_data',
            'type' => 'button',
            'data_id' => '\'+data+\'',
            'data_type' => '\'+row.personal_address_type+\'',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'title' => 'Remove Address Data'
        )
    ),
    'btn_edit_address_data' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'data_type' => '\'+row.personal_address_type+\'',
            'name' => 'btn_edit_address_data',
            'type' => 'button',
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'title' => 'Edit Address Data'
        )
    ),
    'btn_download_document' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'type' => 'button',
            'name' => 'btn_download_supporting_document',
            'class' => 'btn btn-primary btn-sm',
            'content' => '<i class="fas fa-download"></i>',
            'title' => 'Download Document'
        )
    ),
    'btn_remove_document' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'data_link' => '\'+row.document_requirement_link+\'',
            'name' => 'btn_delete_supporting_document',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Remove Document'
        )
    ),
    'btn_edit_subject' => array(
        'type' => 'modal',
        'target' => 'curriculum/subject/subject_lists',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_edit_subject',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Edit Subject'
        )
    ),
    'btn_delete_subject' => array(
        'type' => 'modal',
        'target' => 'curriculum/subject/subject_lists',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_delete_subject',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Remove Subject'
        )
    ),
    'btn_edit_curriculum' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_edit_curriculum',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Edit Curriculum'
        )
    ),
    'btn_copy_curriculum' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_copy_curriculum',
            'class' => 'btn btn-success btn-sm',
            'content' => '<i class="fas fa-copy"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Create with copy data'
        )
    ),
    'btn_view_curriculum_semester' => array(
        'type' => 'link',
        'target' => 'academic/curriculum/curriculum_lists/\'+row.curriculum_id+\'',
        'properties' => array(
            'name' => 'btn_view_curriculum_semester',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-bookmark"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'View curriculum semester'
        )
    ),
    'btn_view_curriculum_subject' => array(
        'type' => 'link',
        'target' => 'academic/curriculum/curriculum_lists/\'+row.curriculum_id+\'/\'+data+\'',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_view_curriculum_subject',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-book"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'View curriculum subject'
        )
    ),
    'btn_curriculum_table_offer_subject' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_curriculum_table_offer_subject',
            'class' => 'btn btn-success btn-sm',
            'content' => '<i class="fas fa-bookmark"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Add offered subject'
        )
    ),
    'btn_curriculum_semester_edit' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_curriculum_semester_edit',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-edit"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Edit curriculum semester'
        )
    ),
    'btn_curriculum_add_offered_subject' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_curriculum_add_offered_subject',
            'class' => 'btn btn-info btn-sm',
            'content' => '<i class="fas fa-angle-double-right"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Offer Subject'
        )
    ),
    'btn_remove_offered_subject' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_remove_offered_subject',
            'class' => 'btn btn-danger btn-sm',
            'content' => '<i class="fas fa-trash"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Remove Offer Subject'
        )
    ),
    'btn-team-teaching' => array(
        'type' => 'modal',
        'target' => '#',
        'properties' => array(
            'data_id' => '\'+data+\'',
            'name' => 'btn_team_teaching',
            'class' => 'btn btn-success btn-sm',
            'content' => '<i class="fas fa-user-plus"></i>',
            'data-toggle' => 'tooltip',
            'title' => 'Team Teaching'
        )
    ),
    'btn_institution_edit' => array(
		'type' => 'modal',
		'target' => 'institution/form_institution',
		'title' => 'Save or Edit Institution',
		'include_params' => false,
		'properties' => array(
			'id' => 'btn_institution_edit_modal',
			'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Save or Edit Institution',
			'content' => '<i class="fas fa-file"></i>'
		)
    ),
    'btn_close_job_vacancy' => array(
		'type' => 'modal',
		'target' => '#',
		'title' => 'Save or Edit Institution',
		'include_params' => false,
		'properties' => array(
            'data_id' => '\'+data+\'',
            'data_status' => '\'+row.post_status+\'',
            'id' => 'btn_close_job_vacancy',
            'name' => 'btn_close_job_vacancy',
			'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Close job vacancy',
			'content' => '<i class="fas fa-power-off"></i>'
		)
    ),
    'btn_set_draft' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'data_id' => '\'+data+\'',
            'data_status' => '\'+row.info_status+\'',
            'id' => 'btn_set_draft',
            'name' => 'btn_set_draft',
            'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Set draft',
			'content' => '<i class="fas fa-power-off"></i>'
        )
    ),
    'btn_set_active' =>array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'data_id' => '\'+data+\'',
            'id' => 'btn_set_active',
            'data_status' => '\'+row.status+\'',
            'data_type' => '\'+row.personal_address_type+\'',
            'data_academic_main' => '\'+row.academic_history_main+\'',
            'name' => 'btn_set_active',
            'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Inactive',
			'content' => '<i class="fas fa-power-off"></i>'
        )
    ),
    'btn_view_lecturer' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => true,
        'properties' => array(
            'id' => 'btn_view_lecturer',
            'name' => 'btn_view_lecturer',
            'type' => 'button',
            'class' => 'btn btn-info',
            'title' => 'View lecturer',
            'content' => '<i class="fas fa-users"></i>'
        )
    ),
    'btn_view_score' => array(
        'type' => 'link',
        'target' => 'academic/class_group/class_group_lists/',
        'include_params' => true,
        'properties' => array(
            'target' => 'blank',
            'id' => 'btn_view_score',
            'name' => 'btn_view_score',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'view score absence',
            'content' => '<i class="fas fa-address-card"></i>'
        )
    ),
    'btn_download_score_template' => array(
        'type' => 'link',
        'target' => 'academic/class_group/download_score_template/',
        'include_params' => true,
        'properties' => array(
            'id' => 'btn_download_score_template',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'download score template',
            'content' => '<i class="fas fa-file-download"></i>'
        )
    ),
    'btn_download_absence_template' => array(
        'type' => 'link',
        'target' => 'academic/class_group/download_absence_template/',
        'include_params' => true,
        'properties' => array(
            'id' => 'btn_download_absence_template',
            'type' => 'button',
            'target' => 'blank',
            'class' => 'btn btn-info btn-sm',
            'title' => 'download absence',
            'content' => '<i class="fas fa-download"></i>'
        )
    ),
    'btn_send_score_template' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'id' => 'btn_send_score_template',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'send score template',
            'content' => '<i class="fas fa-paper-plane"></i>'
        )
    ),
    'btn_action_edit' => array(
        'type' => 'modal',
        'target' => '#',
        'include_param' => false,
        'properties' => array(
            'id' => 'btn_action_edit',
            'name' => 'btn_action_edit',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Edit Data',
            'content' => '<i class="fas fa-edit"></i>'
        )
    ),
    'btn_action_delete' => array(
        'type' => 'modal',
        'target' => '#',
        'include_param' => false,
        'properties' => array(
            'id' => 'btn_action_delete',
            'name' => 'btn_action_delete',
            'type' => 'button',
            'class' => 'btn btn-danger btn-sm',
            'title' => 'Delete Data',
            'content' => '<i class="fas fa-trash"></i>'
        )
    ),
    'btn_action_absence' => array(
        'type' => 'link',
        'target' => 'academic/class_group/class_group_absence/\'+row.class_master_id+\'',
        'properties' => array(
            'target' => 'blank',
            'id' => 'btn_action_absence',
            'name' => 'btn_action_absence',
            'type' => 'button',
            'class' => 'btn btn-success btn-sm',
            'title' => 'Absence Student',
            'content' => '<i class="fas fa-list-alt"></i>'
        )
    ),
    'btn_ofse_table_score' => array(
        'type' => 'link',
        'target' => 'academic/ofse/ofse_lists/',
        'include_params' => true,
        'title' => 'Show class member ofse',
        'properties' => array(
            'id' => 'btn_ofse_table_score',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Show member',
            'content' => '<i class="fas fa-address-card"></i>'
        )
    ),
    'btn_input_score_ofse' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'id' => 'btn_input_score_ofse',
            'name' => 'btn_input_score_ofse',
            'type' => 'button',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Input Score',
            'content' => '<i class="fas fa-poll"></i>'
        )
    ),
    'btn_absence_input_score_quiz' => array(
        'type' => 'modal',
        'target' => '#',
        'include_params' => false,
        'properties' => array(
            'id' => 'btn_absence_input_score_quiz',
            'name' => 'btn_absence_input_score_quiz',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Input Score Quiz',
            'content' => '<i class="fas fa-poll"></i>'
        )
    ),
    'btn_student_score' => array(
        'type' => 'link',
        'target' => 'academic/score/student_score/',
        'include_params' => true,
        'properties' => array(
            'id' => 'btn_student_score',
            'name' => 'btn_student_score',
            'class' => 'btn btn-info btn-sm',
            'title' => 'Student score',
            'content' => '<i class="fas fa-book-open"></i>'
        )
    )
);

$config['module'] = array(
	'admission' => array(
		'institution' => array(
			$config['btn_config']['btn_institution_edit']
		)
    ),
    'alumni' => array(
        'job_vacancy' => array(
            $config['btn_config']['btn_close_job_vacancy']
        ),
    ),
    'academic' => array(
        'transfer_subject' => array(
            $config['btn_config']['btn_remove_transfer_credit']
        ),
        'transfer_credit' => array(
            $config['btn_config']['btn_transfer_credit_student']
        ),
        'subject' => array(
            $config['btn_config']['btn_edit_subject']
        ),
        'curriculum' => array(
            $config['btn_config']['btn_edit_curriculum'],
            $config['btn_config']['btn_copy_curriculum'],
            $config['btn_config']['btn_view_curriculum_semester'],
            $config['btn_config']['btn_view_curriculum_subject']
        ),
        'curriculum_semester' => array(
            $config['btn_config']['btn_curriculum_semester_edit'],
            $config['btn_config']['btn_copy_curriculum'],
            $config['btn_config']['btn_view_curriculum_subject']
        ),
        'curriculum_subject' => array(
            $config['btn_config']['btn_edit_subject'],
            // $config['btn_config']['btn_curriculum_table_offer_subject']
        ),
        'curriculum_offered_subject' => array(
            $config['btn_config']['btn_curriculum_add_offered_subject']
        ),
        'offer_subject_lecturer' => array(
            $config['btn_config']['btn_delete_academic_history']
        ),
        'offered_subject' => array(
            $config['btn_config']['btn_remove_offered_subject'],
            $config['btn_config']['btn-team-teaching'],
            $config['btn_config']['btn_view_lecturer']
        ),
        'class_group' => array(
            // $config['btn_config']['btn_view_score'],
            $config['btn_config']['btn_download_score_template'],
            // $config['btn_config']['btn_send_score_template']
        ),
        'ofse_table_action' => array(
            $config['btn_config']['btn_ofse_table_score']
        ),
        'ofse_member_action' => array(
            $config['btn_config']['btn_input_score_ofse']
        ),
        'student_absence_table' => array(
            $config['btn_config']['btn_absence_input_score_quiz']
        ),
        'class_student_member' => array(
            $config['btn_config']['btn_moving_student']
        )
    ),
    'profile' => array(
        'academic_history' => array(
            $config['btn_config']['btn_edit_academic_history'],
            $config['btn_config']['btn_delete_academic_history']
        ),
        'document_list' => array(
            $config['btn_config']['btn_download_document'],
            $config['btn_config']['btn_remove_document']
        ),
        'address_list' => array(
            $config['btn_config']['btn_edit_address_data'],
            $config['btn_config']['btn_delete_address_data']
        )
    ),
    'alumni' => array(
        'academic_history' => array(
            $config['btn_config']['btn_set_active']
        ),
        'document_list' => array(
            $config['btn_config']['btn_download_document'],
            $config['btn_config']['btn_remove_document']
        ),
        'address_list' => array(
            $config['btn_config']['btn_set_active']
        ),
        'job_history' => array(
            $config['btn_config']['btn_set_active']
        ),
        'job_vacancy' => array(
            $config['btn_config']['btn_close_job_vacancy']
        ),
        'my_info' => array(
            $config['btn_config']['btn_set_draft']
        )
    )
);