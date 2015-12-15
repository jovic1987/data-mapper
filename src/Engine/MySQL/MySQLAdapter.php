<?php

namespace G4\DataMapper\Engine\MySQL;

use G4\DataMapper\Common\AdapterInterface;
use G4\DataMapper\Common\MappingInterface;
use G4\DataMapper\Engine\MySQL\MySQLClientFactory;
use Zend_Db_Adapter_Abstract;
use Zend_Db;
use G4\DataMapper\Common\SelectionFactoryInterface;

class MySQLAdapter implements AdapterInterface
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $client;


    public function __construct(MySQLClientFactory $clientFactory)
    {
        $this->client = $clientFactory->create();
    }

    public function delete($table, MappingInterface $mapping)
    {
        $identifiers = $mapping->identifiers();

        if (empty($identifiers)) {
            throw new \Exception('Empty identifiers for delete', 101);
        }

        $this->client->delete($table, http_build_query($identifiers, '', ' AND '));
    }

    public function insert($table, MappingInterface $mappings)
    {
        $data = $mappings->map();

        if (empty($data)) {
            throw new \Exception('Empty data for insert', 101);
        }

        $this->client->insert($table, $data);
    }

    public function select($table, SelectionFactoryInterface $selectionFactory)
    {
        $select = $this->client
            ->select()
            ->from($table, $selectionFactory->fields())
            ->where($selectionFactory->where())
            ->order($selectionFactory->sort())
            ->limit($selectionFactory->limit());

        $this->client->fetchAll($select);
    }

    public function update($table, MappingInterface $mapping)
    {
        $data = $mapping->map();

        if (empty($data)) {
            throw new \Exception('Empty data for update', 101);
        }

        $identifiers = $mapping->identifiers();

        if (empty($identifiers)) {
            throw new \Exception('Empty identifiers for update', 101);
        }

        $this->client->update($table, $data, http_build_query($identifiers, '', ' AND '));
    }
}