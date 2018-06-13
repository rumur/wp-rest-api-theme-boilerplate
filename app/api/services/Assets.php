<?php

namespace App\Services;

/**
 * Class Assets
 */
class Assets {
	/** @var null */
	protected static $instance = null;

	/** @var array */
	public $manifest;

	/** @var string */
	public $dist;

	/**
	 * Assets constructor
	 *
	 * @param string $manifestPath Local filesystem path to JSON-encoded manifest
	 * @param string $distUri Remote URI to assets root
	 */
	protected function __construct( $manifestPath, $distUri )
	{
		$this->manifest = file_exists( $manifestPath ) ? json_decode( file_get_contents( $manifestPath ), true ) : [];
		$this->dist     = $distUri;
	}

	/**
	 * Checks if there a manifest file.
	 *
	 * @return bool
	 *
	 * @author rumur
	 */
	public function hasManifest()
	{
		return ! empty( $this->manifest );
	}

	/**
	 * Make it Singleton.
	 *
	 * @param $manifestPath
	 * @param $distUri
	 *
	 * @return Assets|null
	 */
	public static function make( $manifestPath, $distUri )
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $manifestPath, $distUri );
		}

		return self::$instance;
	}

	/**
	 * Get the cache-busted filename
	 *
	 * If the manifest does not have an entry for $asset, then return $asset
	 *
	 * @param string $asset The original name of the file before cache-busting
	 * @return string
	 */
	public function get( $asset )
	{
		return isset( $this->manifest[ $asset ] ) ? $this->manifest[ $asset ] : $asset;
	}

	/**
	 * Get the cache-busted URI
	 *
	 * If the manifest does not have an entry for $asset, then return URI for $asset
	 *
	 * @param string $asset The original name of the file before cache-busting
	 * @return string
	 */
	public function getUri( $asset )
	{
		return "{$this->dist}/{$this->get($asset)}";
	}
}