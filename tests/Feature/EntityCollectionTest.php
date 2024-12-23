<?php

namespace Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use KusikusiCMS\Models\EntityContent;
use KusikusiCMS\Models\EntityRelation;
use KusikusiCMS\Models\Support\EntityCollection;
use Orchestra\Testbench\TestCase;
use function PHPUnit\Framework\assertCount;

final class EntityCollectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get package providers.
     *
     * @param  Application  $app
     *
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
     * Testing adding contents to an entity
     */
    public function testACollectionIsRetrieved(): void
    {
        $entity1 = Entity::query()->create();
        $entity2 = Entity::query()->create();
        $entities = Entity::all();
        $this->assertInstanceOf(EntityCollection::class, $entities);
    }
}
