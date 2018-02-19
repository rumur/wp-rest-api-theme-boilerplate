<?php

namespace App\Services\Transmit;

/**
 * Class APIResponse
 * @package App\Services\Transmit
 * @author  rumur
 */
class APIResponse extends Transmit {
	/** @var \WP_REST_Response */
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

		$self->driver = new \WP_REST_Response();

		return $self;
	}

	public function transmit()
	{
		$data = $this->getAdaptedMessageToTransmit();

		if ( ! isset( $data['code'] ) ) {
			$data['code'] = $this->store_code;
		}

		$this->driver->set_data( $data );
		$this->driver->set_status( $this->status );

		return $this->driver;
	}
}
