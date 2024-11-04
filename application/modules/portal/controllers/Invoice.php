<?php
class Invoice extends App_core
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Portal_model', 'Mdb');
        $this->load->model('finance/Invoice_model', 'Im');
    }

    public function get_invoice_already_in_mdb()
    {
        $mba_invoice_unpaid = $this->Mdb->get_invoice(['inv.payment_type_id' => '2'], true);
        if ($mba_invoice_unpaid) {
            $i_count = 0;
            foreach ($mba_invoice_unpaid as $key => $o_invoice) {
                $student_data = $this->Mdb->retrieve_data('student', ['id' => $o_invoice->student_id]);
                // if (!$student_data) {
                //     // $i_count++;
                //     unset($mba_invoice_unpaid[$key]);
                //     // print($o_invoice->id.'<br>');
                // }
                // if ($o_invoice->student_id == '1088') {
                //     print('<pre>');
                //     var_dump($student_data);exit;
                // }
                if ($student_data) {
                    $o_staging_student_data = $this->General->get_where('dt_student', ['portal_id' => $o_invoice->student_id]);
                    if ($o_staging_student_data) {
                        $mba_staging_student_invoice = $this->Im->student_has_invoice_data($o_staging_student_data[0]->personal_data_id, [
                            'df.semester_id' => $o_invoice->semester_id
                        ]);

                        if (!$mba_staging_student_invoice) {
                            unset($mba_invoice_unpaid[$key]);
                        }else{
                            // print($o_invoice->id.'-'.'<br>');
                            print('<pre>');var_dump($o_invoice);
                        }
                    }else{
                        unset($mba_invoice_unpaid[$key]);
                    }
                }else{
                    unset($mba_invoice_unpaid[$key]);
                }
            }

            // print('<pre>');
            // var_dump($mba_invoice_unpaid);exit;
        }
    }
}
