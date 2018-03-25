<?php

namespace App\Services\Validation;

/**
 * Trait ValidationTrait
 * @package App\Services\Validation
 */
trait ValidationTrait {
	/** @var Validation */
	protected $validation;

	/**
	 * Validate the fields.
	 *
	 * @param array  $rules
	 *
	 * @uses  \Validation::class to validate the form fields.
	 *
	 * @return array|mixed
	 *
	 * @author rumur
	 */
	public function validate( array $rules )
	{
		$this->validation = new Validation( $this->request, $rules );

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
