<?php
declare(strict_types=1);

namespace TrayDigita\OAuth2\Interfaces\Scratches;

interface TimestampInterface
{
    /**
     * Get current set timestamp, this is for checking if the data is expired or not,
     * and also for calculating the remaining time until it expires.
     *
     * @return positive-int the timestamp when the data that issued,
     * this is for checking if the data is expired or not,
     * and also for calculating the remaining time until it expires.
     */
    public function getTimestamp() : int;
}
