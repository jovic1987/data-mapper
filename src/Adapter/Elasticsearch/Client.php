<?php

namespace G4\DataMapper\Adapter\Elasticsearch;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\Namespaces\IndicesNamespace;
use G4\DataMapper\Selection\Elasticsearch\Factory as SelectionFactory;

class Client
{

    /**
     * @var ElasticsearchClient
     */
    private $client;

    /**
     * @var IndicesNamespace
     */
    private $clientIndices;

    /**
     * @var string
     */
    private $index;

    /**
     * @var array
     */
    private $hosts;

//     private $profiler;

    /**
     * @var string
     */
    private $type;


    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->filterParams($params);
        $this->client        = ClientBuilder::create()->setHosts($this->hosts)->build();
        $this->clientIndices = $this->client->indices();
    }

    public function putMapping(SelectionFactory $selectionFactory)
    {
        if (!$this->clientIndices->exists($selectionFactory->prepareIndex())) {
            $this->clientIndices->create($selectionFactory->prepareIndex());
        }
        return $this->clientIndices->putMapping($selectionFactory->prepareForMapping());
    }

    public function deleteMapping(SelectionFactory $selectionFactory)
    {
        return $this->clientIndices->exists($selectionFactory->prepareIndex()) && $this->clientIndices->existsType($selectionFactory->prepareType())
            ? $this->clientIndices->deleteMapping($selectionFactory->prepareType())
            : true;
    }

    public function index(SelectionFactory $selectionFactory)
    {
        return $this->client->index($selectionFactory->prepareForIndexing());
    }

//     public function get($params)
//     {
//         return $this->client->get($params);
//     }

    public function search(SelectionFactory $selectionFactory)
    {
        try {
//             echo '<pre>';print_r($selectionFactory->query());
            $result = $this->client->search($selectionFactory->query());
        } catch (\Exception $e) {
//             var_dump($e);
        }
        return $result;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getType()
    {
        return $this->type;
    }

//     public function delete()
//     {
//         return $this->client->delete();
//     }

//     public function create($params)
//     {
//         return $this->client->create($params);
//     }

    /**
     * @param array $params
     */
    private function filterParams(array $params)
    {
        $this->index = $params['index'];
        $this->type  = $params['type'];
        $this->hosts = $params['hosts'];
    }
}