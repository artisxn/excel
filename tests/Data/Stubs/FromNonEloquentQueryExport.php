<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromQuery;
use codicastudio\Excel\Concerns\WithCustomChunkSize;

class FromNonEloquentQueryExport implements FromQuery, WithCustomChunkSize
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return DB::table('users')->select('name')->orderBy('id');
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 10;
    }
}
