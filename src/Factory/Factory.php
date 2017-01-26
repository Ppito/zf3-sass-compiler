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

namespace zf3SassCompiler\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class Factory implements FactoryInterface  {

    /**
     * Invoke Handler
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param string                                $requestedName
     * @param array|null                            $options
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $config = $container->has('config') ? $container->get('config') : [];
        $config = isset($config['zf3-sass']) ? $config['zf3-sass'] : [];

        return new $requestedName($container, $config);
    }
}