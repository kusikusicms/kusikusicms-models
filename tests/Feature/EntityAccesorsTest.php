<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use KusikusiCMS\Models\EntityRelation;
use Orchestra\Testbench\TestCase;

final class EntityAccesorsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            'KusikusiCMS\Models\EntityEventsServiceProvider',
            'KusikusiCMS\Models\ModelsServiceProvider',
        ];
    }

    /**
     * Testing the scope ofModel.
     */
    public function testStatusAccesor(): void
    {
        $entity = Entity::create();
        $this->assertEquals('published', $entity->status);
        $entity->publish_at = now()->subtract(10, 'day');
        $entity->unpublish_at = now()->add(10, 'day');
        $this->assertEquals('published', $entity->status);
        $entity->publish_at = now()->add(1, 'day');
        $this->assertEquals('scheduled', $entity->status);
        $entity->publish_at = now()->subtract(10, 'day');
        $entity->unpublish_at = now()->subtract(1, 'day');
        $this->assertEquals('outdated', $entity->status);
        $entity->published = false;
        $this->assertEquals('draft', $entity->status);
    }
}
