<?php

namespace BahasTech\SizeBasedRotationLogging;

use Illuminate\Log\Logger as LaravelLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Process\Process;

class Handler
{
    public function __invoke(Logger|LaravelLogger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof StreamHandler) {
                $handler->pushProcessor(function ($record) use ($handler) {
                    $file = $handler->getUrl();
                    $thresholdInMb = config('bt-rotation-logging.size_threshold_in_mb', 500) * 1024 * 1024;

                    if (file_exists($file) && filesize($file) >= $thresholdInMb) {
                        $this->rotateAndCompress($file);
                    }

                    $this->cleanupOldLogs(dirname($file), config('bt-rotation-logging.keep_logs_in_days', 3));

                    return $record;
                });
            }
        }
    }

    protected function rotateAndCompress($file)
    {
        $rotatedFile = $file . '.' . date('Y-m-d_H-i-s') . '.log';
        rename($file, $rotatedFile);

        // Compress the rotated log file
        $process = new Process(['gzip', '-f', $rotatedFile]);
        $process->run();
    }

    protected function cleanupOldLogs($logDir, $days)
    {
        $files = glob("$logDir/*.log.gz");
        $cutOffTime = strtotime("-{$days} days");

        foreach ($files as $file) {
            if (filemtime($file) < $cutOffTime) {
                unlink($file);
            }
        }
    }
}
