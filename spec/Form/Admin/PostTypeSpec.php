<?php

namespace spec\Labstag\Form\Admin;

use Labstag\Form\Admin\PostType;
use PhpSpec\ObjectBehavior;

class PostTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PostType::class);
    }
}