<?php

namespace Labstag\Handler;

use Labstag\Entity\Configuration;

class ConfigurationPublishingHandler
{
    public function handle(Configuration $entity): void
    {
        unset($entity);
        // your logic for publishing book or/and eg. return your custom data
    }
}
