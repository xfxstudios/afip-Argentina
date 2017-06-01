<?php
namespace Msantang\AfipPublicApi;
use GuzzleHttp\Exception\RequestException;
use Msantang\AfipPublicApi\Exceptions\Exception;
use Msantang\AfipPublicApi\Exceptions\NotFoundException;

/**
 *  Api Client
 *
 *  @author Martin Alejandro Santangelo
 */
class Client
{
    // if not work try with 'https://soa.afip.gob.ar/sr-padron/v2/';
    // if not work try with 'https://aws.afip.gov.ar/sr-padron/v2/';
    const DOMAIN = 'https://aws.afip.gov.ar/sr-padron/v2/';
    /**
     * @var GuzzleHttp\Client $client
     */
    private $client = null;

    /**
     * Class constructor
     * @param \GuzzleHttp\Client $client client
     */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Call remote http api
     * @param  string $method Method name
     * @param  string $param  prameters
     * @return
     */
    protected function callApi($method, $param)
    {
        $url = self::DOMAIN.$method;

        if ($param) $url .= '/'.$param;

        try {
            $res = $this->client->request('GET', $url, [
                'headers' => [
                    'User-Agent'                => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
                ]
            ]);
        } catch (RequestException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        $json = json_decode($res->getBody()->getContents());

        if (!$json) {
            throw new Exception('Error in response from server (not json)');
        }

        if ($json->success == 0) {
            throw new NotFoundException($json->error->mensaje);
        }

        return $json->data;
    }

    /**
     * Return fisical/fiscal person data searching by cuit/cuil
     *
     * @param int $doc
     *
     * @return string JSON
     */
    public function persona($doc)
    {
        return $this->callApi('persona', $doc);
    }

    /**
     * Search a person by document nomber $dni
     *
     * @param  int $dni Document number
     * @return stdClass
     */
    public function personaByDni($dni)
    {
        $cuil = $this->personas($dni);
        return $this->personas($cuil[0]);
    }

    /**
     * Return cuil of person/s with $dni document number
     *
     * @param int $doc Document number
     *
     * @return stdClass
     */
    public function personas($dni)
    {
        return $this->callApi('personas', $dni);
    }
}