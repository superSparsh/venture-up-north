<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use App\Models\TourClick;

class SyncTourClicks extends Command
{
    protected $signature = 'analytics:sync-tour-clicks';
    protected $description = 'Sync tour click events from GA4 into local DB';

    public function handle()
    {
        $propertyId = config('services.ga4.property_id');

        $client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/ga/credentials.json'),
        ]);

        $response = $client->runReport([
            'property' => "properties/{$propertyId}",
            'dateRanges' => [
                new DateRange([
                    'start_date' => '7daysAgo',
                    'end_date' => 'today',
                ]),
            ],
            'dimensions' => [
                new Dimension(['name' => 'eventName']),
                new Dimension(['name' => 'eventLabel']),
                new Dimension(['name' => 'date']),
            ],
            'metrics' => [
                new Metric(['name' => 'eventCount']),
            ],
            'dimensionFilter' => [
                'filter' => [
                    'fieldName' => 'eventName',
                    'stringFilter' => ['value' => 'tour_click'], // adjust to your GA event
                ],
            ],
        ]);

        foreach ($response->getRows() as $row) {
            $eventLabel = $row->getDimensionValues()[1]->getValue();
            $date = \Carbon\Carbon::createFromFormat('Ymd', $row->getDimensionValues()[2]->getValue())->toDateString();
            $clicks = (int) $row->getMetricValues()[0]->getValue();

            TourClick::updateOrCreate([
                'event_label' => $eventLabel,
                'date' => $date,
            ], [
                'clicks' => $clicks,
            ]);
        }

        $this->info('Tour clicks synced from GA4.');
    }
}
