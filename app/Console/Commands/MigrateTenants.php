<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Services\TenantMigrationService;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate {tenant_id? : The ID of the tenant to migrate} {--fresh : Wipe the database before running migrations} {--seed : Seed the database after migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for tenant databases';

    protected $migrationService;

    public function __construct(TenantMigrationService $migrationService)
    {
        parent::__construct();
        $this->migrationService = $migrationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        if ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
        } else {
            $tenants = Tenant::all();
        }

        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant: {$tenant->name} (ID: {$tenant->id})");

            try {
                // Ensure database exists
                $this->migrationService->createDatabase($tenant);

                // Run migrations
                $output = $this->migrationService->migrate($tenant, $this->option('fresh'));
                $this->line($output);

                if ($this->option('seed')) {
                    $this->info("Seeding tenant: {$tenant->name}");
                    $seedOutput = $this->migrationService->seed($tenant);
                    $this->line($seedOutput);
                }

                $this->info("Successfully migrated tenant: {$tenant->name}");
            } catch (\Exception $e) {
                $this->error("Failed to migrate tenant {$tenant->name}: " . $e->getMessage());
            }
        }
    }
}
