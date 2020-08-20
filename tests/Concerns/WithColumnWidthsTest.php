<?php

namespace codicastudio\Excel\Tests\Concerns;

use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromArray;
use codicastudio\Excel\Concerns\WithColumnWidths;
use codicastudio\Excel\Tests\TestCase;

class WithColumnWidthsTest extends TestCase
{
    /**
     * @test
     */
    public function can_set_column_width()
    {
        $export = new class implements FromArray, WithColumnWidths {
            use Exportable;

            public function columnWidths(): array
            {
                return [
                    'A' => 55,
                ];
            }

            public function array(): array
            {
                return [
                    ['AA'],
                    ['BB'],
                ];
            }
        };

        $export->store('with-column-widths.xlsx');

        $spreadsheet = $this->read(__DIR__ . '/../Data/Disks/Local/with-column-widths.xlsx', 'Xlsx');

        $this->assertEquals(55, $spreadsheet->getActiveSheet()->getColumnDimension('A')->getWidth());
    }
}
