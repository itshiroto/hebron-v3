<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CopyDatabaseFrom extends Command
{
    protected $signature = 'db:copy-from
        {url? : Source database URL, e.g. mariadb://user:pass@host:3306/hebron_v3 (prompted if omitted)}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Replace all data in the default database with the data from another database (e.g. the old MariaDB)';

    // The target keeps its own migrations; the rest is throwaway runtime state.
    private const SKIP = ['migrations', 'cache', 'cache_locks', 'sessions', 'jobs', 'job_batches', 'failed_jobs'];

    public function handle(): int
    {
        $url = $this->argument('url') ?? $this->secret('Source database URL');
        config(['database.connections.copy_source' => ['url' => $url] + config('database.connections.mariadb')]);

        $source = DB::connection('copy_source');
        $target = DB::connection();
        $tables = array_diff(array_column($target->getSchemaBuilder()->getTables(), 'name'), self::SKIP);

        if (! $this->option('force') && ! $this->confirm('This deletes every row in '.count($tables).' tables of ['.$target->getName().'] and copies them from the source. Continue?')) {
            return self::FAILURE;
        }

        $target->getSchemaBuilder()->withoutForeignKeyConstraints(fn () => $target->transaction(function () use ($source, $target, $tables) {
            foreach ($tables as $table) {
                if (! $source->getSchemaBuilder()->hasTable($table)) {
                    $this->warn("Skipped {$table}: not in source");

                    continue;
                }

                // Columns added by newer migrations keep their defaults.
                $columns = array_values(array_intersect(
                    $target->getSchemaBuilder()->getColumnListing($table),
                    $source->getSchemaBuilder()->getColumnListing($table),
                ));

                $target->table($table)->delete();
                $source->table($table)->select($columns)->cursor()->chunk(100)->each(
                    fn ($rows) => $target->table($table)->insert($rows->map(fn ($row) => (array) $row)->all())
                );

                $expected = $source->table($table)->count();
                $copied = $target->table($table)->count();
                if ($copied !== $expected) {
                    throw new RuntimeException("{$table}: copied {$copied} of {$expected} rows, rolled back");
                }

                $this->line("{$table}: {$copied} rows");
            }
        }));

        $this->info('Done.');

        return self::SUCCESS;
    }
}
