<?php

namespace App\Api\Services\Controller;

use App\Api\Services\Form\FormTrait;
use App\Api\Services\Http\{Request, Response};
use App\Api\Services\Validation\ValidationTrait;

/**
 * Class BaseController
 *
 * @since 1.1.0
 *
 * @package App\Controllers
 * @author  rumur
 */
abstract class BaseController extends \WP_REST_Controller {
	/**
	 * List of Controller's traits.
	 */
	use ValidationTrait, FormTrait;

	/** @var Request */
	protected $request;

	/** @var Response */
	protected $response;

	/**
	 * BaseController constructor.
	 */
	public function __construct()
	{
        $this->request  = \App\app( 'app.request' );
        $this->response = \App\app( 'app.response' );
	}
}
