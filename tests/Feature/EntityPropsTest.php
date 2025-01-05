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
use Orchestra\Testbench\TestCase;
use function PHPUnit\Framework\assertCount;

final class EntityPropsTest extends TestCase
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

    function someProps(): array
    {
        return [
            "stringProp" => "value1",
            "numberProp" => 2,
            "booleanProp" => true,
            "arrayProp" => ["value1", "value2"],
            "objectProp" => ["key1" => "value1", "key2" => "value2"]
        ];
    }

    function someEntitiesWithProps() {
        Entity::query()->create(['id' => 'e1', 'props' => ['code' => 'Alpha', 'order' => 4]]);
        Entity::query()->create(['id' => 'e2', 'props' => ['code' => 'Gamma', 'order' => 2]]);
        Entity::query()->create(['id' => 'e3', 'props' => ['code' => 'Delta', 'order' => 1]]);
        Entity::query()->create(['id' => 'e4', 'props' => ['code' => 'Beta', 'order' => 3]]);
    }

    /**
     * Testing create an entity with props
     */
    public function testCreateEntityWithProps(): void
    {
        $props = $this->someProps();
        $entity = Entity::query()->create(["props" => $props]);
        $this->assertEquals($props['stringProp'], $entity->props['stringProp']);
        $this->assertEquals($props['numberProp'], $entity->props['numberProp']);
        $this->assertEquals($props['booleanProp'], $entity->props['booleanProp']);
        $this->assertEquals($props['arrayProp'], $entity->props['arrayProp']);
        $this->assertEquals($props['objectProp'], $entity->props['objectProp']);
    }

    /**
     * Testing adding props to an entity
     */
    public function testAddPropsToAnEntity(): void
    {
        $props = $this->someProps();
        $entity = Entity::query()->create();
        $entity->props = $props;
        $entity->save();
        $this->assertEquals($props['stringProp'], $entity->props['stringProp']);
        $this->assertEquals($props['numberProp'], $entity->props['numberProp']);
        $this->assertEquals($props['booleanProp'], $entity->props['booleanProp']);
        $this->assertEquals($props['arrayProp'], $entity->props['arrayProp']);
        $this->assertEquals($props['objectProp'], $entity->props['objectProp']);
    }

    /**
     * Testing setting individual props
     */
    public function testSetProp(): void
    {
        $entity = Entity::query()->create();
        $entity->setProp('alpha', 'beta'); $entity->save();
        $this->assertEquals($entity->props['alpha'], 'beta');
        $entity->setProp('one.two', 'gamma'); $entity->save();
        $this->assertEquals($entity->props['one']['two'], 'gamma');
        $entity->setProp('one->two', 'gamma2'); $entity->save();
        $this->assertEquals($entity->props['one']['two'], 'gamma2');
    }
    /**
     * Testing getting individual props
     */
    public function testGetProp(): void
    {
        $entity = Entity::query()->create();
        $entity->setProp('alpha', 'beta'); $entity->save();
        $this->assertEquals($entity->getProp('alpha'), 'beta');
        $entity->setProp('one.two', 'gamma'); $entity->save();
        $this->assertEquals($entity->getProp('one.two'), 'gamma');
        $this->assertEquals($entity->getProp('one->two'), 'gamma');
    }

    public function testOrderByProp(): void
    {
        $this->someEntitiesWithProps();
        $entities = Entity::query()->select('id')->orderBy('props->order', 'desc')->get();
        $this->assertEquals($entities->get(0)->id, 'e1');
        $this->assertEquals($entities->get(3)->id, 'e3');

        $entities = Entity::query()->select('id')->orderBy('props->code')->get();
        $this->assertEquals($entities->get(0)->id, 'e1');
        $this->assertEquals($entities->get(3)->id, 'e2');
    }

    public function testWhereProps(): void
    {
        $this->someEntitiesWithProps();

        $entities = Entity::query()->select('id')->where('props->code', 'Beta')->get();
        $this->assertCount(1, $entities);
        $this->assertEquals($entities->get(0)->id, 'e4');

        $entities = Entity::query()->select('id')->where('props->order', '<', 3)->orderBy('props->order')->get();
        $this->assertCount(2, $entities);
        $this->assertEquals($entities->get(0)->id, 'e3');
        $this->assertEquals($entities->get(1)->id, 'e2');

    }
}
