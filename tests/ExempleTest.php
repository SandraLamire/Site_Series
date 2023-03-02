<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class ExempleTest extends TestCase
{
    public function testSomething(): void
    {
        // est-ce que vrai c'est vrai!
        // $this->assertTrue(true);
        $this->assertTrue(2==2, 'l\'égalité est fausse');

    }
}
