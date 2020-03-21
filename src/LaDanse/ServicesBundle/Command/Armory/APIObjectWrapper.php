<?php


namespace LaDanse\ServicesBundle\Command\Armory;


use Exception;

class APIObjectWrapper
{
    private $record;

    protected function __construct($record)
    {
        $this->record = $record;
    }

    /**
     * @param string $propertyName
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getPropertyValue1(string $propertyName)
    {
        return $this->internalGetPropertyValue($this->record, $propertyName);
    }

    /**
     * @param string $firstPropertyName
     * @param string $secondPropertyName
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getPropertyValue2(string $firstPropertyName, string $secondPropertyName)
    {
        return $this->internalGetPropertyValue(
            $this->internalGetPropertyValue(
                $this->record,
                $firstPropertyName),
            $secondPropertyName);
    }

    /**
     * @param string $firstPropertyName
     * @param string $secondPropertyName
     * @param string $thirdPropertyName
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function getPropertyValue3(string $firstPropertyName, string $secondPropertyName, string $thirdPropertyName)
    {
        return $this->internalGetPropertyValue(
                $this->internalGetPropertyValue(
                    $this->internalGetPropertyValue(
                        $this->record,
                        $firstPropertyName),
                    $secondPropertyName),
                $thirdPropertyName);
    }

    /**
     * @param $object
     * @param string $propertyName
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function internalGetPropertyValue($object, string $propertyName)
    {
        if (!property_exists($object, $propertyName))
        {
            throw new Exception(sprintf('property %s does not exist on object', $propertyName));
        }

        return $object->$propertyName;
    }
}