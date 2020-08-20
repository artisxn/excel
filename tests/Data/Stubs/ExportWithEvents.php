<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\WithEvents;
use codicastudio\Excel\Events\AfterSheet;
use codicastudio\Excel\Events\BeforeExport;
use codicastudio\Excel\Events\BeforeSheet;
use codicastudio\Excel\Events\BeforeWriting;

class ExportWithEvents implements WithEvents
{
    use Exportable;

    /**
     * @var callable
     */
    public $beforeExport;

    /**
     * @var callable
     */
    public $beforeWriting;

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
            BeforeExport::class  => $this->beforeExport ?? function () {
            },
            BeforeWriting::class => $this->beforeWriting ?? function () {
            },
            BeforeSheet::class   => $this->beforeSheet ?? function () {
            },
            AfterSheet::class    => $this->afterSheet ?? function () {
            },
        ];
    }
}
