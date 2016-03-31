<?php

namespace DR\Gallery\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;

class GalleryModuleConfigTest extends \PHPUnit_Framework_TestCase
{
    private $moduleName = 'DR_Gallery';

    /**
     * @test
     */
    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey($this->moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    /**
     * @test
     */
    public function testTheModuleIsConfiguredAndEnabled()
    {
        $objectManager = Bootstrap::getObjectManager();

        $moduleList = $objectManager->create(ModuleList::class);

        $this->assertTrue($moduleList->has($this->moduleName));
    }
}