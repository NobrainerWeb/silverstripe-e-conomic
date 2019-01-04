<?php

namespace NobrainerWeb\Economic;

use Lenius\Economic\API\Response;
use Lenius\Economic\RestClient;

/**
 *
 * Class EconomicClient
 * @package NobrainerWeb\Economic
 */
class EconomicClient extends \Object
{
	/**
	 * @config
	 */
	protected static $secretToken;

	/**
	 * @config
	 */
	protected static $agreementGrantToken;

	/**
	 * @var string
	 */
	private static $currency;

	/**
	 * TODO this should be able to be chosen for each invoice
	 * @var int
	 */
	private static $invoice_layout_number;

	/**
	 * paymentTermsNumber is required by e-conomic, the other keys are not.
	 * @var array
	 */
	private static $payment_terms = [
		'paymentTermsNumber' => null,
		'daysOfCredit'       => null,
		'name'               => null,
		'paymentTermsType'   => null
	];

	/**
	 * @object RestClient
	 */
	protected $client;

	/**
	 * JSON data
	 * @var
	 */
	protected $response;

	public function __construct()
	{
		parent::__construct();

		$this->client = new RestClient($this->getSecretToken(), $this->getAgreementGrantToken());

		return $this;
	}

	protected function getSecretToken()
	{
		return $this->config()->secretToken;
	}

	protected function getAgreementGrantToken()
	{
		return $this->config()->agreementGrantToken;
	}

	/**
	 * @return int
	 */
	public function getCurrency()
	{
		return $this->config()->currency;
	}

	/**
	 * @return int
	 */
	public function getInvoiceLayoutNumber()
	{
		return $this->config()->invoice_layout_number;
	}

	public function getPaymentTerms()
	{
		return $this->config()->payment_terms;
	}

	/**
	 * @return RestClient
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->response;
	}

	public function request()
	{
		return $this->getClient()->request;
	}

	public function get($string, $params = null)
	{
		$this->response = $this->request()->get($string, $params);

		return $this->getResponse();
	}

	public function post($string, $params = null)
	{
		$this->response = $this->request()->post($string, $params);

		return $this->getResponse();
	}

	public function put($string, $params = null)
	{
		$this->response = $this->request()->put($string, $params);

		return $this->getResponse();
	}

	public function delete($string)
	{
		$this->response = $this->request()->delete($string);

		return $this->getResponse();
	}

	/**
	 * Shortcut for getting all customers
	 * @return Response
	 */
	public function getCustomers($params = array('pagesize' => 400))
	{
		return $this->get('customers', $params);
	}

	/**
	 * Create customer in e-conomic
	 * @return Response
	 */
	public function createCustomer($params)
	{
		return $this->post('customers', $params);
	}

	/**
	 * Update customer in e-conomic
	 * @return Response
	 */
	public function updateCustomer($customerID, $params)
	{
		return $this->put('customers/' . $customerID, $params);
	}

    /**
     * Get customer from customerID
     * @return Response
     */
    public function getCustomer($customerID){
        return $this->get('/customers/'.$customerID);
    }

    /**
     * Get customer from email
     * @return Response
     */
    public function getCustomerByEmail($email){
        return $this->get('/customers?filter=email$eq:'.$email);
    }

	/**
	 * Delete customer in e-conomic
	 * @return Response
	 */
	public function deleteCustomer($customerID)
	{
		return $this->delete('customers/' . $customerID);
	}

	/**
	 * Create draft invoice
	 * @return Response
	 */
	public function createInvoiceDraft($params)
	{
		return $this->post('invoices/drafts', $params);
	}

	/**
	 * Book draft invoice
	 * @return Response
	 */
	public function bookInvoiceDraft($params)
	{
		return $this->post('invoices/booked', $params);
	}

    /**
     * Get a booker invoice in e-conomic
     * @return Response
     */
    public function getBookedInvoice($invoiceID){
        return $this->get('invoices/booked/'.$invoiceID);
    }

    /**
     * Get all created product groups in eConomic
     * @return Response
     */
	public function getProductGroups($params = null){
	    return  $this->get('product-groups',$params);
    }

    /**
     * Get product
     * @return Response
     */
    public function getProduct($SKU){
        return  $this->get('products/'.$SKU);
    }

    /**
     * Create product
     * @return Response
     */
    public function createProduct($params){
        return  $this->post('products',$params);
    }

    /**
     * Update product
     * @return Response
     */
    public function updateProduct($SKU,$params){
        return  $this->put('products/'.$SKU,$params);
    }

    /**
     * Create order in e-conomic
     * @return Response
     */
    public function createOrder($params){
        return  $this->post('orders/drafts',$params);
    }

    /**
     * Get all orders in e-conomic
     * @return Response
     */
    public function getOrdersDraft(){
        return  $this->get('orders/drafts');
    }

    /**
     * Get order in e-conomic
     * @return Response
     */
    public function getOrderDraft($orderNumber){
        return  $this->get('orders/drafts/'.$orderNumber);
    }

    /**
     * Mark drafted order as sent
     * @return Response
     */
    public function sendOrderDraft($orderNumber){
        $orderDocument = $this->get('orders/drafts/'.$orderNumber)->asArray();
        return $this->post('orders/sent/'.$orderNumber,$orderDocument)->asArray();
    }

    /**
     * Delete drafted order
     * @return Response
     */
    public function deleteOrderDraft($orderNumber){
        return $this->delete('orders/drafts/'.$orderNumber);
    }

    /**
     * Delete sent order
     * @return Response
     */
    public function deleteSentOrderDraft($orderNumber){
        return $this->delete('orders/sent/'.$orderNumber);
    }

    /**
     * Book draft order
     * @return String
     */
    public function bookDraftOrder($orderNumber){
        //Get orderdraft
        $params = $this->getOrderDraft($orderNumber)->asArray();

        //Mark as sent
        $this->sendOrderDraft($params);

        //Then create invoice draft and book the invoice
        $invoiceNumber = $this->bookSentOrder($orderNumber);
        return $invoiceNumber;
    }

    /**
     * Book sent order
     * @return String
     */
    public function bookSentOrder($orderNumber){
        $invoiceTemplate = $this->get('orders/sent/'.$orderNumber.'/templates/upgrade-instructions/draftInvoice')->asArray();
        $params = $invoiceTemplate['draftInvoice'];

        //Remove metaData that often is empty and breaks creation
        unset($params['metaData']);
        foreach ($params['lines'] as $key => $line){
            unset($params['lines'][$key]['metaData']);
        }

        //Create invoice draft
        $draft = $this->createInvoiceDraft($params)->asArray();
        $draftInvoiceNumber = $draft['draftInvoiceNumber'];

        //Book drafted invoice
        $params = [
            'draftInvoice' => [
                'draftInvoiceNumber' => $draftInvoiceNumber
            ]
        ];
        $invoice = $this->bookInvoiceDraft($params)->asArray();

        return $invoice['bookedInvoiceNumber'];
    }
}
