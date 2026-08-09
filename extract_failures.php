<?php
$logPath = 'C:\Users\mouba\.gemini\antigravity-ide\brain\554e2209-1df6-4df7-8575-aa1e1527c3d3\.system_generated\tasks\task-283.log';
$log = file_get_contents($logPath);
$log = preg_replace('/\x1b\[[0-9;]*m/', '', $log);
preg_match_all('/(FAIL|FAILED)\s+(Tests.*?)(?:\n|$)/i', $log, $matches);
foreach($matches[2] as $match) {
    echo trim($match) . PHP_EOL;
}
echo "Done\n";
