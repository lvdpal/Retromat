<?php

namespace AppBundle\Importer\Activity;

use AppBundle\Importer\Activity\Exception\ActivitySyntaxException;

class OriginalRetromatActivityImporter
{
    private $activities;

    public function __construct($fileName)
    {
        $this->activities = file_get_contents($fileName);
    }

    public function extractActivityBlock($id)
    {
        $startMarker = "{\n";
        $endMarker = "\n};";

        $blockStart = strpos($this->activities, 'all_activities['.$id.']');
        $start = strpos($this->activities, $startMarker, $blockStart) + strlen($startMarker);
        $end = strpos($this->activities, $endMarker, $start);

        return substr($this->activities, $start, $end - $start);
    }

    // this may later generalize into extractInteger
    public function extractActivityPhase($activityBlock)
    {
        $key = 'phase:';
        $lineNumber = 0;

        $line = explode("\n", $activityBlock)[$lineNumber];

        if (0 !== strpos($line, $key)) {
            throw new ActivitySyntaxException('Key '.$key.' is expected at the beginning of line '.$lineNumber.'.');
        }

        $start = strlen($key);
        $end = strpos($line, ',', $start);

        return intval(trim(substr($line, $start, $end - $start)));
    }

    public function extractActivityName($activityBlock)
    {
        return $this->extractStringValue($activityBlock, $key = 'name:');
    }

    public function extractActivitySummary($activityBlock)
    {
        return $this->extractStringValue($activityBlock, $key = 'summary:');
    }

    public function extractActivityDescription($activityBlock)
    {
        return $this->extractStringValue($activityBlock, $key = 'desc:');
    }

    /**
     * @param $activityBlock
     * @param $key
     * @return string
     */
    public function extractStringValue($activityBlock, $key)
    {
        $keyPosition = strpos($activityBlock, $key);
        $start = strpos($activityBlock, '"', $keyPosition + strlen($key)) + strlen('"');
        $end = strpos($activityBlock, '",', $start);

        return substr($activityBlock, $start, $end - $start);
    }

}