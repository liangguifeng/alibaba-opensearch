<?php

declare(strict_types=1);

namespace AlibabaOpenSearch;

use AlibabaOpenSearch\Exception\HttpException;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    /**
     * @const Response Status OK
     */
    const STATUS_OK = 'OK';

    /**
     * @const Response Status FAIL
     */
    const STATUS_FAIL = 'FAIL';

    /**
     * Alibaba OpenSearch Config.
     *
     * @var Config
     */
    public Config $config;

    /**
     * Alibaba OpenSearch Client.
     *
     * @var HttpClient
     */
    public HttpClient $client;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->client = new HttpClient($config->getGuzzleConfig());
        $this->config = $config;
    }


    /**
     * Alibaba OpenSearch GET Client.
     *
     * @param $url
     * @param $queryParams
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function get($url, $queryParams)
    {
        $headers = $this->getHeaders();
        $query = $this->buildQuery($url, $queryParams);

        $signatureParams = [
            'method' => 'GET',
            'content_md5' => '',
            'content_type' => $headers['Content-Type'],
            'date' => $headers['Date'],
            'sign_headers' => $this->buildSignHeaders($headers),
            'sign_uri' => $query,
        ];

        $headers['Authorization'] = $this->authorization($signatureParams);

        $response = $this->client->get($this->config->getEndpoint() . $query, [
            'headers' => $headers,
        ]);

        $responseBody = $response->getBody()->getContents();
        $responseBodyDecode = json_decode($responseBody, true);


        // todo liangguifeng 封装一下错误处理，并且只返回result
        if ($response->getStatusCode() !== 200) {
            throw new HttpException(sprintf('Request status code is abnormal, response code: %s, response information: %s', $responseBodyDecode['errors'][0]['code'], $responseBodyDecode['errors'][0]['message']));
        }

        if ($responseBodyDecode['status'] !== self::STATUS_OK) {
            throw new HttpException(sprintf('Request status Fail, response code: %s, response information: %s', $responseBodyDecode['errors'][0]['code'], $responseBodyDecode['errors'][0]['message']));
        }

        return $responseBodyDecode['result'];
    }

    /**
     * Alibaba OpenSearch POST Client.
     *
     * @param $url
     * @param $body
     *
     * @return mixed
     * @throws GuzzleException
     */
    public function post($url, $body)
    {
        $body = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $headers = $this->getHeaders();
        $query = $this->buildQuery($url, []);

        $signatureParams = [
            'method' => 'GET',
            'content_md5' => '',
            'content_type' => $headers['Content-Type'],
            'date' => $headers['Date'],
            'sign_headers' => $this->buildSignHeaders($headers),
            'sign_uri' => $query,
        ];

        $headers['Authorization'] = $this->authorization($signatureParams);

        $response = $this->client->get($this->config->getEndpoint() . $query, [
            'headers' => $headers,
            'body' => $body,
        ]);

        $responseBody = $response->getBody()->getContents();
        $responseBodyDecode = json_decode($responseBody, true);

        if ($response->getStatusCode() !== 200) {
            throw new HttpException(sprintf('Request status code is abnormal, response code: %s, response information: %s', $responseBodyDecode['code'], $responseBodyDecode['msg']));
        }

        if ($responseBodyDecode['status'] !== self::STATUS_OK) {
            throw new HttpException(sprintf('Request status Fail, response code: %s, response information: %s', $responseBodyDecode['errors'][0]['code'], $responseBodyDecode['errors'][0]['message']));
        }

        return $responseBodyDecode;
    }

    /**
     * Generate authorization.
     *
     * @param $signatureParams
     *
     * @return string
     */
    public function authorization($signatureParams)
    {
        return sprintf('%s %s:%s', 'OPENSEARCH', $this->config->getAccessKey(), $this->signature($signatureParams));
    }

    /**
     * Generate signature.
     *
     * @param $signatureParams
     *
     * @return string
     */
    public function signature($signatureParams): string
    {
        $signatureString = implode("\n", $signatureParams);

        return base64_encode(hash_hmac('sha1', $signatureString, $this->config->getAccessSecret(), true));
    }

    /**
     * Get common Headers.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Date' => Carbon::now()->toIso8601ZuluString(),
            'X-Opensearch-Nonce' => $this->generateNonce(),
        ];
    }

    /**
     * Build Sign Headers.
     *
     * @param $headers
     *
     * @return string
     */
    protected function buildSignHeaders($headers)
    {
        $filteredHeaders = array_filter($headers, function ($key) {
            return strpos($key, 'X-Opensearch-') === 0;
        }, ARRAY_FILTER_USE_KEY);

        $nonEmptyHeaders = array_filter($filteredHeaders, function ($value) {
            return !empty($value);
        });

        uksort($nonEmptyHeaders, function ($a, $b) {
            return strcmp(strtolower($a), strtolower($b));
        });

        $canonicalHeaders = [];
        foreach ($nonEmptyHeaders as $key => $value) {
            $lowerKey = strtolower($key);
            $formattedHeader = trim("{$lowerKey}:{$value}");
            $canonicalHeaders[] = $formattedHeader;
        }

        return implode("\n", $canonicalHeaders);
    }

    /**
     * Constructing request parameters.
     *
     * @param $uri
     * @param $queryParams
     *
     * @return mixed|string
     */
    protected function buildQuery($uri, $queryParams)
    {
        if (!empty($queryParams)) {
            $queryParams = array_filter($queryParams, function ($value) {
                return !empty($value);
            });

            ksort($queryParams);

            $queryStr = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);

            $openSearchResource = $uri . '?' . $queryStr;
        } else {
            $openSearchResource = $uri;
        }

        return $openSearchResource;
    }

    /**
     * Generate 10-bit timestamp + 6-bit random value.
     *
     * @return string
     */
    private function generateNonce()
    {
        return intval(microtime(true) * 1000) . mt_rand(10000, 99999);
    }
}
