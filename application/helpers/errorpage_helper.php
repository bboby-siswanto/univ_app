<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('show_403'))
{
    function show_403($log_error = FALSE, $page = '')
    {
        if (is_cli())
		{
			$heading = 'Forbidden';
			$message = "You don't have access permission to access / on this page!.";
		}
		else
		{
			$heading = '403 - Forbidden';
			$message = "You don't have access permission to access / on this page!.";
		}

		// By default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', $heading.': '.$page);
		}

		echo showerror_page($heading, $message, 'error_403', 403);
		exit(4); // EXIT_UNKNOWN_FILE
    }
}

if ( ! function_exists('iuli_message'))
{
    function iuli_message($s_heading = 'IULI Information', $s_message = 'No message found!', $b_get_page = false)
    {
		$error_page = showerror_page($s_heading, $s_message, 'message_view', 403);
		if ($b_get_page) {
			return $error_page;
		}
		else {
			echo $error_page;
			exit(4); // EXIT_UNKNOWN_FILE
		}
    }
}

if ( ! function_exists('maintenance_page'))
{
    function maintenance_page($b_get_page = false)
    {
		$helper = &get_instance();
		$a_page_data['s_header_message'] = 'Sorry';
        $a_page_data['s_body_message'] = '<p>Sorry for the inconvenience, This page is under maintenance..</p> <p>We will be back asap</p><p></p><hr>';
        $error_page = $helper->load->view('dashboard/student_error', $a_page_data, true);
        
		
		if ($b_get_page) {
			return $error_page;
		}
		else {
			echo $error_page;
			exit(4); // EXIT_UNKNOWN_FILE
		}
    }
}

if ( ! function_exists('showerror_page'))
{
    function showerror_page($heading, $message, $template = 'error_general', $status_code = 0)
    {
        $templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		if (is_cli())
		{
			$message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
			$template = 'cli'.DIRECTORY_SEPARATOR.$template;
		}
		else
		{
			set_status_header($status_code);
			$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
			$template = 'html'.DIRECTORY_SEPARATOR.$template;
		}

		if (ob_get_level() > ob_get_level() + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
    }
}