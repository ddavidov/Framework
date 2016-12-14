<?php

namespace ZFTests\Service;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Service\Crypt;

/**
 * Class CryptServiceTest
 * Crypting service tests
 *
 * @package ZFTests\Service
 */
class CryptServiceTest extends ZFTestCase
{
    /**
     * Encrypt decrypt tests
     *
     * @covers          Crypt::encrypt()
     * @covers          Crypt::decrypt()
     *
     * @dataProvider    phraseDataSet
     */
    public function testEncryptDecrypt($testPhrase){
        $crypt = new Crypt(self::$container);
        $encrypted = $crypt->encrypt($testPhrase);

        $this->assertNotEmpty($encrypted);
        $this->assertNotEquals($testPhrase, $encrypted);

        $this->assertEquals($testPhrase, $crypt->decrypt($encrypted));
    }

    /**
     * Test password decryption function
     *
     * @covers          Crypt::decryptPassword()
     *
     * @dataProvider    passwordDataSet
     */
    public function testPasswordDecryption($src, $expected){
        $crypt = new Crypt(self::$container);
        $this->assertEquals($expected, $crypt->decryptPassword($src));
    }

    /**
     * Test phrases data provider
     */
    public function phraseDataSet(){
        return [
            ['alpha'],
            ['bravo'],
            ['charlie']
        ];
    }

    /**
     * Password encrypted data set
     */
    public function passwordDataSet(){
        return [
            ['zl-encrypted[ 322191012]', 'alpha']
        ];
    }
}
