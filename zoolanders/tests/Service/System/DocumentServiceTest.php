<?php

namespace ZFTests\Service\System;

use ZFTests\Service\SystemServiceTestLayer;
use Zoolanders\Framework\Service\System\Document;

/**
 * Class ApplicationServiceTest
 * Document service test
 *
 * @package ZFTests\Service\System
 */
class DocumentServiceTest extends SystemServiceTestLayer
{
    protected $serviceClassNames = ['JDocument', 'JDocumentHtml'];

    /**
     * Service class instance provider
     */
    protected function getServiceInstance()
    {
        return new Document(self::$container);
    }
}
