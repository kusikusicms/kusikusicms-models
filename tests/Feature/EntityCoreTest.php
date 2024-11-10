<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use Orchestra\Testbench\TestCase;

final class EntityCoreTest extends TestCase
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
     * An Entity can be saved.
     */
    public function testAnEntityCanBeSaved(): void
    {
        $entity = new Entity;
        $entity->save();
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertDatabaseHas('entities', [
            'id' => $entity->id,
        ]);
    }

    /**
     * An Entity is saved with default values.
     */
    public function testAnEntityIsSavedWithDefaultValues(): void
    {
        $entity = new Entity;
        $entity->save();
        $this->assertNotNull($entity->id);
        $this->assertNotNull($entity->published_at);
        $this->assertEquals('Entity', $entity->model);
        $this->assertEquals('entity', $entity->view);
        $this->assertEquals([], $entity->properties);
    }

    /**
     * A custom entity id can be set.
     */
    public function testACustomIdCanBeSet(): void
    {
        $id = 'customId';
        $entity = new Entity([
            'id' => $id,
        ]);
        $this->assertEquals($entity->id, $id);
        $entity->save();
        $this->assertDatabaseHas('entities', [
            'id' => $id,
        ]);
        $this->assertModelExists($entity);
    }

    /**
     * A custom model id can be set.
     */
    public function testACustomModelIdCanBeSet(): void
    {
        $model = 'CustomModel';
        $entity = new Entity([
            'model' => $model,
        ]);
        $this->assertEquals($model, $entity->model);
        $entity->save();
        $this->assertDatabaseHas('entities', [
            'model' => $model,
        ]);
        $this->assertModelExists($entity);
    }
}
