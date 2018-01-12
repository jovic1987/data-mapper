<?php

use G4\DataMapper\Engine\Elasticsearch\ElasticsearchSelectionFactory;

class ElasticSearchSelectionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticsearchSelectionFactory
     */
    private $selectionFactory;

    private $identityMock;

    protected function setUp()
    {
        $this->identityMock = $this->getMockBuilder(\G4\DataMapper\Common\Identity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectionFactory = new ElasticsearchSelectionFactory($this->identityMock);
    }

    protected function tearDown()
    {
        $this->identityMock = null;

        $this->selectionFactory = null;
    }

    public function testSort()
    {
        $this->identityMock
            ->expects($this->once())
            ->method('getSorting')
            ->willReturn([
                $this->getMockForSort('id', 'desc'),
                $this->getMockForSort('name', 'asc'),
            ]);

        $this->assertEquals([
            ['id'   => ['order' => 'desc']],
            ['name' => ['order' => 'asc']],
        ], $this->selectionFactory->sort());
    }

    public function testEmptySort()
    {
        $this->identityMock
            ->expects($this->once())
            ->method('getSorting')
            ->willReturn([]);

        $this->assertEquals([], $this->selectionFactory->sort());
    }

    public function testWhere()
    {
        $this->identityMock
            ->expects($this->once())
            ->method('isVoid')
            ->willReturn(false);

        $this->identityMock
            ->expects($this->once())
            ->method('getComparisons')
            ->willReturn([
                $this->getMockForEqualComparison('id', 1),
                $this->getMockForEqualComparison('name', 'Test'),
                $this->getMockForGtComparison('age', 18),
            ]);

        $this->assertEquals(
            ['must' =>
                [
                    ['match' => ['id' => 1]],
                    ['match' => ['name' => 'Test']],
                    ['range' => ['age' => ['gt' => 18]]]
                ]
            ], $this->selectionFactory->where());
    }

    public function testWhereIfIdentityIsVoid()
    {
        $this->identityMock
            ->expects($this->once())
            ->method('isVoid')
            ->willReturn(true);

        $this->assertEquals(['must' => ['match_all' => []]], $this->selectionFactory->where());
    }

    private function getMockForEqualComparison($column, $value)
    {
        $mock = $this->getMockBuilder(\G4\DataMapper\Common\Selection\Comparison::class)
            ->disableOriginalConstructor()
            ->setMethods(['getComparison'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('getComparison')
            ->willReturn(['match' => [$column => $value]]);

        return $mock;
    }

    private function getMockForGtComparison($column, $value)
    {
        $mock = $this->getMockBuilder(\G4\DataMapper\Common\Selection\Comparison::class)
            ->disableOriginalConstructor()
            ->setMethods(['getComparison'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('getComparison')
            ->willReturn(['range' => [$column => ['gt' => $value]]]);

        return $mock;
    }

    private function getMockForSort($column, $sortDirection)
    {
        $mock = $this->getMockBuilder(\G4\DataMapper\Common\Selection\Sort::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSort'])
            ->getMock();

        $mock
            ->expects($this->once())
            ->method('getSort')
            ->willReturn([$column => ['order' => $sortDirection]]);

        return $mock;
    }
}
