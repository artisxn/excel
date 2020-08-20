<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use Illuminate\Database\Query\Builder;
use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\FromQuery;
use codicastudio\Excel\Concerns\WithMapping;
use codicastudio\Excel\Tests\Data\Stubs\Database\User;

class FromUsersQueryExportWithEagerLoad implements FromQuery, WithMapping
{
    use Exportable;

    /**
     * @return Builder
     */
    public function query()
    {
        return User::query()->with([
            'groups' => function ($query) {
                $query->where('name', 'Group 1');
            },
        ])->withCount('groups');
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            $row->groups_count,
            $row->groups->implode('name', ', '),
        ];
    }
}
