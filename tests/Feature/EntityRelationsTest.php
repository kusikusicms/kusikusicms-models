<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use KusikusiCMS\Models\EntityRelation;
use Orchestra\Testbench\TestCase;

final class EntityRelationsTest extends TestCase
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
     * Testing scope ParentOf
     */
    public function testScopeFilter(): void
    {
        $entities = ['entity1', 'entity2', 'entity3', 'entity4', 'entity5', 'entity6'];
        $kinds = ['kind1', 'kind2', 'kind3', 'kind4', 'kind5'];
        $positions = [1, 2, 3, 4];
        $depths = [1, 2, 3];
        $tags = ['tag1', 'tag2'];
        Entity::factory(5)->create(); // Create random entities to be sure they are not included
        foreach ($entities as $id) {
            Entity::create(['id' => $id]);
        }
        $position = 0;
        $depth = 0;
        $tag = 0;
        foreach ($entities as $caller) {
            foreach ($entities as $called) {
                foreach ($kinds as $kind) {
                    EntityRelation::create([
                        'caller_entity_id' => $caller,
                        'called_entity_id' => $called,
                        'kind' => $kind,
                        'position' => $positions[$position],
                        'depth' => $depths[$depth],
                        'tags' => [$tags[$tag]],
                    ]);
                    $position ++; if ($position >= count($positions)) $position = 0;
                    $depth ++;    if ($depth >= count($depths))       $depth = 0;
                    $tag ++;      if ($tag >= count($tags))           $tag = 0;
                }
            }
        }
        $all = EntityRelation::query()->get();
        $total = count($entities) * count($entities) * count($kinds);
        $this->assertCount($total, $all);
        $oneKind = EntityRelation::query()
            ->filter(['kind' => $kinds[0]])
            ->get();
        $this->assertCount(ceil($total/count($kinds)), $oneKind);
        $oneCaller = EntityRelation::query()
            ->filter(['caller_entity_id' => $entities[0]])
            ->get();
        $this->assertCount(ceil($total/count($entities)), $oneCaller);
        $oneCalled = EntityRelation::query()
            ->filter(['called_entity_id' => $entities[0]])
            ->get();
        $this->assertCount(ceil($total/count($entities)), $oneCalled);
        $onePosition = EntityRelation::query()
            ->filter(['position' => $positions[0]])
            ->get();
        $this->assertCount(ceil($total/count($positions)), $onePosition);
        $oneDepth = EntityRelation::query()
            ->filter(['depth' => $depths[0]])
            ->get();
        $this->assertCount(ceil($total/count($depths)), $oneDepth);
        $oneTag = EntityRelation::query()
            ->filter(['tag' => $tags[0]])
            ->get();
        $this->assertCount(ceil($total/count($tags)), $oneTag);
        // TODO: Test combinations of filters
    }
}
