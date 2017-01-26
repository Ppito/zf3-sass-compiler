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
use Zend\View\View;
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

        $this->configureService($serviceManager);
        /** @var View $view */
        $view = $serviceManager->has(View::class) ?
            $serviceManager->get(View::class) :
            null;

        if ($view) {
            $view->getEventManager()
                 ->attach(ViewEvent::EVENT_RESPONSE, [
                     $this,
                     'prepareException2',
                 ]);
        }
    }

    /**
     * Whoops handle exceptions
     *
     * @param ViewEvent $e
     */
    public function prepareException2(ViewEvent $e) {
        /** @var \Zend\View\Renderer\PhpRenderer|\ZendTwig\Renderer\TwigRenderer $renderer */
        $renderer = $e->getRenderer();
        if ($renderer instanceof \ZendTwig\Renderer\TwigRenderer) {
            $pluginManager = $renderer->getZendHelpers();
        } else {
            $pluginManager = $renderer->getHelperPluginManager();
        }
        $containerLink = $pluginManager->get('headLink')->getContainer();

        $styles = [];
        foreach ($containerLink as $link) {
            if (isset($link->rel) && $link->rel == 'stylesheet') {
                $res = preg_match('#^(?:http://|https://|//)([^/]+)(.*)$#', $link->href, $matches);
                if (!$res) {
                    $styles[] = $link->href;
                } else if ($matches[1] == $_SERVER['SERVER_NAME']) {
                    $styles[] = $matches[2];
                }
            }
        }
    }
    /**
     * Configure Whoops Service
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    protected function configureService(ContainerInterface $container) {
        $config = $container->has('config') ? $container->get('config') : [];
        $config = isset($config['zf3-sass']) ? $config['zf3-sass'] : [];

    }

}
