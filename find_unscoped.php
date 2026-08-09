<?php
$data = json_decode(file_get_contents('audit_models.json'), true);
$unscoped = array_filter($data, function($m) { return $m['Has company_id'] === 'Yes' && $m['CompanyScoped'] === 'No'; });
foreach($unscoped as $m) echo $m['Model'] . PHP_EOL;
