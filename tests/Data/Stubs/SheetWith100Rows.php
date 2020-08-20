<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Support\Collection;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromCollection;
use codicastudio\Excel\Concerns\RegistersEventListeners;
use codicastudio\Excel\Concerns\ShouldAutoSize;
use codicastudio\Excel\Concerns\WithEvents;
use codicastudio\Excel\Concerns\WithTitle;
use codicastudio\Excel\Events\BeforeWriting;
use codicastudio\Excel\Tests\TestCase;
use codicastudio\Excel\Writer;

class SheetWith100Rows implements FromCollection, WithTitle, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        $collection = new Collection;
        for ($i = 0; $i < 100; $i++) {
            $row = new Collection();
            for ($j = 0; $j < 5; $j++) {
                $row[] = $this->title() . '-' . $i . '-' . $j;
            }

            $collection->push($row);
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @param BeforeWriting $event
     */
    public static function beforeWriting(BeforeWriting $event)
    {
        TestCase::assertInstanceOf(Writer::class, $event->writer);
    }
}
