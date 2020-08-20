<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromQuery;
use codicastudio\Excel\Concerns\WithEvents;
use codicastudio\Excel\Concerns\WithMapping;
use codicastudio\Excel\Events\BeforeSheet;
use codicastudio\Excel\Tests\Data\Stubs\Database\User;

class FromUsersQueryExportWithMapping implements FromQuery, WithMapping, WithEvents
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return User::query();
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class   => function (BeforeSheet $event) {
                $event->sheet->chunkSize(10);
            },
        ];
    }

    /**
     * @param User $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            'name' => $row->name,
        ];
    }
}
