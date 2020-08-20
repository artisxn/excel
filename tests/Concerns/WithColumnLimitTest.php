<?php

namespace codicastudio\Excel\Tests\Concerns;

use codicastudio\Excel\Concerns\Importable;
use codicastudio\Excel\Concerns\ToArray;
use codicastudio\Excel\Concerns\WithColumnLimit;
use codicastudio\Excel\Tests\TestCase;
use PHPUnit\Framework\Assert;

class WithColumnLimitTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    /**
     * @test
     */
    public function can_import_to_array_with_column_limit()
    {
        $import = new class implements ToArray, WithColumnLimit {
            use Importable;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                Assert::assertEquals([
                    [
                        'Patrick Brouwers',
                    ],
                    [
                        'Taylor Otwell',
                    ],
                ], $array);
            }

            public function endColumn(): string
            {
                return 'A';
            }
        };

        $import->import('import-users.xlsx');
    }
}
