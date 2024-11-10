<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use Orchestra\Testbench\TestCase;

final class EntityTest extends TestCase
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

    /**
     * Testing the scope ofModel.
     */
    public function testScopeOfModel(): void
    {
        $counts = [3, 5, 7];
        $total = 0;
        for ($m = 0; $m < count($counts); $m++) {
            $modelName = 'model'.$m;
            Entity::factory($counts[$m])->create(['model' => $modelName]);
            $total = $total + $counts[$m];
            $scoped = Entity::query()
                ->ofModel($modelName)
                ->get();
            $this->assertEquals($counts[$m], $scoped->count());
        }
        $this->assertDatabaseCount('entities', $total);
    }

    /**
     * Testing scope ChildrenOf
     */
    public function testScopeChildrenOf(): void
    {
        $parentId = 'parentId';
        $childId = 'childId';
        $childCount = 2;
        Entity::factory()->create(['id' => $parentId]);
        Entity::factory(5)->create();
        Entity::factory($childCount)->create([
            'parent_entity_id' => $parentId
        ]);
        $scoped = Entity::query()
            ->childrenOf($parentId)
            ->get();
        $this->assertEquals($childCount, $scoped->count());
    }

    /**
     * Testing combination of ChildrenOf and ofModel scopes
     */
    public function testScopeChildrenOfAndOfModel(): void
    {
        $parentId = 'parentId';
        $model1 = 'model1';
        $model2 = 'model2';
        $model1Count = 3;
        $model2Count = 5;
        Entity::factory()->create(['id' => $parentId]);
        Entity::factory($model1Count)->create([
            'parent_entity_id' => $parentId,
            'model' => $model1
        ]);
        Entity::factory($model2Count)->create([
            'parent_entity_id' => $parentId,
            'model' => $model2
        ]);
        $scoped = Entity::query()
            ->childrenOf($parentId)
            ->ofModel($model1)
            ->get();
        $this->assertEquals($model1Count, $scoped->count());
        $scoped = Entity::query()
            ->childrenOf($parentId)
            ->ofModel($model2)
            ->get();
        $this->assertEquals($model2Count, $scoped->count());
    }
}
