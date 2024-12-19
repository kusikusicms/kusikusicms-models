<?php

namespace Unit;

use KusikusiCMS\Models\EntityContent;
use PHPUnit\Framework\TestCase;

final class EntityContentTest extends TestCase
{
    /**
     * An Entity can be constructed.
     */
    public function testAnEntityContentCanBeConstructed(): void
    {
        $entityContent = new EntityContent;
        $this->assertInstanceOf(EntityContent::class, $entityContent);
    }
}
