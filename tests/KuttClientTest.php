<?php

declare(strict_types=1);

namespace FromHome\Kutt\Tests;

use FromHome\Kutt\KuttClient;
use FromHome\Kutt\Credentials;
use PHPUnit\Framework\TestCase;
use FromHome\Kutt\Input\HealthInput;
use FromHome\Kutt\Input\ListShortLinkInput;
use FromHome\Kutt\Input\ShowShortLinkInput;
use FromHome\Kutt\Input\CreateShortLinkInput;
use FromHome\Kutt\Input\DeleteShortLinkInput;
use FromHome\Kutt\Input\UpdateShortLinkInput;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use FromHome\Kutt\Exceptions\ShortLinkNotExistException;

final class KuttClientTest extends TestCase
{
    public function testCanHealthCheck(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);
        $result = $client->healthCheck(new HealthInput());

        $this->assertSame('OK', $result);
    }

    public function testCanGetListShortLink(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $body = <<<JSON
{
  "total":1,
  "limit":10,
  "skip":0,
  "data":[
    {
      "id":"e3f0e46e-b38f-4fa2-bc19-a0327f768996",
      "address":"test-zp",
      "banned":false,
      "created_at":"2021-06-21T05:09:15.571Z",
      "updated_at":"2021-06-21T05:11:40.380Z",
      "password":false,
      "description":null,
      "expire_in":null,
      "target":"https://zakatpedia.com/qurban",
      "visit_count":0,
      "domain":null,
      "link":"https://izi.fyi/test-zp"
    }
  ]
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
            new MockResponse($body),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);

        $input = new ListShortLinkInput();
        $results = $client->listShortLink($input);

        $this->assertCount(1, $results);
        $this->assertSame('e3f0e46e-b38f-4fa2-bc19-a0327f768996', $results[0]->id);
        $this->assertSame('https://zakatpedia.com/qurban', $results[0]->target);
        $this->assertSame('https://izi.fyi/test-zp', $results[0]->link);
        $this->assertSame('test-zp', $results[0]->address);
    }

    public function testCanCreateShortLink(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $body = <<<JSON
{
      "id":"e3f0e46e-b38f-4fa2-bc19-a0327f768996",
      "address":"test-zp",
      "banned":false,
      "created_at":"2021-06-21T05:09:15.571Z",
      "updated_at":"2021-06-21T05:11:40.380Z",
      "password":false,
      "description":null,
      "expire_in":null,
      "target":"https://zakatpedia.com",
      "visit_count":0,
      "domain":null,
      "link":"https://izi.fyi/random"
    }
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
            new MockResponse($body),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);

        $input = CreateShortLinkInput::create([
            'target' => 'https://zakatpedia.com',
        ]);

        $result = $client->createShortLink($input);

        $this->assertSame('https://zakatpedia.com', $result->target);
    }

    public function testCanUpdateShortLink(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $body = <<<JSON
{
      "id":"e3f0e46e-b38f-4fa2-bc19-a0327f768996",
      "address":"test-zp",
      "banned":false,
      "created_at":"2021-06-21T05:09:15.571Z",
      "updated_at":"2021-06-21T05:11:40.380Z",
      "password":false,
      "description":null,
      "expire_in":null,
      "target":"https://zakatpedia.com/qurban",
      "visit_count":0,
      "domain":null,
      "link":"https://izi.fyi/random"
    }
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
            new MockResponse($body),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);

        $input = UpdateShortLinkInput::create([
            'id' => 'e3f0e46e-b38f-4fa2-bc19-a0327f768996',
            'target' => 'https://zakatpedia.com/qurban',
            'address' => 'test-zp',
        ]);

        $result = $client->updateShortLink($input);

        $this->assertSame('test-zp', $result->address);
        $this->assertSame('https://zakatpedia.com/qurban', $result->target);
    }

    public function testCanShowShortLink(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $body = <<<JSON
{
      "id":"e3f0e46e-b38f-4fa2-bc19-a0327f768996",
      "address":"test-zp",
      "banned":false,
      "created_at":"2021-06-21T05:09:15.571Z",
      "updated_at":"2021-06-21T05:11:40.380Z",
      "password":false,
      "description":null,
      "expire_in":null,
      "target":"https://zakatpedia.com/qurban",
      "visit_count":0,
      "domain":null,
      "link":"https://izi.fyi/random"
    }
JSON;

        $error = <<<JSON
{
    "error":"Link could not be found."
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
            new MockResponse($body),
            new MockResponse('OK'),
            new MockResponse($error, [
                'http_code' => 500,
            ]),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);

        $result = $client->detailShortLink(
            new ShowShortLinkInput('e3f0e46e-b38f-4fa2-bc19-a0327f768996')
        );
        $this->assertSame('e3f0e46e-b38f-4fa2-bc19-a0327f768996', $result->id);

        $this->expectException(ShortLinkNotExistException::class);
        $client->detailShortLink(
            new ShowShortLinkInput('e3f0e46e-b38f-4fa2-bc19-a0327f768995')
        );
    }

    public function testCanDeleteShortLink(): void
    {
        $credentials = new Credentials('https://izi.fyi', 'testing-api-key');

        $body = <<<JSON
{
  "message":"string"
}
JSON;
        $error = <<<JSON
{
  "error":"Link could not be found."
}
JSON;

        $httpClient = new MockHttpClient([
            new MockResponse('OK'),
            new MockResponse($body),
            new MockResponse('OK'),
            new MockResponse($error, [
                'http_code' => 500,
            ]),
        ], 'https://izi.fyi');

        $client = new KuttClient($credentials, $httpClient);

        $input = DeleteShortLinkInput::create([
            'id' => 'd39c1b6d-4986-4db9-a74c-39b48850bee3',
        ]);

        $client->deleteShortLink($input);

        $this->expectException(ShortLinkNotExistException::class);
        $client->detailShortLink(
            new ShowShortLinkInput('d39c1b6d-4986-4db9-a74c-39b48850bee3')
        );
    }
}
