<?php

namespace spec\Labstag\Repository;

use Labstag\Repository\CategoryRepository;
use PhpSpec\ObjectBehavior;

class CategoryRepositorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CategoryRepository::class);
    }
}