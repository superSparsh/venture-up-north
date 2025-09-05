<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google_Client;
use Google_Service_Indexing;
use Google_Service_Indexing_UrlNotification;

class SubmitIndexingUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:indexing {url* : One or more full URLs to submit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit URLs to Google Indexing API for fast indexing';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        // 1) Set up Google client
        $client = new Google_Client();
        $client->setAuthConfig(storage_path('app/keys/indexing-api-key.json'));
        $client->addScope(Google_Service_Indexing::INDEXING);

        $service = new Google_Service_Indexing($client);

        // 2) Loop through each URL argument
        foreach ($this->argument('url') as $url) {
            try {
                $notification = new Google_Service_Indexing_UrlNotification([
                    'url'  => $url,
                    'type' => 'URL_UPDATED', // or 'URL_DELETED'
                ]);
                $response = $service->urlNotifications->publish($notification);

                $this->info("✅ Submitted: $url (type={$response->getType()})");
            } catch (\Throwable $e) {
                $this->error("❌ Failed: $url — " . $e->getMessage());
            }
        }

        return 0;
    }
}
