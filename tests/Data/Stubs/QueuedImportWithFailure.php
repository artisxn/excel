<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use codicastudio\Excel\Concerns\Importable;
use codicastudio\Excel\Concerns\ToModel;
use codicastudio\Excel\Concerns\WithChunkReading;
use codicastudio\Excel\Tests\Data\Stubs\Database\Group;

class QueuedImportWithFailure implements ShouldQueue, ToModel, WithChunkReading
{
    use Importable;

    /**
     * @param array $row
     *
     * @return Model|null
     */
    public function model(array $row)
    {
        throw new \Exception('Something went wrong in the chunk');

        return new Group([
            'name' => $row[0],
        ]);
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
