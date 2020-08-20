<?php

namespace codicastudio\Excel;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use codicastudio\Excel\Cache\CacheManager;
use codicastudio\Excel\Console\ExportMakeCommand;
use codicastudio\Excel\Console\ImportMakeCommand;
use codicastudio\Excel\Files\Filesystem;
use codicastudio\Excel\Files\TemporaryFileFactory;
use codicastudio\Excel\Mixins\DownloadCollection;
use codicastudio\Excel\Mixins\StoreCollection;
use codicastudio\Excel\Transactions\TransactionHandler;
use codicastudio\Excel\Transactions\TransactionManager;

class ExcelServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Console/stubs/export.model.stub'       => base_path('stubs/export.model.stub'),
                __DIR__ . '/Console/stubs/export.plain.stub'       => base_path('stubs/export.plain.stub'),
                __DIR__ . '/Console/stubs/export.query.stub'       => base_path('stubs/export.query.stub'),
                __DIR__ . '/Console/stubs/export.query-model.stub' => base_path('stubs/export.query-model.stub'),
                __DIR__ . '/Console/stubs/import.collection.stub'  => base_path('stubs/import.collection.stub'),
                __DIR__ . '/Console/stubs/import.model.stub'       => base_path('stubs/import.model.stub'),
            ], 'stubs');

            if ($this->app instanceof LumenApplication) {
                $this->app->configure('excel');
            } else {
                $this->publishes([
                    $this->getConfigFile() => config_path('excel.php'),
                ], 'config');
            }
        }

        if ($this->app instanceof \Illuminate\Foundation\Application) {
            // Laravel
            $this->app->booted(function () {
                $this->app->make(SettingsProvider::class)->provide();
            });
        } else {
            // Lumen
            $this->app->make(SettingsProvider::class)->provide();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->getConfigFile(),
            'excel'
        );

        $this->app->bind(CacheManager::class, function () {
            return new CacheManager($this->app);
        });

        $this->app->bind(TransactionManager::class, function () {
            return new TransactionManager($this->app);
        });

        $this->app->bind(TransactionHandler::class, function () {
            return $this->app->make(TransactionManager::class)->driver();
        });

        $this->app->bind(TemporaryFileFactory::class, function () {
            return new TemporaryFileFactory(
                config('excel.temporary_files.local_path', config('excel.exports.temp_path', storage_path('framework/laravel-excel'))),
                config('excel.temporary_files.remote_disk')

            );
        });

        $this->app->bind(Filesystem::class, function () {
            return new Filesystem($this->app->make('filesystem'));
        });

        $this->app->bind('excel', function () {
            return new Excel(
                $this->app->make(Writer::class),
                $this->app->make(QueuedWriter::class),
                $this->app->make(Reader::class),
                $this->app->make(Filesystem::class)
            );
        });

        $this->app->alias('excel', Excel::class);
        $this->app->alias('excel', Exporter::class);
        $this->app->alias('excel', Importer::class);

        Collection::mixin(new DownloadCollection);
        Collection::mixin(new StoreCollection);

        $this->commands([
            ExportMakeCommand::class,
            ImportMakeCommand::class,
        ]);
    }

    /**
     * @return string
     */
    protected function getConfigFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'excel.php';
    }
}
