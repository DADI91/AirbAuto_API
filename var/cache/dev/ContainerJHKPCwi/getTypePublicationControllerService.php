<?php

namespace ContainerJHKPCwi;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getTypePublicationControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\TypePublicationController' shared autowired service.
     *
     * @return \App\Controller\TypePublicationController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/TypePublicationController.php';

        $container->services['App\\Controller\\TypePublicationController'] = $instance = new \App\Controller\TypePublicationController(($container->privates['App\\Services\\FirebaseService'] ?? $container->load('getFirebaseServiceService')));

        $instance->setContainer(($container->privates['.service_locator.jIxfAsi'] ?? $container->load('get_ServiceLocator_JIxfAsiService'))->withContext('App\\Controller\\TypePublicationController', $container));

        return $instance;
    }
}
