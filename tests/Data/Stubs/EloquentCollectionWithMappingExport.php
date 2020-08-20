<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Database\Eloquent\Collection;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromCollection;
use codicastudio\Excel\Concerns\WithMapping;
use codicastudio\Excel\Tests\Data\Stubs\Database\User;

class EloquentCollectionWithMappingExport implements FromCollection, WithMapping
{
    use Exportable;

    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            new User([
                'firstname' => 'Patrick',
                'lastname'  => 'Brouwers',
            ]),
        ]);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function map($user): array
    {
        return [
            $user->firstname,
            $user->lastname,
        ];
    }
}
