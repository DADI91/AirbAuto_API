<?php

namespace ContainerJHKPCwi;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUploadMediaControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\UploadMediaController' shared autowired service.
     *
     * @return \App\Controller\UploadMediaController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/UploadMediaController.php';

        $container->services['App\\Controller\\UploadMediaController'] = $instance = new \App\Controller\UploadMediaController(($container->privates['App\\Services\\FirebaseService'] ?? $container->load('getFirebaseServiceService')));

        $instance->setContainer(($container->privates['.service_locator.jIxfAsi'] ?? $container->load('get_ServiceLocator_JIxfAsiService'))->withContext('App\\Controller\\UploadMediaController', $container));

        return $instance;
    }
}
