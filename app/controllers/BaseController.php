<?php

namespace App\Controllers;
use App\Services\Validation\Validation;


/**
 * Class BaseController
 * @package App\Controllers
 * @author  rumur
 */
class BaseController extends \WP_REST_Controller {
	/** @var Validation */
	protected $validation;

	/** @var \WP_REST_Response */
	protected $response;

	/**
	 * BaseController constructor.
	 */
	public function __construct()
	{
		$this->response = new \WP_REST_Response();
	}

	/**
	 * Validate the fields.
	 *
	 * @param \WP_REST_Request $request
	 * @param array            $rules
	 * 
	 * @uses  \Validation::class to validate the form fields.
	 *
	 * @return array|mixed
	 *
	 * @author rumur
	 */
	public function validate( \WP_REST_Request $request, array $rules )
	{
		$this->validation = new Validation( $request, $rules );

		return $this->validation->validate();
	}

	/**
	 * @return array
	 *
	 * @author rumur
	 */
	protected function hasFailedValidation()
	{
		return $this->validation->hasFailedFields();
	}
}