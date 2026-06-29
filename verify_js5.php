<?php
$script = file_get_contents('test_script.js');

// Show the production chart section
$start = strpos($script, 'function initProductionChart');
if ($start !== false) {
    $end = strpos($script, 'function initCumulativeChart', $start);
    $section = substr($script, $start, $end - $start);
    echo "=== Production Chart JS ===\n";
    echo $section . "\n\n";
}

// Show the revenue cost chart section
$start = strpos($script, 'function initRevenueCostChart');
if ($start !== false) {
    $end = strpos($script, 'function initPerformanceChart', $start);
    $section = substr($script, $start, $end - $start);
    echo "=== Revenue Cost Chart JS ===\n";
    echo $section . "\n\n";
}

// Show cumulative chart section
$start = strpos($script, 'function initCumulativeChart');
if ($start !== false) {
    $end = strpos($script, 'function initPdfExport', $start);
    $section = substr($script, $start, $end - $start);
    echo "=== Cumulative Chart JS ===\n";
    echo $section . "\n\n";
}
