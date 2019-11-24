<?php

namespace Vipertecpro\Laravel\VisitorTracker\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Vipertecpro\Laravel\VisitorTracker\Geoip;
use Vipertecpro\Laravel\VisitorTracker\Models\Visit;

class GetGeoipData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $visit;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @param Visit $visit
     */
    public function __construct(Visit $visit)
    {
        $this->visit = $visit;
    }

    /**
     * Execute the job. Fetches geolocation data from the preferred driver for
     * a specific visit. Records the data to the database if the received
     * values are not excluded from being tracked in the config file.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if (config('visitortracker.geoip_on')) {
            $geoip = new Geoip(config('visitortracker.geoip_driver'));
            if ($geoip->driver && $geoip = $geoip->driver->getDataFor($this->visit)) {
                $data = [
                    'lat' => $geoip->latitude() ?: null,
                    'long' => $geoip->longitude() ?: null,
                    'country' => $geoip->country() ?: '',
                    'country_code' => $geoip->countryCode() ?: '',
                    'city' => $geoip->city() ?: '',
                ];

                if ($this->shouldRecordVisit($data)) {
                    $this->visit->update($data);
                } else {
                    $this->visit->delete();
                }
            }
        }
    }

    /**
     * Determine if the request/visit should be recorded
     *
     * @return boolean
     */
    protected static function shouldRecordVisit($data)
    {
        foreach (config('visitortracker.dont_record_geoip') as $fields) {
            $conditionsMet = 0;
            foreach ($fields as $field => $value) {
                if ($data[$field] === $value) {
                    $conditionsMet++;
                }
            }

            if ($conditionsMet === count($fields)) {
                return false;
            }
        }

        return true;
    }
    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return string
     */
    public function failed(Exception $exception)
    {
        return $exception->getMessage();
    }
}
