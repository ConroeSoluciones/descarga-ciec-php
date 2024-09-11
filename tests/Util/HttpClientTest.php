<?php

declare(strict_types=1);

namespace Csfacturacion\Test\Descarga\Util;

use Csfacturacion\Descarga\Model\HttpRequest;
use Csfacturacion\Descarga\Util\HttpClient;
use Csfacturacion\Test\Descarga\TestCase;

use function sys_get_temp_dir;
use function usleep;

class HttpClientTest extends TestCase
{
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpClient = new HttpClient();
    }

    public function testSend(): void
    {
        $req = new HttpRequest('https://httpbin.org/anything');
        $resp = $this->httpClient->send($req);
        $payload = $resp->bodyAsArray();

        $this->assertTrue($resp->isOk());
        $this->assertJson($resp->getRawResponse());

        $this->assertEquals($req->getHeader('Accept'), (string) $payload['headers']['Accept']);
        $this->assertEquals($req->getMethod()->name, $payload['method']);
        $this->assertEmpty($payload['json']);
    }

    public function testSendAndSave(): void
    {
        $req = new HttpRequest('https://httpbin.org/bytes/102400');
        $toSave = sys_get_temp_dir() . '/demo.bin';

        $this->httpClient->sendAndSave($req, $toSave, function (int $dlNow, int $dlSize, array $info) {
            if ($dlSize && $dlNow > 0) {
                echo "downloading...\n";
            }
            usleep(100);
        });

        $this->assertFileExists($toSave);
    }
}
