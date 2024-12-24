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

final class EntityContentCollectionTest extends TestCase
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
     * Testing an Entity Collection is Received
     */
    public function testACollectionIsRetrieved(): void
    {
        Entity::query()->create();
        Entity::query()->create();
        Entity::query()->create();
        $entities = Entity::all();
        $this->assertInstanceOf(EntityCollection::class, $entities);
        $this->assertCount(3, $entities);
    }

    static function createSampleEntities() {
        $entity1 = Entity::create(['id' => 'e1']);
        $entity1->contents()->create(['lang' => 'en', 'field' => 'title', 'text' => 'Title 1']);
        $entity1->contents()->create(['lang' => 'en', 'field' => 'body', 'text' => 'Body 1']);
        $entity1->contents()->create(['lang' => 'es', 'field' => 'title', 'text' => 'Título 1']);
        $entity1->contents()->create(['lang' => 'es', 'field' => 'body', 'text' => 'Cuerpo 1']);
        $entity2 = Entity::create(['id' => 'e2']);
        $entity2->contents()->create(['lang' => 'en', 'field' => 'title', 'text' => 'Title 2']);
        $entity2->contents()->create(['lang' => 'en', 'field' => 'body', 'text' => 'Body 2']);
        $entity2->contents()->create(['lang' => 'es', 'field' => 'title', 'text' => 'Título 2']);
        $entity2->contents()->create(['lang' => 'es', 'field' => 'body', 'text' => 'Cuerpo 2']);
        $entity3 = Entity::create(['id' => 'e3']);
        $entity3->contents()->create(['lang' => 'en', 'field' => 'title', 'text' => 'Title 3']);
        $entity3->contents()->create(['lang' => 'en', 'field' => 'body', 'text' => 'Body 3']);
        $entity3->contents()->create(['lang' => 'es', 'field' => 'title', 'text' => 'Título 3']);
        $entity3->contents()->create(['lang' => 'es', 'field' => 'body', 'text' => 'Cuerpo 3']);
    }

    /**
     * Testing an Entity Collection can transform the contents
     */
    public function testByFieldMethod(): void
    {
        $this->createSampleEntities();

        $entity = Entity::query()->withContents('en')->find('e1');
        $contents = $entity->contents->flattenByField();
        $this->assertEquals('Title 1', $contents['title']);
        $this->assertEquals('Body 1', $contents['body']);

        $entity = Entity::query()->withContents('es')->find('e2');
        $contents = $entity->contents->flattenByField();
        $this->assertEquals('Título 2', $contents['title']);
        $this->assertEquals('Cuerpo 2', $contents['body']);
    }

    /**
     * Testing an Entity Collection can transform the contents relation
     */
    public function testContentsByFieldMethod(): void
    {
        $this->createSampleEntities();

        $entity = Entity::query()->withContents('en')->find('e1')->flattenContentsByField();
        $this->assertEquals('Title 1', $entity->contents['title']);
        $this->assertEquals('Body 1', $entity->contents['body']);

        $entity = Entity::query()->withContents('es')->find('e2')->flattenContentsByField();;
        $this->assertEquals('Título 2', $entity->contents['title']);
        $this->assertEquals('Cuerpo 2', $entity->contents['body']);
    }

    public function testGroupContentsByFieldMethod(): void
    {
        $this->createSampleEntities();

        $entity = Entity::query()->withContents()->find('e2');;
        $entity->groupContentsByField();;
        $this->assertEquals('Title 2', $entity->contents['title']['en']);
        $this->assertEquals('Título 2', $entity->contents['title']['es']);
        $this->assertEquals('Body 2', $entity->contents['body']['en']);
        $this->assertEquals('Cuerpo 2', $entity->contents['body']['es']);
    }

    public function testGroupContentsByLangMethod(): void
    {
        $this->createSampleEntities();

        $entity = Entity::query()->withContents()->find('e3');;
        $entity->groupContentsByLang();;
        $this->assertEquals('Title 3', $entity->contents['en']['title']);
        $this->assertEquals('Título 3', $entity->contents['es']['title']);
        $this->assertEquals('Body 3', $entity->contents['en']['body']);
        $this->assertEquals('Cuerpo 3', $entity->contents['es']['body']);
    }
}
