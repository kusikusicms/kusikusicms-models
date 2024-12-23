<?php

namespace Unit;

use KusikusiCMS\Models\Support\EntityCollection;
use PHPUnit\Framework\TestCase;

final class EntityCollectionTest extends TestCase
{
    /**
     * An Entity Collection can be constructed.
     */
    public function testAnEntityCanBeConstructed(): void
    {
        $collection = new EntityCollection();
        $this->assertInstanceOf(EntityCollection::class, $collection);
    }
}
