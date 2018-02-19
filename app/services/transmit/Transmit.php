<?php

namespace App\Services\Transmit;

/**
 * Class Transmit
 * @package App\Services\Transmit
 * @author  rumur
 */
abstract class Transmit implements TransmitInterface {
	/** @var array */
	protected $store = [];

	/** @var string  */
	protected $store_code = '';

	/** @var string  */
	protected $status = 404;

	/**
	 * Message constructor.
	 *
	 * @param $code
	 * @param $data
	 */
	function __construct($code, $data)
	{
		$this->store_code = $code;

		$this->setData($data);
	}

	/**
	 * @inheritdoc
	 *
	 * @param string       $code
	 * @param array|string $data
	 *
	 * @return mixed|void
	 *
	 * @author rumur
	 */
	public static function make($code, $data)
	{
		_doing_it_wrong( __CLASS__ . '::' . __METHOD__,
			__('Method requires to be implemented', 'api'),
			null);
	}

	/**
	 * @param array|string $data
	 *
	 * @return $this|mixed
	 *
	 * @author rumur
	 */
	public function setData( $data )
	{
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$this->store[ $this->store_code ][ $key ] = $value;
			}
		} else {
			$this->store[ $this->store_code ][] = $data;
		}

		return $this;
	}

	/**
	 * @param $status
	 *
	 * @return $this|mixed
	 *
	 * @author rumur
	 */
	public function setStatus( $status )
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return array
	 *
	 * @author rumur
	 */
	protected function getAdaptedMessageToTransmit()
	{
		return [
			'data' => $this->getStore()
		];
	}

	/**
	 * @return array
	 *
	 * @author rumur
	 */
	public function getStore()
	{
		return $this->store[ $this->store_code ];
	}

	/**
	 * @return mixed|void
	 *
	 * @author rumur
	 */
	public function transmit()
	{
		_doing_it_wrong( __CLASS__ . '::' . __METHOD__,
			__('Method requires to be implemented', 'api'),
			null);
	}
}