<?php

namespace codicastudio\Excel\Imports;

use codicastudio\Excel\Concerns\ToModel;
use codicastudio\Excel\Concerns\WithBatchInserts;
use codicastudio\Excel\Concerns\WithCalculatedFormulas;
use codicastudio\Excel\Concerns\WithColumnLimit;
use codicastudio\Excel\Concerns\WithMapping;
use codicastudio\Excel\Concerns\WithProgressBar;
use codicastudio\Excel\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ModelImporter
{
    /**
     * @var ModelManager
     */
    private $manager;

    /**
     * @param ModelManager $manager
     */
    public function __construct(ModelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Worksheet   $worksheet
     * @param ToModel     $import
     * @param int|null    $startRow
     * @param string|null $endColumn
     *
     * @throws \codicastudio\Excel\Validators\ValidationException
     */
    public function import(Worksheet $worksheet, ToModel $import, int $startRow = 1)
    {
        if ($startRow > $worksheet->getHighestRow()) {
            return;
        }

        $headingRow       = HeadingRowExtractor::extract($worksheet, $import);
        $batchSize        = $import instanceof WithBatchInserts ? $import->batchSize() : 1;
        $endRow           = EndRowFinder::find($import, $startRow, $worksheet->getHighestRow());
        $progessBar       = $import instanceof WithProgressBar;
        $withMapping      = $import instanceof WithMapping;
        $withCalcFormulas = $import instanceof WithCalculatedFormulas;
        $endColumn        = $import instanceof WithColumnLimit ? $import->endColumn() : null;

        $this->manager->setRemembersRowNumber(method_exists($import, 'rememberRowNumber'));

        $i = 0;
        foreach ($worksheet->getRowIterator($startRow, $endRow) as $spreadSheetRow) {
            $i++;

            $row      = new Row($spreadSheetRow, $headingRow);
            $rowArray = $row->toArray(null, $withCalcFormulas, true, $endColumn);

            if ($withMapping) {
                $rowArray = $import->map($rowArray);
            }

            $this->manager->add(
                $row->getIndex(),
                $rowArray
            );

            // Flush each batch.
            if (($i % $batchSize) === 0) {
                $this->manager->flush($import, $batchSize > 1);
                $i = 0;

                if ($progessBar) {
                    $import->getConsoleOutput()->progressAdvance($batchSize);
                }
            }
        }

        // Flush left-overs.
        $this->manager->flush($import, $batchSize > 1);
    }
}
