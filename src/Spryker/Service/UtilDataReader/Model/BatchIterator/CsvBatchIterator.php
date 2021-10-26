<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\UtilDataReader\Model\BatchIterator;

use Spryker\Service\UtilDataReader\Model\Reader\Csv\CsvReaderInterface;

class CsvBatchIterator implements CountableIteratorInterface
{
    /**
     * @var \Spryker\Service\UtilDataReader\Model\Reader\Csv\CsvReaderInterface
     */
    protected $csvReader;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var int
     */
    protected $chunkSize = 100;

    /**
     * @var array
     */
    protected $batchData = [];

    /**
     * @param \Spryker\Service\UtilDataReader\Model\Reader\Csv\CsvReaderInterface $csvReader
     * @param string $fileName
     * @param int $chunkSize
     */
    public function __construct(CsvReaderInterface $csvReader, $fileName, $chunkSize)
    {
        $this->csvReader = $csvReader->load($fileName);
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return \Spryker\Service\UtilDataReader\Model\Reader\Csv\CsvReaderInterface
     */
    protected function getCsvReader()
    {
        return $this->csvReader;
    }

    /**
     * @return void
     */
    protected function loadChunk()
    {
        $this->batchData = [];

        $batchSize = $this->offset + $this->chunkSize;
        if ($batchSize > $this->getCsvReader()->getTotal()) {
            $batchSize = $this->getCsvReader()->getTotal();
        }

        while ($this->getCsvReader()->valid()) {
            $this->batchData[] = $this->getCsvReader()->read();
            $this->offset++;

            if ($this->offset >= $batchSize) {
                break;
            }
        }
    }

    /**
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->batchData;
    }

    /**
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->loadChunk();
    }

    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->offset;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return !empty($this->batchData);
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->offset = 0;
        $this->getCsvReader()->rewind();
        $this->loadChunk();
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->getCsvReader()->getTotal();
    }
}
