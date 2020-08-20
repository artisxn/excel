<?php

namespace codicastudio\Excel\Tests\Concerns;

use Illuminate\Database\Eloquent\Model;
use codicastudio\Excel\Concerns\Importable;
use codicastudio\Excel\Concerns\ToArray;
use codicastudio\Excel\Concerns\ToModel;
use codicastudio\Excel\Concerns\WithStartRow;
use codicastudio\Excel\Tests\Data\Stubs\Database\User;
use codicastudio\Excel\Tests\TestCase;
use PHPUnit\Framework\Assert;

class WithStartRowTest extends TestCase
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
    public function can_import_each_row_to_model_with_different_start_row()
    {
        $import = new class implements ToModel, WithStartRow {
            use Importable;

            /**
             * @param array $row
             *
             * @return Model
             */
            public function model(array $row): Model
            {
                return new User([
                    'name'     => $row[0],
                    'email'    => $row[1],
                    'password' => 'secret',
                ]);
            }

            /**
             * @return int
             */
            public function startRow(): int
            {
                return 5;
            }
        };

        $import->import('import-users-with-different-heading-row.xlsx');

        $this->assertDatabaseHas('users', [
            'name'  => 'Patrick Brouwers',
            'email' => 'patrick@codicastudio.nl',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'Taylor Otwell',
            'email' => 'taylor@laravel.com',
        ]);
    }

    /**
     * @test
     */
    public function can_import_to_array_with_start_row()
    {
        $import = new class implements ToArray, WithStartRow {
            use Importable;

            /**
             * @param array $array
             */
            public function array(array $array)
            {
                Assert::assertEquals([
                    [
                        'Patrick Brouwers',
                        'patrick@codicastudio.nl',
                    ],
                    [
                        'Taylor Otwell',
                        'taylor@laravel.com',
                    ],
                ], $array);
            }

            /**
             * @return int
             */
            public function startRow(): int
            {
                return 5;
            }
        };

        $import->import('import-users-with-different-heading-row.xlsx');
    }
}
