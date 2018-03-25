<?php

namespace App\Services\Controller;

use App\Services\Http\Request;
use App\Services\Http\Response;
use App\Services\Form\FormTrait;
use App\Services\Validation\ValidationTrait;

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
        $this->request  = \App\app( 'request' );
        $this->response = \App\app( 'response' );
	}
}
