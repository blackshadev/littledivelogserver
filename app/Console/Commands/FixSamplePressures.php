<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\DiveSamples\Services\FixDiveSamplePressures;
use App\Models\Dive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FixSamplePressures extends Command
{
    protected $signature = 'fix:samples:pressures';

    protected $description = 'Fixes sample pressures to ensure every pressure sample has only sample for the same tank';

    public function handle(FixDiveSamplePressures $fixDiveSamplePressures): int
    {
        DB::connection()->disableQueryLog();

        $dives = Dive::query()->select(['id', 'samples'])->whereNotNull('samples')->cursor();

        /** @var Dive $dive */
        foreach ($dives as $dive) {
            $result = $fixDiveSamplePressures->fix($dive->diveSamples());

            if ($result->touched) {
                $this->output->info('Updated dive ' . $dive->id);
            } else {
                $this->output->info('Skipped dive ' . $dive->id);
            }
        }
        return 0;
    }
}
