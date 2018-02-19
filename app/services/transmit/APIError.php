<?php

namespace App\Services\Transmit;

/**
 * Class APIError
 * @package App\Services
 * @author  rumur
 */
class APIError extends Transmit {
	/** @var \WP_Error */
	protected $driver;

	/**
	 * @param string       $code
	 * @param array|string $data
	 *
	 * @return APIResponse|mixed
	 *
	 * @author rumur
	 */
	public static function make( $code, $data )
	{
		$self = new self( $code, $data );

		/** @var  \WP_Error */
		$self->driver = new \WP_Error();

		return $self;
	}

	/**
	 * Shows the the User Browser.
	 *
	 * @return mixed|\WP_Error
	 *
	 * @author rumur
	 */
	public function transmit()
	{
		$data = $this->getAdaptedMessageToTransmit();

		// Make a balance of the `data`, the WP_Error adds it itself
		$data = $data['data'];

		if ( is_array( $data ) && ! $this->isAssociative( $data ) ) {
			// Take it the first one
			$data['message'] = array_shift( $data );
		}

		if ( ! isset( $data['message'] ) ) {
			// We have to add at least en empty error.
			// otherwise the WP REST API will throw a Notice Exception.
			$this->driver->errors[ $this->store_code ] = '';
		} else {
			$this->driver->errors[ $this->store_code ] = $data['message'];
			// Remove it, we don't want to duplicate it.
			unset($data['message']);
		}

		if ( ! isset( $data['status'] ) ) {
			$data['status'] = $this->status;
		}

		$this->driver->error_data[ $this->store_code ] = $data;

		return $this->driver;
	}

	/**
	 * Check if a given array is associative.
	 *
	 * @param array $arr
	 *
	 * @return bool True if associative.
	 */
	public function isAssociative(array $arr)
	{
		if (empty($arr)) return false;
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

}
