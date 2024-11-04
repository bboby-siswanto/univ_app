<?php
class Finance extends App_core
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Finance_model', 'Fm');
		$this->load->model('portal/Portal_model', 'Pm');
		$this->load->model('portal/Staging_model', 'Sm');
	}
	
	public function sync_invoice()
	{
		$mba_invoice_data = $this->Fm->get_portal_invoice();
		print "<pre>";
		if($mba_invoice_data){
			foreach($mba_invoice_data as $invoice){
				$a_bni_billing = array(
					'trx_id' => $invoice->trx_id,
					'trx_amount' => $invoice->trx_amount,
					'client_id' => '310',
					'billing_type' => $invoice->billing_type,
					'customer_name' => $invoice->customer_name,
					'customer_email' => $invoice->customer_email,
					'customer_phone' => $invoice->customer_phone,
					'virtual_account' => $invoice->virtual_account,
					'description' => $invoice->description,
					'va_status' => $invoice->va_status,
					'payment_ntb' => $invoice->payment_ntb,
					'payment_amount' => $invoice->payment_amount,
					'cumulative_payment_amount' => $invoice->cumulative_payment_amount,
					'datetime_created' => $invoice->datetime_created,
					'datetime_expired' => $invoice->datetime_expired,
					'datetime_payment' => $invoice->datetime_payment,
					'datetime_last_updated' => $invoice->datetime_last_updated
				);
				
				$mba_check_bni_billing = $this->Sm->retrieve_data('bni_billing', array('trx_id' => $invoice->trx_id));
				if(!$mba_check_bni_billing){
					// $this->db->insert('bni_billing', $a_bni_billing);
				}
			}
		}
	}
}