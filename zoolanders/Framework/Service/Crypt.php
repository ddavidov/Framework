<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

use Zoolanders\Framework\Container\Container;

class Crypt extends Service
{
    /**
     * @var \JCrypt
     */
    public $crypt;

    /**
     * Crypt constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        parent::__construct($c);

        $secret = $this->container->system->config->get('secret');

        $key = new \JCryptKey('simple', $secret, $secret);
        $this->crypt = new \JCrypt(null, $key);
    }

    /**
     * Encrypt text
     * @param $text
     */
    public function encrypt($text)
    {
        return $this->crypt->encrypt($text);
    }

    /**
     * Decrypt text
     * @param $text
     */
    public function decrypt($text)
    {
        return $this->crypt->decrypt($text);
    }

    /**
     * Password field decryption
     *
     * @param  string $pass The encrypted password to decrypt
     *
     * @return string The decrypted password
     *
     * @since 3.0.3
     */
    public function decryptPassword($pass)
    {
        $matches = array();
        if (preg_match('/zl-encrypted\[(.*)\]/', $pass, $matches)) {
            return $this->crypt->decrypt($matches[1]);
        }

        // if no valid pass to decrypt, return orig pass
        return $pass;
    }
}
