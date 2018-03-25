<?php

namespace App\Services\Form;

/**
 * Trait FormTrait
 *
 * @since 1.1.0
 *
 * @package App\Services\Form
 */
trait FormTrait {
	/**
	 * Set of the validation rules for a specific Form's field.
	 *
	 * @var array
	 */
	protected $form_rules = [];

	/**
	 * Gets the rules for specific fields from Request.
	 *
	 * @param string $rulesFor    The name of specific rule.
	 *
	 * @return array
	 *
	 * @author rumur
	 */
	abstract protected function getFormRules($rulesFor);

	/**
	 * Checks whether the rules config has a specific rule for form's field.
	 *
	 * @param string $name  Name of the From field.
	 *
	 * @return bool
	 *
	 * @author rumur
	 */
	protected function hasFieldRule($name)
	{
		return isset( $this->form_rules[ $name ] );
	}
}