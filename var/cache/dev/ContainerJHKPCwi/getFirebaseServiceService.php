<?php

namespace ContainerJHKPCwi;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getFirebaseServiceService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Services\FirebaseService' shared autowired service.
     *
     * @return \App\Services\FirebaseService
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Services/FirebaseService.php';
        include_once \dirname(__DIR__, 4).'/vendor/kreait/firebase-php/src/Firebase/Factory.php';

        return $container->privates['App\\Services\\FirebaseService'] = new \App\Services\FirebaseService(new \Kreait\Firebase\Factory($container->getEnv('GOOGLE_APPLICATION_CREDENTIALS')));
    }
}
