<?php

class Files extends MX_Controller
{
    public $a_page_data = [];
    function __construct()
    {
        parent::__construct();
        $this->load->model('public_model', 'Pm');
    }

    public function testing($s_filesencrypt = false, $s_customfilename = false)
    {
        if (!$s_filesencrypt) {
			return show_404();
		}

		$s_file = base64_decode(urldecode($s_filesencrypt));
		$s_file_path = APPPATH.'uploads/'.$s_file;
		print($s_file);exit;
		if(!file_exists($s_file_path)){
			print('empty data');exit;
			return show_404();
		}
		else {
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			$s_mime = mime_content_type($s_file_path);
			$a_file = explode('/', $s_file_path);
			$s_download_filename = $a_file[count($a_file) - 1];
			$s_download_filename = str_replace('_', ' ', $s_download_filename).'.'.$s_file_ext;

			$s_download_filename = ($s_customfilename) ? urldecode($s_customfilename) : $s_download_filename;
			header("Content-Type: ".$s_mime);
            header('Content-Disposition: attachment; filename='.$s_customfilename);
			header("filename: ".$s_download_filename);
			
			readfile( $s_file_path );
			exit;
		}
    }

    function test() {
        $this->load->model('exam/Entrance_test_model', 'Etm');
        $a_candidate_status_allowed = ['register', 'candidate'];
        $mba_data = $this->Etm->get_candidate_exam([
            'st.finance_year_id' => '2023',
            'ec.candidate_exam_status' => 'PENDING'
        ], 'pmb');
        if ($mba_data) {
            foreach ($mba_data as $o_exam_candidate) {
                if (in_array($o_exam_candidate->student_status, $a_candidate_status_allowed)) {
                    $mba_personal_data = $this->General->get_where('dt_personal_data', ['personal_data_id' => $o_exam_candidate->personal_data_id]);
                    if ($mba_personal_data) {
                        print($mba_personal_data[0]->personal_data_name);
                        print('<br>');
                    }
                }
            }
        }

        // print('<pre>');var_dump($mba_data);exit;
    }

