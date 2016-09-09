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
	 * @var
	 */
	protected $secretToken;

	/**
	 * @var
	 */
	protected $agreementGrantToken;

	/**
	 * @var RestClient
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

	/**
	 * Shortcut for getting all customers
	 * @return Response
	 */
	public function getCustomers($params = array('pagesize' => 400))
	{
		return $this->get('customers', $params);
	}

}