<?php

namespace App\Service;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

class GeoIpService
{
    private Reader $reader;

    public function __construct(string $databasePath)
    {
        $this->reader = new Reader($databasePath);
    }

    public function getCountryCode(string $ip): ?string
    {
        try {
            $record = $this->reader->country($ip);
            return $record->country->isoCode; // e.g. "US", "DE", "BR"
        } catch (AddressNotFoundException) {
            return null; // private/local IPs, or genuinely unknown
        } catch (\Exception) {
            return null;
        }
    }
}