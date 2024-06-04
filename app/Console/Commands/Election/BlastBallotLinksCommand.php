<?php

namespace App\Console\Commands\Election;

use App\Events\BallotLinkBlastCompleted;
use App\Events\BallotLinkBlastInitiated;
use App\Models\BallotLinkBlast;
use App\Models\Election;
use App\Models\Elector;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class BlastBallotLinksCommand extends Command
{
    protected $signature = 'election:blast-ballot-links';

    protected $description = 'Blast ballot links';

    public function handle(): void
    {
        BallotLinkBlast::query()->pending()
            ->activeElection()
            ->chunkById(count: 50, callback: function (Collection $blasts) {
                foreach ($blasts as $blast) {

                    /** @var BallotLinkBlast $blast */
                    $blast->touch(attribute: 'initiated_at');

                    BallotLinkBlastInitiated::dispatch($blast);

                    /** @var Election $election */
                    $election = $blast->election;
                    $election->electors()
                        ->chunkById(
                            count: 50,
                            callback: fn (Collection $collection) => $collection
                                ->each(
                                    callback: fn (Elector $elector) => $elector
                                        ->sendBallotLink(election: $election)
                                )
                        );

                    $blast->touch(attribute: 'completed_at');

                    BallotLinkBlastCompleted::dispatch($blast);
                }
            });
    }
}
