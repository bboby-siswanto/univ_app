<?php
class Sponsor extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('finance/Invoice_model', 'Im');
    }

    public function index()
    {
        $datenow = date('Y-m-d H:i:s');
        $dateevent = date('Y-m-d H:i:s', strtotime('2022-06-19 23:59:59'));
        if ($datenow > $dateevent) {
            $this->a_page_data['body'] = $this->load->view('sponsor/event_closed', $this->a_page_data, true);
        }
        else {
            $this->a_page_data['body'] = $this->load->view('sponsor/page_sponsor', $this->a_page_data, true);
        }
        $this->load->view('layout_public', $this->a_page_data);
    }

    public function list()
    {
        $this->a_page_data['body'] = $this->load->view('sponsor/list_sponsor', $this->a_page_data, true);
        $this->load->view('layout_public', $this->a_page_data);
    }

    public function get_invoice_sponsor()
    {
        if ($this->input->is_ajax_request()) {
            $mba_invoice_sponsor_list = $this->Im->get_invoice_open_payment();
            if ($mba_invoice_sponsor_list) {
                foreach ($mba_invoice_sponsor_list as $o_sponsor) {
                    $o_sponsor->payment_amount = number_format($o_sponsor->sub_invoice_details_amount_total, 0, '.', ',');
                    $o_sponsor->paid_amount = number_format($o_sponsor->sub_invoice_details_amount_paid, 0, '.', ',');
                }
            }
            print json_encode(['data' => $mba_invoice_sponsor_list]);
        }
    }

    public function get_invoice_counter()
    {
        $test = $this->Im->get_latest_invoice_number('', 'F220619');
        print("<pre>");var_dump($test);exit;
    }

    public function generate_invoice($s_invoice_number)
    {
        $s_invoice_number = str_replace('_', '-', $s_invoice_number);
        $mba_invoice_data = $this->General->get_where('dt_invoice', ['invoice_number' => $s_invoice_number]);
        if ($mba_invoice_data) {
            modules::run('download/Pdf_download/generate_invoice_billing', $mba_invoice_data[0]->invoice_id);
        }
        else {
            print('Cannt download invoice, please contact the officer!');
        }
    }

    public function create_invoice()
    {
        if ($this->input->is_ajax_request()) {
            $s_sponsor_name = $this->input->post('sponsor_name');
            $s_sponsor_email = $this->input->post('sponsor_email');
            $s_sponsor_amount = $this->input->post('sponsor_amount');
            $a_amount = $this->get_amount($s_sponsor_amount, false);
            $d_amount = $a_amount['am'];
            $d_amount = ($d_amount == '') ? 0 : $d_amount;
            // var_dump($d_amount['am']);exit;
            $this->load->model('personal_data/Personal_data_model', 'Pdm');

            // if ((empty($s_sponsor_name)) OR (empty($s_sponsor_email)) OR (empty($s_sponsor_amount))) {
            if ((empty($s_sponsor_name)) OR (empty($s_sponsor_email))) {
                $a_return = ['code' => 1, 'message' => 'Please fill the require field!!'];
            }
            // else if (doubleval($d_amount) < 10000) {
            //     $a_return = ['code' => 1, 'message' => 'Minimum transfer IDR 10.000,-'];
            // }
            else {
                $mba_sponsor_data = $this->General->get_where('dt_personal_data', ['personal_data_name' => $s_sponsor_name]);
                if (!$mba_sponsor_data) {
                    $s_personal_data_id = $this->uuid->v4();
                    $a_sponsor_data = [
                        'personal_data_id' => $s_personal_data_id,
                        'personal_data_name' => $s_sponsor_name,
                        'personal_data_email' => $s_sponsor_email,
                        'personal_data_cellular' => 0
                    ];
                    $this->Pdm->create_personal_data_parents($a_sponsor_data);
                }
                else {
                    $s_personal_data_id = $mba_sponsor_data[0]->personal_data_id;
                }

                $s_invoice_number_prefix = 'F220619';
                $s_invoice_id = $this->uuid->v4();
                $s_sub_invoice_id = $this->uuid->v4();
                $s_sub_invoice_details_id = $this->uuid->v4();
                $s_invoice_counter = $this->Im->get_latest_invoice_number('', $s_invoice_number_prefix);
                $s_invoice_number = str_pad($s_invoice_counter, 4, "0", STR_PAD_LEFT);
                $s_invoice_number = 'INV-'.$s_invoice_number_prefix.$s_invoice_number;

                $a_invoice_data = [
                    'invoice_id' => $s_invoice_id,
                    'personal_data_id' => $s_personal_data_id,
                    'invoice_customer' => $s_sponsor_name,
                    'invoice_number' => $s_invoice_number,
                    'invoice_amount_paid' => 0,
                    'invoice_description' => 'IULIFest Invoice',
                    'invoice_allow_fine' => 'no',
                    'invoice_allow_reminder' => 'no',
                    'invoice_note' => NULL,
                ];

                $a_sub_invoice_data = [
                    'sub_invoice_id' => $s_sub_invoice_id,
                    'invoice_id' => $s_invoice_id,
                    'sub_invoice_amount' => $d_amount,
                    'sub_invoice_amount_total' => $d_amount
                ];

                $a_sub_invoice_details_data = [
                    'sub_invoice_details_id' => $s_sub_invoice_details_id,
                    'trx_id' => '2050332246',
                    'sub_invoice_id' => $s_sub_invoice_id,
                    'sub_invoice_details_amount' => $d_amount,
                    'sub_invoice_details_amount_total' => $d_amount,
                    'sub_invoice_details_va_number' => '8310884920220619',
                    'sub_invoice_details_deadline' => date('Y-m-d H:i:s', strtotime('2022-06-19 23:59:59')),
                    'sub_invoice_details_real_datetime_deadline' => date('Y-m-d H:i:s', strtotime('2022-06-19 23:59:59')),
                    'sub_invoice_details_description' => 'Sponsorship IULIFest Payment'
                ];

                $this->Im->create_invoice($a_invoice_data);
                $this->Im->create_sub_invoice($a_sub_invoice_data);
                $create_invoice = $this->Im->create_sub_invoice_details($a_sub_invoice_details_data);
                if ($create_invoice) {
                    $direct_url = base_url().'apps/sponsor/generate_invoice/'.str_replace('-', '_', $s_invoice_number);
                    $a_return = ['code' => 0, 'inumber' => str_replace('-', '_', $s_invoice_number), 'message' => 'Success', 'redirectURL' => $direct_url];
                }
                else {
                    $a_return = ['code' => 1, 'message' => 'Failed create invoice, please contact the officer!'];
                }
            }

            print json_encode($a_return);
        }
    }

    public function get_amount($s_amount = 0, $b_is_ajax = true)
    {
        if ($b_is_ajax) {
            $s_amount = $this->input->post('amount');
        }

        $d_new_amount = 0;
        if (doubleval($s_amount) >= 10000) {
            $last_invoice = $this->Im->get_count_invoice_open_payment();
            if ($last_invoice) {
                $d_last_amount = intval(substr($last_invoice->sub_invoice_details_amount, (strlen($last_invoice->sub_invoice_details_amount) - 4)));
            }
            else {
                $d_last_amount = 0;
            }
            $d_last_amount++;
            $d_last_amount = str_pad($d_last_amount, 4, "0", STR_PAD_LEFT);
            $d_new_amount = substr($s_amount, 0, (intval(strlen($s_amount) - 4)));
            $d_new_amount = $d_new_amount.$d_last_amount;
        }

        $a_return = ['am' => $d_new_amount];

        if ($b_is_ajax) {
            print json_encode($a_return);
        }
        else {
            return $a_return;
        }
    }
}
