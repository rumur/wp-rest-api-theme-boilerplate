<?php

namespace App\Services\Validation;

/**
 * Class Validation
 * @package App\Services\Validation
 * @author  rumur
 */
class Validation {
	/** @var ValidationBuilder */
	protected $builder;

	/** @var \WP_REST_Request */
	protected $request;

	/** @var array */
	protected $rules;

	/** @var array */
	protected $originalFields;

	/** @var array */
	protected $validatedFields = [];

	/** @var array  */
	protected $requiredFields = [];

	/**
	 * Validation constructor.
	 *
	 * @param \WP_REST_Request $request
	 * @param array            $rules
	 */
	public function __construct(\WP_REST_Request $request, array $rules = [])
	{
		$this->request = $request;

		$this->builder = new ValidationBuilder();

		$this->setRules( $rules )->setFields( $rules )->setRequiredFields();
	}

	/**
	 * @param array $rules
	 *
	 * @return $this
	 *
	 * @author rumur
	 */
	protected function setRules(array $rules)
	{
		$this->rules = $rules;

		return $this;
	}

	/**
	 * @param array $rules
	 *
	 * @return $this
	 *
	 * @author rumur
	 */
	protected function setFields(array $rules)
	{
		$this->originalFields = array_keys( $rules );

		return $this;
	}

	/**
	 * Stored the `required` fields for further check after validation process.
	 *
   * @return $this
   *
	 * @author rumur
	 */
	protected function setRequiredFields()
	{
		array_filter( $this->rules, function ( $rule, $name ) {
			$isRequired = strpos( $rule, 'required' ) !== false;

			if ( $isRequired ) {
				$this->requiredFields[ $name ] = __( 'Required field.', 'api' );
			}
		}, ARRAY_FILTER_USE_BOTH );

		return $this;
	}

	/**
	 * Gets stored required fields.
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function getRequiredFields()
	{
		return $this->requiredFields;
	}

	/**
	 * @return array
	 *
	 * @author rumur
	 */
	public function getValidatedFields()
	{
		return $this->validatedFields;
	}

	/**
	 *
	 * @author rumur
	 */
	public function validate()
	{
		if ( $this->isSingleMode() ) {
			$key = array_shift( $this->originalFields );
			$this->validatedFields = $this->builder->single( $this->request->get_param( $key ), $this->rules[ $key ] );
		} else {
			$this->validatedFields = $this->builder->multiple( $this->request->get_params(), $this->rules);
		}

		return $this->validatedFields;
	}

	/**
	 * @return bool
	 *
	 * @author rumur
	 */
	protected function isSingleMode()
	{
		return count( $this->originalFields ) == 1;
	}

	/**
	 * Check if the required fields are empty after validation.
	 *
	 * @return array
	 *
	 * @author rumur
	 */
	public function hasFailedFields()
	{
	  // @TODO add a check for not required fields as well.
		return array_filter( $this->requiredFields, function ( $_ignore, $name ) {
			return ! $this->validatedFields[ $name ];
		}, ARRAY_FILTER_USE_BOTH );
	}
}
