<?php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

/**
 *  Corresponding Class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 *  @author yourname
 */
class ClientTest extends PHPUnit_Framework_TestCase
{
    const PERSONA = '{"success":true,"data":{"idPersona":20111111111,"tipoPersona":"FISICA","tipoClave":"CUIT","estadoClave":"ACTIVO","nombre":"Jhon Doe","tipoDocumento":"DNI","numeroDocumento":"11111111","domicilioFiscal":{"direccion":"YRIGOYEN 1","localidad":"PARANA","codPostal":"3100","idProvincia":5},"idDependencia":485,"mesCierre":12,"fechaInscripcion":"1997-10-08","idCatAutonomo":103,"impuestos":[308],"actividades":[620100,475490]}}';
    const NOTFOUND = '{"success":false,"error":{ "tipoError":"client","mensaje":"No existe persona con ese Id"}}';
    const PERSONAS = '{"success":true,"data":[20111111112]}';

    protected function getGuzzleClientMock($responses)
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    /**
     * Just check if has no syntax error
     */
    public function testIsThereAnySyntaxError(){
        $httpClient = new \GuzzleHttp\Client;
        $var = new Msantang\AfipPublicApi\Client($httpClient);
        $this->assertTrue(is_object($var));
        unset($var);
    }

    /**
     *
     */
    public function testPersona()
    {
        $responses = [
            new Response(200, ['Content-Length' => 442], self::PERSONA),
            new Response(200, ['Content-Length' => 90], self::NOTFOUND)
        ];

        $clientMock = $this->getGuzzleClientMock($responses);

        // test correct response
        $var = new Msantang\AfipPublicApi\Client($clientMock);
        $r   = $var->persona("20111111111");
        $this->assertTrue($r->numeroDocumento == '11111111');

        // test not found
        $this->expectException(Msantang\AfipPublicApi\Exceptions\NotFoundException::class);
        $r   = $var->persona("20111111111");
    }

    public function testPersonaCommError()
    {
        $responses = [
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ];

        $clientMock = $this->getGuzzleClientMock($responses);

        $var = new Msantang\AfipPublicApi\Client($clientMock);

        // test http comm error
        $this->expectException(Msantang\AfipPublicApi\Exceptions\Exception::class);
        $r   = $var->persona("20111111111");
    }

    public function testPersonas()
    {
        $responses = [
            new Response(200, ['Content-Length' => 37], self::PERSONAS),
            new Response(200, ['Content-Length' => 90], self::NOTFOUND)
        ];

        $clientMock = $this->getGuzzleClientMock($responses);

        // test correct response
        $var = new Msantang\AfipPublicApi\Client($clientMock);
        $r   = $var->persona("11111111");
        $this->assertTrue($r == ['20111111112']);

        // test not found
        $this->expectException(Msantang\AfipPublicApi\Exceptions\NotFoundException::class);
        $r   = $var->persona("11111111");
    }

    public function testPersonasByDni()
    {
        $responses = [
            new Response(200, ['Content-Length' => 37], self::PERSONAS),
            new Response(200, ['Content-Length' => 90], self::PERSONA),
            new Response(200, ['Content-Length' => 90], self::NOTFOUND)
        ];

        $clientMock = $this->getGuzzleClientMock($responses);

        // test correct response
        $var = new Msantang\AfipPublicApi\Client($clientMock);
        $r   = $var->personaByDni("11111111");
        $this->assertTrue($r->numeroDocumento == '11111111');

        // test not found
        $this->expectException(Msantang\AfipPublicApi\Exceptions\NotFoundException::class);
        $r   = $var->persona("11111111");
    }
}