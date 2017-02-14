<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Service;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Zoolanders\Framework\Element\FilesInterface;

class Drive extends Service
{
    public function createFromElement(FilesInterface $element)
    {
        $config = $element->config;
        $storage = $config->get('files.storage', 'local');

        switch ($storage) {
            case 's3':
                $bucket = trim($config->find('files._s3bucket'));
                $region = trim($config->find('files._awsregion'));

                $key = trim($this->container->crypt->decrypt($config->find('files._awsaccesskey')));
                $secret = trim($this->container->crypt->decrypt($config->find('files._awssecretkey')));

                $client = new S3Client([
                    'credentials' => [
                        'key'    => $key,
                        'secret' => $secret,
                    ],
                    'region' => $region,
                    'version' => 'latest',
                ]);

                $adapter = new AwsS3Adapter($client, $bucket);
                $filesystem = new Filesystem($adapter);

                break;
            case 'local':
            default:
                $filesystem = $this->container->filesystem;
                break;
        }

        return $filesystem;
    }
}