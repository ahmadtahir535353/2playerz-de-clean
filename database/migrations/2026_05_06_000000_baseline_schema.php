<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Baseline schema for 2playerz.de — squash of all migrations up to 2026-05-06.
 *
 * Applied state (already on prod): this migration was inserted into the
 * `migrations` table without executing up(). On a brand-new database
 * (`migrate:fresh` / `migrate:install` followed by `migrate`), up() will
 * recreate the full schema from baseline_schema.sql in this directory.
 */
return new class extends Migration {
    public function up(): void
    {
        $sqlFile = __DIR__ . '/2026_05_06_000000_baseline_schema.sql';

        if (! is_readable($sqlFile)) {
            throw new \RuntimeException("Baseline schema file missing: {$sqlFile}");
        }

        DB::unprepared(file_get_contents($sqlFile));
    }

    public function down(): void
    {
        $tables = collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0])
            ->reject(fn ($t) => $t === 'migrations')
            ->values();

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $t) {
            Schema::dropIfExists($t);
        }
        Schema::enableForeignKeyConstraints();
    }
};
