<?php

namespace codicastudio\Excel\Tests\Data\Stubs;

use codicastudio\Excel\Concerns\Exportable;
use codicastudio\Excel\Concerns\WithTitle;

class WithTitleExport implements WithTitle
{
    use Exportable;

    /**
     * @return string
     */
    public function title(): string
    {
        return 'given-title';
    }
}