    public function testfile()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/contacts/contact_lists/async',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('name' => 'invitation_broadcast', 'source_type' => 'spreadsheet', 'file' => new CURLFILE('@/templates/csv_file.csv')),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer _qpMh4YpOefs3TpXjWpUSfdI1Lk6xYsSPWBk-JaxwBo'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        print('<pre>');var_dump($response);

        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://service-chat.qontak.com/api/open/v1/contacts/contact_lists/async',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS => array('name' => 'invitation_broadcast', 'source_type' => 'spreadsheet','file'=> new CURLFILE('/home/budi/Downloads/csv_file.csv')),
        // CURLOPT_HTTPHEADER => array(
        //         'Authorization: Bearer hJRpHV2bwqj2a0bVB622eYBGNOxa4n6-lpL2toqLlAA',
        //         'Content-Type:multipart/form-data'
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // print('<pre>');var_dump($response);

    }

    public function file($s_filesencrypt = false, $s_customfilename = false)
    {
        if (!$s_filesencrypt) {
			return show_404();
		}

		$s_file = base64_decode(urldecode($s_filesencrypt));
		$s_file_path = APPPATH.'uploads/'.$s_file;
		// print($s_file);exit;
		if(!file_exists($s_file_path)){
			// print('empty data');exit;
			return show_404();
		}
		else {
			$a_path_info = pathinfo($s_file_path);
			$s_file_ext = $a_path_info['extension'];
			$s_mime = mime_content_type($s_file_path);
			$a_file = explode('/', $s_file_path);
			$s_download_filename = $a_file[count($a_file) - 1];
			$s_download_filename = str_replace('_', ' ', $s_download_filename).'.'.$s_file_ext;

			$s_download_filename = ($s_customfilename) ? urldecode($s_customfilename) : $s_download_filename;
			header("Content-Type: ".$s_mime);
            // header('filename='.$s_customfilename);
			header("filename: ".$s_download_filename);
			
			readfile( $s_file_path );
			exit;
		}
    }

    public function show_digitalsign($s_document_token = false)
    {
        if (!$s_document_token) {
            show_404();
        }

        $this->load->model('apps/Letter_numbering_model', 'Lm');
        $this->load->model('apps/Gsr_model', 'Gm');
        $this->load->model('apps/Dfrf_model', 'Dm');
        $this->load->model('finance/Finance_model', 'Fim');

        $a_data = false;
        $mbo_personal_document_data = $this->Pm->get_personal_document($s_document_token);
        $mbo_student_transctipt = false;
        
        if ($mbo_personal_document_data) {
            if (!is_null($mbo_personal_document_data->letter_number_id)) {
                $mba_letter_data = $this->General->get_where('dt_letter_number', ['letter_number_id' => $mbo_personal_document_data->letter_number_id]);
                if ($mba_letter_data) {
                    $a_data = [
                        'letter_number' => $mba_letter_data[0]->letter_number_result,
                        'action_datetime' => date('d F Y H:i:s', strtotime($mbo_personal_document_data->date_added)),
                        'user_sign' => $this->General->retrieve_title($mbo_personal_document_data->personal_data_id_generated),
                        'about_letter' => $mba_letter_data[0]->letter_description,
                    ];
                }
            }
            else if (!is_null($mbo_personal_document_data->key_table)) {
                switch ($mbo_personal_document_data->key_table) {
                    case 'portal_gsr.dt_gsr_main':
                        $mba_gsr_data = $this->Gm->get_gsr_status_log(['gs.personal_document_id' => $mbo_personal_document_data->personal_document_id]);
                        if ($mba_gsr_data) {
                            $s_status = ucwords(strtolower(str_replace('ed', '', $mba_gsr_data->current_progress)));
                            $a_data = [
                                'letter_number' => $mba_gsr_data->gsr_code,
                                'action_datetime' => date('d F Y H:i:s', strtotime($mbo_personal_document_data->date_added)),
                                'user_sign' => $this->General->retrieve_title($mbo_personal_document_data->personal_data_id_generated),
                                'about_letter' => 'New '.$s_status.' GSR '.$mba_gsr_data->gsr_code,
                            ];
                        }
                        break;
                    case 'portal_main.bni_transactions':
                        $mba_transaction_data = $this->Fim->get_transaction_sign(['dpd.personal_document_id' => $mbo_personal_document_data->personal_document_id]);
                        if ($mba_transaction_data) {
                            $transaction_data = $mba_transaction_data[0];
                            $mba_transaction_data = $this->Fim->get_payment_history([
                                'bt.transaction_type' => 'paymentnotification',
                                'btp.bni_transactions_id' => $transaction_data->bni_transactions_id
                            ], [
                                'sid.sub_invoice_details_real_datetime_deadline' => 'ASC'
                            ]);

                            $a_invoice_number = [];
                            if ($mba_transaction_data) {
                                foreach ($mba_transaction_data as $o_transaction) {
                                    if (!in_array($o_transaction->invoice_number, $a_invoice_number)) {
                                        array_push($a_invoice_number, $o_transaction->invoice_number);
                                    }
                                }
                            }
                            $a_data = [
                                'letter_number' => $transaction_data->receipt_no,
                                'action_datetime' => date('d F Y H:i:s', strtotime($transaction_data->date_added)),
                                'user_sign' => 'International University Liaison Indonesia',
                                'about_letter' => 'Receipt for payment of invoice '.implode(' & ', $a_invoice_number),
                            ];
                        }
                        break;
                    
                    default:
                        break;
                }
                
                // $mba_dfrf_data = $this->Dm->get_df_status_log(['ds.personal_document_id' => $mbo_personal_document_data->personal_document_id]);
                // if ($mba_dfrf_data) {
                //     $a_data = [
                //         'letter_number' => $mba_dfrf_data->df_number,
                //         'action_datetime' => date('d F Y H:i:s', strtotime($mbo_personal_document_data->date_added)),
                //         'user_sign' => $this->General->retrieve_title($mbo_personal_document_data->personal_data_id_generated),
                //         'about_letter' => 'DF/RF Number '.$mba_dfrf_data->df_number.' current status '.$mba_dfrf_data->current_progress.' '.$mba_dfrf_data->status_action,
                //     ];
                // }
            }
        }
        else {
            $this->a_page_data['body'] = $this->load->view('public/signature/signature_not_found', $this->a_page_data, true);
        }

        $this->a_page_data['title_site'] = 'Digital Letter';
        $this->a_page_data['document'] = $a_data;
        $this->a_page_data['body'] = $this->load->view('public/signature/public_signature', $this->a_page_data, true);
        if (!$a_data) {
            $this->a_page_data['body'] = $this->load->view('public/signature/signature_not_found', $this->a_page_data, true);
        }
        $this->load->view('layout_public', $this->a_page_data);
    }

    public function get_sign($s_personal_document_id)
    {
        $mba_personal_document_data = $this->General->get_where('dt_personal_document', ['personal_document_id' => $s_personal_document_id]);
        if ($mba_personal_document_data) {
            $s_filename ='qr_view'.$mba_personal_document_data[0]->document_token.'.png';
            $s_year = date('Y', strtotime($mba_personal_document_data[0]->date_added));
            $s_month = date('M', strtotime($mba_personal_document_data[0]->date_added));
            $s_file_path = APPPATH.'uploads/qr_view/'.$s_year.'/'.$s_month.'/'.$s_filename;

            if (file_exists($s_file_path)) {
                $s_mime = mime_content_type($s_file_path);
				header("Content-Type: ".$s_mime);
                readfile( $s_file_path );
                exit;
            }
            else {
                show_404();
            }
        }
        else {
            show_404();
        }
    }

    public function download($s_category, $s_filename)
    {
        // $a_list = modules::run('file_manager/public_download', $s_category, $s_filename);
        // redirect('file_manager/public_download/'.$s_category.'/'.$s_filename);

        $s_filename = urldecode($s_filename);
		$s_filepath = APPPATH.'uploads/public/'.$s_category.'/';
        $s_fullpath = $s_filepath.$s_filename;
        
        $a_list = [
            'code' => 0,
            'fp' => $s_fullpath,
            'fn' => $s_filename
        ];

        if (empty($a_list)) {
            return show_404();
        }else if($a_list['code'] > 0){
            return show_404();
		}
		else{
            $a_path_info = pathinfo($a_list['fp']);
            // print('<pre>');
            // var_dump($a_list);exit;
			$s_file_ext = $a_path_info['extension'];
			header('Content-Disposition: attachment; filename='.urlencode($a_list['fn']));
			readfile( $a_list['fp'] );
			exit;
		}
    }

    // public function lists($s_category = 'all')
    // {
    //     $this->a_page_data['a_category'] = $this->Pm->get_category();
    //     $this->a_page_data['a_files'] = $this->Pm->get_files($s_category);
    //     // print('<pre>');
    //     // var_dump($this->a_page_data['a_files']);exit;
    //     $this->a_page_data['body'] = $this->load->view('table/file_lists', $this->a_page_data, true);
    //     $this->load->view('layout_file', $this->a_page_data);
    // }
}
