<?php

namespace Labstag\Handler;

use Labstag\Entity\Post;

class PostPublishingHandler
{
    public function handle(Post $entity): void
    {
        unset($entity);
        // your logic for publishing book or/and eg. return your custom data
    }
}
