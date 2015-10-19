<?php
/**
 * This file is part of the PHP Client for 1001 Pharmacies API.
 *
 * (c) florianajir <https://github.com/florianajir/home-api>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HomeApi\Client\Tests\HttpClient\Cache;

use Guzzle\Http\Message\Response;
use HomeApi\Client\HttpClient\Cache\FilesystemCache;

/**
 * Class FilesystemCacheTest
 *
 * @author Loïc Ambrosini <loic@florianajir.com>
 */
class FilesystemCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldStoreAResponseForAGivenKey()
    {
        $cache = new FilesystemCache('/tmp/github-api-test');

        $cache->set('test', new Response(200));

        $this->assertNotNull($cache->get('test'));
    }

    /**
     * @test
     */
    public function shouldGetATimestampForExistingFile()
    {
        $cache = new FilesystemCache('/tmp/github-api-test');

        $cache->set('test', new Response(200));

        $this->assertInternalType('int', $cache->getModifiedSince('test'));
    }

    /**
     * @test
     */
    public function shouldNotGetATimestampForInexistingFile()
    {
        $cache = new FilesystemCache('/tmp/home-api-cache');

        $this->assertNull($cache->getModifiedSince('test2'));
    }
}
