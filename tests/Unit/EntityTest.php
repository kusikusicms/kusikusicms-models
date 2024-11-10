<?php

namespace Unit;

use KusikusiCMS\Models\Entity;
use PHPUnit\Framework\TestCase;

final class EntityTest extends TestCase
{
    /**
     * An Entity can be constructed.
     */
    public function testAnEntityCanBeConstructed(): void
    {
        $entity = new Entity;
        $this->assertInstanceOf(Entity::class, $entity);
    }
}
