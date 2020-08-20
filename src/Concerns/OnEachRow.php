<?php

namespace codicastudio\Excel\Concerns;

use codicastudio\Excel\Row;

interface OnEachRow
{
    /**
     * @param Row $row
     */
    public function onRow(Row $row);
}
