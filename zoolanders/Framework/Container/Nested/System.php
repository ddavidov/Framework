<?php
/**
 * Created by PhpStorm.
 * User: skullbock
 * Date: 14/02/17
 * Time: 17:42
 */

namespace Zoolanders\Framework\Container\Nested;


use Zoolanders\Framework\Container\Nested;

/**
 * Class System
 * @package Zoolanders\Framework\Container\Nested
 *
 * @property-read   \Zoolanders\Framework\Service\System\Language $language
 * @property-read   \Zoolanders\Framework\Service\System\Application $application
 * @property-read   \Zoolanders\Framework\Service\System\Session $session
 * @property-read   \Zoolanders\Framework\Service\System\Document $document
 * @property-read   \Zoolanders\Framework\Service\System\Dbo $dbo
 * @property-read   \Zoolanders\Framework\Service\System\Config $config
 */
class System extends Nested
{

}