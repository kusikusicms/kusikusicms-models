<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use Orchestra\Testbench\TestCase;

final class EntityScopesTest extends TestCase
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
        $childCount = 2;
        Entity::factory()->create(['id' => $parentId]);
        Entity::factory(5)->create();
        Entity::factory($childCount)->create([
            'parent_entity_id' => $parentId,
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
            'model' => $model1,
        ]);
        Entity::factory($model2Count)->create([
            'parent_entity_id' => $parentId,
            'model' => $model2,
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

    /**
     * Testing scope ParentOf
     */
    public function testScopeParentOf(): void
    {
        $parentId = 'parentId';
        $childId = 'childId';
        Entity::factory()->create(['id' => $parentId]);
        Entity::factory(5)->create(); // Create random entities to be sure they are not included
        Entity::factory()->create([
            'id' => $childId,
            'parent_entity_id' => $parentId,
        ]);
        $scoped = Entity::query()
            ->select('model')
            ->parentOf($childId);
        $this->assertEquals(1, $scoped->get()->count());
        $this->assertEquals($parentId, $scoped->first()->id);
    }

    /**
     * Testing scope ParentOf
     */
    public function testAncestorsOf(): void
    {
        $levels = 5;
        $parentId = null;
        Entity::factory(5)->create(); // Create random entities to be sure they are not included
        for ($l = 0; $l < $levels; $l++) {
            $entityId = 'entity'.$l;
            Entity::factory()->create([
                'id' => $entityId,
                'parent_entity_id' => $parentId,
            ]);
            $parentId = $entityId;
            $scoped = Entity::query()
                ->ancestorsOf($entityId)
                ->get();
            $this->assertEquals($l, $scoped->count());
        }
    }
}
