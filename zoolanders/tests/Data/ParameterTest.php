<?php

namespace ZFTests\Data;

use Zoolanders\Framework\Data\Parameter;

/**
 * Class ParameterTest
 * Parameter data unit tests
 *
 * @package ZFTests\Data
 */
class ParameterTest extends DataTest
{
    /**
     * Make dataset for forward testing:
     */
    protected function makeDataSet($dataset){

        $this->object = new Parameter($dataset);

        return $this->object;
    }

 }
