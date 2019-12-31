<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-plugin-prg for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-plugin-prg/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-plugin-prg/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Mvc\Plugin\Prg\TestAsset;

use Laminas\Mvc\Controller\AbstractActionController;

class SampleController extends AbstractActionController
{
    /**
     * Override notFoundAction() to work as a no-op.
     */
    public function notFoundAction()
    {
    }
}
