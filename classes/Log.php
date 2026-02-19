<?php

use Exceptions\QuintypeException;

class Log
{
    public function __construct(
        private Quintype $quintype
    ) {
        // 
    }

    /**
     * @param string $data
     * @return void
     */
    public function write($data, $publisherCode = null)
    {
        if ($data instanceof QuintypeException) {
            $publisher = $data->getPublisher();

            if ($publisher !== null) {
                $publisherCode = $publisher['code'];
                $data = $data->getCode() . ' ' . $data->getMessage();
            }
        }

        if ($publisherCode) {
            $logDirectory = __DIR__ . '/../logs/';
            $logFile = strtolower($publisherCode) . '.log';

            $timestamp = date('[Y-m-d H:i:s]');
            $logLine = $timestamp . ' - ' . $data . PHP_EOL;

            $existingContent = file_exists($logDirectory . $logFile) ? file_get_contents($logDirectory . $logFile) : '';
            $lines = array_filter(explode(PHP_EOL, $existingContent));

            $thirtyDaysAgo = strtotime('-30 days');
            $filteredLines = [];

            foreach ($lines as $line) {
                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    if (strtotime($matches[1]) >= $thirtyDaysAgo) {
                        $filteredLines[] = $line;
                    }
                }
            }

            $newContent = $logLine . implode(PHP_EOL, $filteredLines);
            file_put_contents($logDirectory . $logFile, $newContent);
        }
    }
}
