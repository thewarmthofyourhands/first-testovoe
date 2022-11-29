<?php

declare(strict_types=1);

namespace App\Functions;

function memoryUsage(): string
{
    $size = memory_get_usage(true);
    $unit=['b','kb','mb','gb','tb','pb'];

    return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),2).' '.$unit[$i];
}

function memoryPeakUsage(): string
{
    $size = memory_get_peak_usage(true);
    $unit=['b','kb','mb','gb','tb','pb'];

    return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),2).' '.$unit[$i];
}
