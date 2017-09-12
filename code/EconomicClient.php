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
}
