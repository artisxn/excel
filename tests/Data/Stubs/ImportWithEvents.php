<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use codicastudio\Excel\Concerns\Importable;
use codicastudio\Excel\Concerns\WithEvents;
use codicastudio\Excel\Events\AfterImport;
use codicastudio\Excel\Events\AfterSheet;
use codicastudio\Excel\Events\BeforeImport;
use codicastudio\Excel\Events\BeforeSheet;

class ImportWithEvents implements WithEvents
{
    use Importable;

    /**
     * @var callable
     */
    public $beforeImport;

    /**
     * @var callable
     */
    public $beforeSheet;

    /**
     * @var callable
     */
    public $afterSheet;

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => $this->beforeImport ?? function () {
            },
            AfterImport::class => $this->afterImport ?? function () {
            },
            BeforeSheet::class => $this->beforeSheet ?? function () {
            },
            AfterSheet::class => $this->afterSheet ?? function () {
            },
        ];
    }
}
