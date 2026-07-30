<?php
$directories = ['app/Http/Controllers', 'app/Policies', 'routes', 'resources/views', 'app/Services'];
$replacements = [
    '\'view-tasks\'' => '\'project_task.view\'',
    '"view-tasks"' => '"project_task.view"',
    '\'create-tasks\'' => '\'project_task.create\'',
    '"create-tasks"' => '"project_task.create"',
    '\'edit-tasks\'' => '\'project_task.update\'',
    '"edit-tasks"' => '"project_task.update"',
    '\'delete-tasks\'' => '\'project_task.delete\'',
    '"delete-tasks"' => '"project_task.delete"',
    '\'restore-tasks\'' => '\'project_task.restore\'',
    '"restore-tasks"' => '"project_task.restore"',
    '\'assignTasks\'' => '\'project_task.update\'',
    '"assignTasks"' => '"project_task.update"',
    '\'assign-tasks\'' => '\'project_task.update\'',
    '"assign-tasks"' => '"project_task.update"',
    '\'view-milestones\'' => '\'milestone.view\'',
    '"view-milestones"' => '"milestone.view"',
    '\'create-milestones\'' => '\'milestone.create\'',
    '"create-milestones"' => '"milestone.create"',
    '\'edit-milestones\'' => '\'milestone.update\'',
    '"edit-milestones"' => '"milestone.update"',
    '\'delete-milestones\'' => '\'milestone.delete\'',
    '"delete-milestones"' => '"milestone.delete"',
    '\'restore-milestones\'' => '\'milestone.restore\'',
    '"restore-milestones"' => '"milestone.restore"',
    '\'client.' => '\'customer.',
    '"client.' => '"customer.',
    'permission:view-tasks' => 'permission:project_task.view',
    'permission:create-tasks' => 'permission:project_task.create',
    'permission:edit-tasks' => 'permission:project_task.update',
    'permission:delete-tasks' => 'permission:project_task.delete',
    'permission:restore-tasks' => 'permission:project_task.restore',
    'permission:assign-tasks' => 'permission:project_task.update',
    'permission:assignTasks' => 'permission:project_task.update',
    'permission:view-milestones' => 'permission:milestone.view',
    'permission:create-milestones' => 'permission:milestone.create',
    'permission:edit-milestones' => 'permission:milestone.update',
    'permission:delete-milestones' => 'permission:milestone.delete',
    'permission:restore-milestones' => 'permission:milestone.restore',
    'permission:client.' => 'permission:customer.',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        if (!preg_match('/\.(php)$/i', $file->getFilename())) continue;
        
        $path = $file->getPathname();
        $content = file_get_contents($path);
        $modified = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($modified !== $content) {
            file_put_contents($path, $modified);
            echo "Updated: $path\n";
        }
    }
}
