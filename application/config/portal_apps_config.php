<?php
/**
 * config for all portal system
 * 25 september 2021 
 * budi.siswanto
 */

$config = array(
    /**
     * SMTP Config for mail
     */
    'mail_config' => array(
        'protocol' => 'smtp',
        'smtp_host' => '',
        'smtp_port' => 25,
        'smtp_user' => '',
        'smtp_pass' => '',
        // 'smtp_crypto' => 'tls',
        'smtp_timeout' => 10,
        'priority' => 1,
        'charset'   => 'utf-8'
    ),
    /**
     * other link public in portal
     */
    'whitelist_ip' => array(
        ''
    ),
    'public_link' => array(
        'online_exam_login' => '',
    ),

    /**
     * shortcut link in portal
     * temporarily as long as the view is not yet available
     */
    'shortcut_link' => array(
        'invoice_tuition_fee_per_semester' => '',
        'invoice_tuition_fee_per_semester_detail_finance' => '',
        'invoice_tuition_fee_per_semester_detail_academic' => '',
    ),

    /**
     * config notification for telegram
     * user is the recipient_id of the notification as long as there is no database to accommodate it 
     */
    'telegram_config' => array(
        'error_notification' => array(
            'key' => '',
            'token' => '',
            'user' => array(
                '',
            )
        )
    ),

    /**
     * config email smtp
     * Email format must be HTML
     */
    'smptp_config' => array(
        'protocol' => 'smtp',
        'smtp_host' => '',
        'smtp_port' => 1,
        'smtp_user' => '',
        'smtp_pass' => '',
        'mailtype'  => 'html', 
        // 'smtp_crypto' => 'tls',
        'smtp_timeout' => 10,
        'priority' => 1,
        'charset'   => 'utf-8'
    ),

    /**
     * access button or page as long as the roles user is not yet available
     * using personal data id
     */
    'allowed_page' => array(
        'letter_type_list' => array(
            '',
        ),
        'letter_number_page' => array(
            '',
        ),
        'gsr_request_page' => array(
            '',
        ),
    ),

    /**
     * BEM Apps
     * personal_data_id for member of BEM
     * temporarily as long as the view and database is not yet available
     */

    'bem_member' => array(
        'kpu' => array(
        ),
    ),

    /**
     * personal_data_id to bypass the invoice bill to 0 per semester
     * invoice has been checked by finance
     */
    'invoice_semester_paid' => array(),

    /**
     * personal_data_id for by pass reminder invoice (always send - ignoring student status)
     */
    'invoice_ignore_student_status' => array(),

    /**
     * personal_data_id for external login (ex. examiner ofse, thesis, etc)
     */
    'allowed_external_login' => array(),
    'message_finish_entrancetest' => array(
        'title' => 'Finish',
        'message' => 'Thank you for finishing the IULI English test, your answers have been saved and sent to the IULI Admission Department. You will get your result in 3 business days.<p>After this, please complete your registration data via the <a href="localhost/pmbapp" target="_blank">PMB Site</a> page with the registered email and password, if you have not completed it.</p>'
    ),


    /**
     *  list personal_data_id for student dummy
     */
    'personal_id_student_dummy' => array()
);