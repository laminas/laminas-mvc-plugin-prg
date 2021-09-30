<?php

namespace Laminas\Mvc\Plugin\Prg;

use Laminas\ServiceManager\Factory\InvokableFactory;

class Module
{
    /**
     * Provide application configuration.
     *
     * Adds aliases and factories for the PostRedirectGet plugin.
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'controller_plugins' => [
                'aliases' => [
                    'prg'             => PostRedirectGet::class,
                    'PostRedirectGet' => PostRedirectGet::class,
                    'postRedirectGet' => PostRedirectGet::class,
                    'postredirectget' => PostRedirectGet::class,
                    'Laminas\Mvc\Controller\Plugin\PostRedirectGet' => PostRedirectGet::class,

                    // Legacy Zend Framework aliases
                    'Zend\Mvc\Controller\Plugin\PostRedirectGet' => 'Laminas\Mvc\Controller\Plugin\PostRedirectGet',
                    \Zend\Mvc\Plugin\Prg\PostRedirectGet::class => PostRedirectGet::class,
                ],
                'factories' => [
                    PostRedirectGet::class => InvokableFactory::class,
                ],
            ],
        ];
    }
}
