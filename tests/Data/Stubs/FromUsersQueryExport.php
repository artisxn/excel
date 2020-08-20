<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromQuery;
use codicastudio\Excel\Concerns\WithCustomChunkSize;
use codicastudio\Excel\Tests\Data\Stubs\Database\User;

class FromUsersQueryExport implements FromQuery, WithCustomChunkSize
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
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
