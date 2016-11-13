<?php

namespace LaDanse\RestBundle\Common;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response represents an HTTP response in JSON format.
 *
 * The Json content is created using JMS\Serializer\SerializerBuilder
 */
class JsonResponse extends Response
{
    protected $data;

    /**
     * Creates a JsonResponse representing the given $object.
     * $object must be serializable by JMS\Serializer\SerializerBuilder
     *
     * @param mixed $object  The response object
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($object = null, $status = 200, $headers = [])
    {
        parent::__construct('', $status, $headers);

        $this->setData($object);
    }

    /**
     * {@inheritdoc}
     */
    public static function create($object = null, $status = 200, $headers = [])
    {
        return new static($object, $status, $headers);
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $object
     *
     * @return JsonResponse
     */
    public function setData($object)
    {
        $serializer = SerializerBuilder::create()->build();
        $jsonContent = $serializer->serialize($object, 'json');

        $this->data = $jsonContent;

        $this->headers->set('Content-Type', 'application/json');

        return $this->setContent($this->data);
    }
}
