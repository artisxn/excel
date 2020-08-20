<?php

namespace codicastudio\Excel\Concerns;

use codicastudio\Excel\Validators\Failure;

interface SkipsOnFailure
{
    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures);
}
