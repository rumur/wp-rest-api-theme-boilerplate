<?php

namespace App\Services\Transmit;

/**
 * Interface TransmitInterface
 * @package App\Services\Transmit
 */
interface TransmitInterface {
	/**
	 * @param string        $code
	 * @param string|array  $data
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public static function make($code, $data);

	/**
	 * @param array|string $data
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function setData($data);

	/**
	 * @param $status
	 *
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function setStatus($status);

	/**
	 * @return mixed
	 *
	 * @author rumur
	 */
	public function transmit();
}