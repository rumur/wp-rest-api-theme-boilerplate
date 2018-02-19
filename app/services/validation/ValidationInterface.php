<?php

namespace App\Service\Validation;

/**
 * Interface ValidationInterface
 * @package App\Service\Validation
 */
interface ValidationInterface
{
	/**
	 * Runs a validation rule on a single passed data.
	 *
	 * @param mixed $data  The given data: string, int, array, bool...
	 * @param string $rules The rules to use for validation divided by `|`
	 *
	 * @return mixed
	 */
	public function single($data, $rules);

	/**
	 * Validate multiple inputs.
	 *
	 * @param array $data  The inputs to validate.
	 * @param array $rules The rules to use for validation.
	 *
	 * @return array
	 */
	public function multiple($data,array $rules);

	/**
	 * Check if a given array is associative.
	 *
	 * @param array $arr
	 *
	 * @return bool True if associative.
	 */
	public function isAssociative(array $arr);
}
