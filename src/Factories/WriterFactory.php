<?php

namespace codicastudio\Excel\Factories;

use codicastudio\Excel\Cache\CacheManager;
use codicastudio\Excel\Concerns\MapsCsvSettings;
use codicastudio\Excel\Concerns\WithCharts;
use codicastudio\Excel\Concerns\WithCustomCsvSettings;
use codicastudio\Excel\Concerns\WithMultipleSheets;
use codicastudio\Excel\Concerns\WithPreCalculateFormulas;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Html;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

class WriterFactory
{
    use MapsCsvSettings;

    /**
     * @param string      $writerType
     * @param Spreadsheet $spreadsheet
     * @param object      $export
     *
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @return IWriter
     */
    public static function make(string $writerType, Spreadsheet $spreadsheet, $export): IWriter
    {
        $writer = IOFactory::createWriter($spreadsheet, $writerType);

        $writer->setUseDiskCaching(
            config('excel.cache.driver', CacheManager::DRIVER_MEMORY) !== CacheManager::DRIVER_MEMORY
        );

        if (static::includesCharts($export)) {
            $writer->setIncludeCharts(true);
        }

        if ($writer instanceof Html && $export instanceof WithMultipleSheets) {
            $writer->writeAllSheets();
        }

        if ($writer instanceof Csv) {
            static::applyCsvSettings(config('excel.exports.csv', []));

            if ($export instanceof WithCustomCsvSettings) {
                static::applyCsvSettings($export->getCsvSettings());
            }

            $writer->setDelimiter(static::$delimiter);
            $writer->setEnclosure(static::$enclosure);
            $writer->setEnclosureRequired((bool) static::$enclosure);
            $writer->setLineEnding(static::$lineEnding);
            $writer->setUseBOM(static::$useBom);
            $writer->setIncludeSeparatorLine(static::$includeSeparatorLine);
            $writer->setExcelCompatibility(static::$excelCompatibility);
        }

        // Calculation settings
        $writer->setPreCalculateFormulas(
            $export instanceof WithPreCalculateFormulas
                ? true
                : config('excel.exports.pre_calculate_formulas', false)
        );

        return $writer;
    }

    /**
     * @param $export
     *
     * @return bool
     */
    private static function includesCharts($export): bool
    {
        if ($export instanceof WithCharts) {
            return true;
        }

        if ($export instanceof WithMultipleSheets) {
            foreach ($export->sheets() as $sheet) {
                if ($sheet instanceof WithCharts) {
                    return true;
                }
            }
        }

        return false;
    }
}
