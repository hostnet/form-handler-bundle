<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
declare(strict_types=1);

namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures;

use Symfony\Component\Validator\Constraints as Assert;

class TestData
{
    #[Assert\NotBlank]
    public ?string $test;
}
