<?php
/**
 * Created by PhpStorm.
 * User: Ppito
 * Date: 01/23/2017
 * Time: 09:12 PM
 *
 * @link      https://github.com/Ppito/zf3-sass for the canonical source repository
 * @copyright Copyright (c) 2017 Mickael TONNELIER.
 * @license   https://github.com/Ppito/zf3-whoops/blob/master/LICENSE.md The MIT License
 */

namespace zf3SassCompiler;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Zend\ServiceManager\ServiceManager;
use Zend\Http\Response as HttpResponse;
use Zend\View\ViewEvent;

class Module implements ConfigProviderInterface, BootstrapListenerInterface {

    /**
     * Return default zend-serializer configuration for zend-mvc applications.
     */
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to the bootstrap event
     *
     * @param \Zend\Mvc\MvcEvent|EventInterface $e
     * @return void
     */
    public function onBootstrap(EventInterface $e) {

        $application = $e->getApplication();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $application->getServiceManager();

        $this->configureEvent($serviceManager);
    }

    /**
     * Configure zf3SassCompiler Service
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    protected function configureEvent(ContainerInterface $container) {
        /** @var Service\SassCompilerService $service */
        $service = $container->has(Service\SassCompilerService::class) ?
            $container->get(Service\SassCompilerService::class) :
            null;

        if ($service) {
            $service->getEventManager()
                    ->attach(ViewEvent::EVENT_RESPONSE, [
                        $service,
                        'prepareStyle',
                    ]);
        }
    }
}
