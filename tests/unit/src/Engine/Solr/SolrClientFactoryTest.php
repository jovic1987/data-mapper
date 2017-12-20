<?php

use G4\DataMapper\Engine\Solr\SolrClientFactory;
use G4\DataMapper\Exception\NoHostParameterException;
use G4\DataMapper\Exception\NoPortParameterException;
use G4\DataMapper\ErrorCodes as ErrorCode;

class SolrClientFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $params;

    /**
     * @var SolrClientFactory
     */
    private $clientFactory;


    protected function setUp()
    {
        $this->params = [
            'host'     => '127.0.0.1',
            'port'     => '8983',
        ];

        $this->clientFactory = new SolrClientFactory($this->params);

    }

    protected function tearDown()
    {
        $this->params = null;
        $this->clientFactory = null;
    }

    public function testCreate()
    {
        $this->assertInstanceOf(\G4\DataMapper\Engine\Solr\SolrClient::class, $this->clientFactory->create());
    }

    public function testParamsWithNoHost()
    {
        unset($this->params['host']);
        $this->expectException(NoHostParameterException::class);
        $this->expectExceptionMessage('No host parameter.');
        $this->expectExceptionCode(ErrorCode::NO_HOST_PARAMETER);
        new SolrClientFactory($this->params);
    }

    public function testParamsWithNoPort()
    {
        unset($this->params['port']);
        $this->expectException(NoPortParameterException::class);
        $this->expectExceptionMessage('No port parameter');
        $this->expectExceptionCode(ErrorCode::NO_PORT_PARAMETER);
        new SolrClientFactory($this->params);
    }
}
