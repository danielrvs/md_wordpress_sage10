<?php

declare(strict_types=1);

// CLI Commands
if (defined('WP_CLI')) {
    \WP_CLI::add_command('page:seed', \App\Domain\Pages\Cli\PageSeeder::class);
}
