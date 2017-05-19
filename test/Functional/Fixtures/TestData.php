<?php
/**
 * @copyright 2017 Hostnet B.V.
 */
namespace Hostnet\Bundle\FormHandlerBundle\Functional\Fixtures;

use Symfony\Component\Validator\Constraints as Assert;

class TestData
{
    /**
     * @Assert\NotBlank()
     */
    public $test;
}
