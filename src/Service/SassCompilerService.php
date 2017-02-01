<?php
/**
 * Created by PhpStorm.
 * User: mickael
 * Date: 1/26/17
 * Time: 4:40 PM
 */

namespace zf3SassCompiler\Service;


use Interop\Container\ContainerInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\View;
use Zend\View\ViewEvent;
use Leafo\ScssPhp\Compiler as Scss;

class SassCompilerService {

    /** @var array|null */
    protected $options = [];
    /** @var ContainerInterface */
    protected $container;
    /** @var \Zend\EventManager\EventManager|null  */
    protected $eventManager = null;

    /** @var array */
    protected $stylesheets = [];

    /**
     * HandlerAbstract constructor.
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param array                                 $options
     * @return self
     */
    public function __construct(ContainerInterface $container, $options = []) {
        $this->options   = $options;
        $this->container = $container;

        $view = $container->has(View::class) ?
            $container->get(View::class) :
            null;
        $this->eventManager = $view ?
            $view->getEventManager() :
            null;
        return $this;
    }

    /**
     * Whoops handle exceptions
     *
     * @param ViewEvent $e
     * @return self
     */
    public function prepareStyle(ViewEvent $e) {
        $this->detachEvent();
        $this->getStylesheet($e);

        foreach ($this->stylesheets as $stylesheet) {
            if (!file_exists($url = $_SERVER['DOCUMENT_ROOT'] . $stylesheet)) {
                $this->compile($url);
            }
        }
        return $this;
    }

    /**
     * Detach Event to avoid loop call if an error occurred in this module
     *  - For exemple : during compilation
     *
     * @return self
     */
    protected function detachEvent() {
        $this
            ->getEventManager()
            ->detach([$this, 'prepareStyle'], ViewEvent::EVENT_RESPONSE);
        return $this;
    }

    /**
     * Compile file
     *
     * @param string $url
     */
    private function compile($url) {
        $compile = $this->getOption('compile');
        $src     = $compile['directory']['src'];

        $urlSass = $src . '/' . str_replace('.css', '.scss',  basename($url));
        if (file_exists($urlSass)) {
            $input = file_get_contents($urlSass);
            $scss  = new Scss();
            $paths = $this->getOption('importPath');
            foreach ($paths as $path) {
                $scss->addImportPath($path);
            }
            $style = $compile['format'];
            $scss->setFormatter('Leafo\ScssPhp\Formatter\\' . ($style ? ucfirst($style) : 'Nested'));
            $scss->setLineNumberStyle(Scss::LINE_COMMENTS);

            $out = $scss->compile($input);
            file_put_contents($url, $out);
        }
    }

    /**
     * Parse link render
     *
     * @param ViewEvent $e
     */
    protected function getStylesheet(ViewEvent $e) {
        /** @var \Zend\View\Renderer\PhpRenderer $renderer */
        $renderer = $e->getRenderer();
        $pluginManager = $renderer->getHelperPluginManager();
        $containerLink = $pluginManager->get('headLink')->getContainer();

        foreach ($containerLink as $link) {
            if (isset($link->rel) && $link->rel == 'stylesheet') {
                $res = preg_match('#^(?:http://|https://|//)([^/]+)(.*)$#', $link->href, $matches);
                if (!$res) {
                    $this->stylesheets[] = $link->href;
                } else if ($matches[1] == $_SERVER['SERVER_NAME']) {
                    $this->stylesheets[] = $matches[2];
                }
            }
        }
    }

    /**
     * @return array|null
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function getOption($name) {

        return array_key_exists($name, $this->options) ?
            $this->options[$name] :
            null;
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * @return null|\Zend\EventManager\EventManager
     */
    public function getEventManager() {
        return $this->eventManager;
    }
}