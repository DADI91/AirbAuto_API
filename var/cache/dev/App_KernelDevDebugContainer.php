<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerR6JK1wV\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerR6JK1wV/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerR6JK1wV.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerR6JK1wV\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerR6JK1wV\App_KernelDevDebugContainer([
    'container.build_hash' => 'R6JK1wV',
    'container.build_id' => 'b0dea9e3',
    'container.build_time' => 1676647778,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerR6JK1wV');
