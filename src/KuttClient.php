<?php

declare(strict_types=1);

namespace FromHome\Kutt;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use FromHome\Kutt\Input\HealthInput;
use FromHome\Kutt\ValueObject\ShortLink;
use FromHome\Kutt\Input\ListShortLinkInput;
use FromHome\Kutt\Input\ShowShortLinkInput;
use Symfony\Component\HttpClient\HttpClient;
use FromHome\Kutt\Input\CreateShortLinkInput;
use FromHome\Kutt\Input\DeleteShortLinkInput;
use FromHome\Kutt\Input\UpdateShortLinkInput;
use FromHome\Kutt\Exceptions\HealthCheckException;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use FromHome\Kutt\Exceptions\ShortLinkNotExistException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

class KuttClient implements LoggerAwareInterface
{
    private const API_VERSION = 'v2';

    protected Credentials $credentials;

    protected HttpClientInterface $httpClient;

    protected LoggerInterface $logger;

    public function __construct(
        Credentials $credentials,
        ?HttpClientInterface $httpClient = null,
        ?LoggerInterface $logger = null
    ) {
        $this->credentials = $credentials;
        $this->httpClient = $httpClient ?? HttpClient::createForBaseUri($credentials->getBaseUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'X-API-KEY' => $credentials->getKey(),
            ],
        ]);
        $this->logger = $logger ?? new NullLogger();
    }

    public static function createDefault(Credentials $credentials): self
    {
        return new self($credentials);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function healthCheck(HealthInput $input): string
    {
        $response = $this->execute(
            $input->request()
        );

        $ok = $response->getContent();

        if ($ok !== 'OK') {
            throw new HealthCheckException();
        }

        return $ok;
    }

    /**
     * @return ShortLink[]
     *
     * @throws ClientExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function listShortLink(ListShortLinkInput $input): array
    {
        $this->healthCheck(new HealthInput());

        $response = $this->execute(
            $input->request()
        );

        $rawData = $response->toArray();

        return array_map(fn (array $link) => ShortLink::fromArray($link), $rawData['data']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function createShortLink(CreateShortLinkInput $input): ShortLink
    {
        $this->healthCheck(new HealthInput());

        $response = $this->execute(
            $input->request()
        );

        $data = $response->toArray();

        return ShortLink::fromArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws ShortLinkNotExistException
     * @throws TransportExceptionInterface
     */
    public function detailShortLink(ShowShortLinkInput $input): ShortLink
    {
        $this->healthCheck(new HealthInput());

        $response = $this->execute(
            $input->request()
        );

        if ($response->getStatusCode() === 500 && $response->toArray(false)['error'] === 'Link could not be found.') {
            throw new ShortLinkNotExistException();
        }

        $data = $response->toArray();

        return ShortLink::fromArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function updateShortLink(UpdateShortLinkInput $input): ShortLink
    {
        $this->healthCheck(new HealthInput());

        $response = $this->execute(
            $input->request()
        );

        $data = $response->toArray();

        return ShortLink::fromArray($data);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws HealthCheckException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function deleteShortLink(DeleteShortLinkInput $input): void
    {
        $this->healthCheck(new HealthInput());

        $this->execute(
            $input->request()
        );
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(Request $request): ResponseInterface
    {
        $endPoint = \sprintf('/api/%s/%s', self::API_VERSION, $request->getEndPoint());

        $response = $this->httpClient->request(
            $request->getMethod(),
            $endPoint,
            [
                'query' => $request->getQuery(),
                'json' => $request->getParams(),
            ]
        );

        $this->logger->info(HttpClientInterface::class . ' debug.');
        $this->logger->info($response->getInfo('debug') ?? "");

        return $response;
    }
}
