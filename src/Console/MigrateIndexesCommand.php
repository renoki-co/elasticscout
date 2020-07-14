<?php

namespace Rennokki\ElasticScout\Console;

use Exception;
use Illuminate\Console\Command;

class MigrateIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticscout:migrate {--drop} {--import} {models*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update the listed indexes then update the mapping for searchable models.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Migrating searchable models...');

        if ($this->option('drop') && app()->environment(['production'])) {
            if (! $this->confirm('This will drop ALL your indexes and re-import data. Are you sure?')) {
                return $this->line('Migration stopped.');
            }
        }

        foreach ($this->argument('models') as $model) {
            $this->applyStepsForModel($model);
        }
    }

    /**
     * Apply specific steps for given model.
     *
     * @param  string  $model
     * @return void
     */
    protected function applyStepsForModel($model): void
    {
        if ($this->option('drop')) {
            $this->line("Dropping the index for {$model}....");

            $index = (new $model)->getIndex();

            try {
                $index->delete();
            } catch (Exception $e) {
                //
            }
        }

        $this->line("Syncing the index for {$model}....");

        try {
            $index->sync();
        } catch (Exception $e) {
            //
        }

        if ($this->option('import')) {
            $this->line("Importing the data for {$model}....");

            $this->importDataForModel($model);
        }
    }

    /**
     * Import the data for a model.
     *
     * @param  string  $model
     * @return void
     */
    protected function importDataForModel($model): void
    {
        $this->call('scout:import', ['model' => $model]);
    }
}
