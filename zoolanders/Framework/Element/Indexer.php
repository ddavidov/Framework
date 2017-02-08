<?php

namespace Zoolanders\Framework\Element;

use Zoolanders\Framework\Container\Container;

class Indexer
{
    /**
     * Available Element indexing types
     */
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TEXT = 'text';

    /**
     * @var Container
     */
    protected $container;

    /**
     * Indexer constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function getAvailableDataTypes()
    {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();

        $values = [];

        foreach ($constants as $constant => $value) {
            if (stripos($constant, 'TYPE_') === 0) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * @param \Element $element
     * @return array
     */
    public function getValuesFromElement(\Element $element)
    {
        if ($this->isRepeatable($element)) {
            $values = explode("\n", $element->getSearchData());
            return $values;
        }

        return [$element->getSearchData()];
    }

    /**
     * @param \Element $element
     * @return bool
     */
    public function isRepeatable(\Element $element)
    {
        if ($element instanceof \ElementRepeatable) {
            return true;
        }

        if ($element instanceof \ElementRepeatablePro) {
            return true;
        }

        if ($element instanceof \ElementOption) {
            return true;
        }

        return false;
    }

    /**
     * @param \Element $element
     * @return string
     */
    public function getDataTypeFromElement(\Element $element)
    {
        $type = $element->getElementType();

        switch ($type) {
            case 'rating':
                return self::TYPE_FLOAT;
            case 'text':
            case 'textpro':
            case 'select':
            case 'radio':
            case 'checkbox':
            case 'email':
                return self::TYPE_STRING;
                break;
            case 'date':
            case 'datepro':
                return self::TYPE_DATETIME;
                break;
            case 'textareapro':
            case 'textarea':
            default:
                return self::TYPE_TEXT;
                break;
        }
    }
}