<?php

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
