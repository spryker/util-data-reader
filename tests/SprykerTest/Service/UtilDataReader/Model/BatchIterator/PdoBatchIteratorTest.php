<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Service\UtilDataReader\Model\BatchIterator;

use Codeception\Test\Unit;
use Propel\Generator\Model\PropelTypes;
use Spryker\Service\UtilDataReader\Model\BatchIterator\PdoBatchIterator;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilder;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderDependencyContainer;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactory;
use Spryker\Shared\SqlCriteriaBuilder\CriteriaBuilder\CriteriaBuilderFactoryWorker;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Service
 * @group UtilDataReader
 * @group Model
 * @group BatchIterator
 * @group PdoBatchIteratorTest
 * Add your own group annotations below this line
 */
class PdoBatchIteratorTest extends Unit
{
    /**
     * @var \SprykerTest\Service\UtilDataReader\UtilDataReaderServiceTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\Kernel\Persistence\AbstractQueryContainer
     */
    protected $queryContainer;

    /**
     * @var string
     */
    protected const TESTING_TABLE_NAME = 'foo';

    /**
     * @var string
     */
    protected const TESTING_COLUMN_NAME = 'id_foo';
    protected const TESTING_COLUMN_TYPE = PropelTypes::INTEGER;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->queryContainer = $this->getMockForAbstractClass(AbstractQueryContainer::class);

        $columnsData = [[
            'name' => static::TESTING_COLUMN_NAME,
            'type' => static::TESTING_COLUMN_TYPE,
        ]];
        $this->tester->createTable(static::TESTING_TABLE_NAME, $columnsData);
    }

    /**
     * @return void
     */
    public function testCountShouldReturnNumbersOfRowsInBatch(): void
    {
        // Arrange
        $pdoBatchIterator = $this->createPdoBatchIterator();
        $expectedCount = 3;
        $this->addRowsToTestingTable($expectedCount);

        // Act
        $count = $pdoBatchIterator->count();

        // Assert
        $this->assertSame($expectedCount, (int)$count);
    }

    /**
     * @return \Spryker\Service\UtilDataReader\Model\BatchIterator\PdoBatchIterator
     */
    protected function createPdoBatchIterator(): PdoBatchIterator
    {
        $selectTestingQuery = sprintf('SELECT * FROM %s', static::TESTING_TABLE_NAME);

        $criteriaBuilderMock = new CriteriaBuilder();

        $criteriaBuilderFactoryMock = new CriteriaBuilderFactory(new CriteriaBuilderDependencyContainer());
        $criteriaFactoryWorkerMock = new CriteriaBuilderFactoryWorker($criteriaBuilderFactoryMock);

        $criteriaBuilderMock->setCriteriaBuilderFactoryWorker($criteriaFactoryWorkerMock);
        $criteriaBuilderMock->sql($selectTestingQuery);

        return new PdoBatchIterator(
            $criteriaBuilderMock,
            $this->queryContainer,
        );
    }

    /**
     * @param int $numberOfRows
     *
     * @return void
     */
    protected function addRowsToTestingTable(int $numberOfRows): void
    {
        $addRowQuery = sprintf(
            'INSERT INTO %s (%s) VALUES (?)',
            static::TESTING_TABLE_NAME,
            static::TESTING_COLUMN_NAME,
        );
        for ($i = 0; $i < $numberOfRows; $i++) {
            $statement = $this->queryContainer->getConnection()->prepare($addRowQuery);
            $statement->execute([$i]);
        }
    }
}
