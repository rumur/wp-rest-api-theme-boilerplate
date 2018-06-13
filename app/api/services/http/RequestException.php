<?php
namespace App\Api\Services\Http;

use Throwable;

class RequestException extends \Exception {
    /** @var array  */
    protected $data = [];

    /**
     * RequestException constructor.
     * @param string $message
     * @param array  $data
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message = "", array $data, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->setData($data);
    }

    /**
     * @param array $data
     *
     * @author rumur
     */
    public function setData(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * @return array
     *
     * @author rumur
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     *
     * @author rumur
     */
    public function getResponseData()
    {
        return [
            'data' => $this->getData(),
            'handle' => uniqid('api-'),
            'message' => $this->getMessage(),
        ];
    }
}
