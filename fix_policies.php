<?php
$dir = 'app/Policies';
$iterator = new DirectoryIterator($dir);
foreach ($iterator as $file) {
    if ($file->isDot() || $file->isDir() || !preg_match('/([A-Za-z0-9]+)Policy\.php$/', $file->getFilename(), $matches)) continue;
    $policyName = $matches[1];
    
    // Convert CamelCase to snake_case for module name
    $module = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $policyName));
    
    // Exception mapping
    if ($module === 'client') $module = 'customer';
    if ($module === 'task') $module = 'project_task';
    
    $path = $file->getPathname();
    $content = file_get_contents($path);
    
    // Match standalone verbs like hasPermissionTo('activate') or can('create')
    // We want to avoid matching if it already has a dot (e.g. 'company.create')
    // Also avoid matching single words that are not permissions? Actually, we only replace inside hasPermissionTo or can
    $content = preg_replace_callback('/(hasPermissionTo|can|authorize)\(\s*[\'"]([a-zA-Z0-9_-]+)[\'"]/', function($m) use ($module) {
        $verb = $m[2];
        // If the verb already has a dot, or is already prefixed, skip it (though the regex [a-zA-Z0-9_-]+ doesn't match dots)
        // Wait, if verb is 'view-tasks', it would have been replaced.
        // Let's exclude common words that are already module names if they are somehow used? No, the regex above strictly matches no dots.
        
        // Some verbs might be camelCase like viewAny, let's map them to standard CRUD
        $mappedVerb = $verb;
        if ($verb === 'viewAny') $mappedVerb = 'view';
        if ($verb === 'updateAny') $mappedVerb = 'update';
        if ($verb === 'resetPassword') $mappedVerb = 'reset_password';
        
        return $m[1] . "('" . $module . "." . $mappedVerb . "'";
    }, $content);
    
    // Also fix cases with arrays: hasAnyPermission(['view', 'create']) -> hasAnyPermission(['module.view', 'module.create'])
    $content = preg_replace_callback('/hasAnyPermission\(\s*\[(.*?)\]\s*\)/s', function($m) use ($module) {
        $arrayContent = $m[1];
        $arrayContent = preg_replace_callback('/[\'"]([a-zA-Z0-9_-]+)[\'"]/', function($sm) use ($module) {
            $verb = $sm[1];
            $mappedVerb = $verb;
            if ($verb === 'viewAny') $mappedVerb = 'view';
            if ($verb === 'updateAny') $mappedVerb = 'update';
            return "'" . $module . "." . $mappedVerb . "'";
        }, $arrayContent);
        return "hasAnyPermission([" . $arrayContent . "])";
    }, $content);
    
    // Check if changed
    if ($content !== file_get_contents($path)) {
        file_put_contents($path, $content);
        echo "Updated Policy: $path\n";
    }
}
