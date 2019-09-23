<?php

namespace Labstag\Tests\Service;

use Labstag\Lib\ServiceTestLib;
use Labstag\Service\OauthService;

class OauthTest extends ServiceTestLib
{

    /**
     * @var OauthService
     */
    private $service;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::$container->get(OauthService::class);
    }

    public function testgetIdentity()
    {
        $service = $this->service;
        $empty   = $service->getIdentity(null, null);
        $this->assertTrue(is_null($empty));
        $empty = $service->getIdentity(null, null);
        var_dump(get_class_methods($this->service));
    }

    public function testgetActivedProvider()
    {
        $service       = $this->service;
        $empty         = $service->getActivedProvider(null);
        $falseProvider = $service->getActivedProvider(md5('gitlab'));
        $gitlab        = $service->getActivedProvider('gitlab');
        $this->assertTrue(is_false($empty));
    }

    public function testsetProvider()
    {
        $service = $this->service;
        $empty = $service->setProvider(null);
        $gitlab = $service->setProvider('gitlab');
        
    }
}