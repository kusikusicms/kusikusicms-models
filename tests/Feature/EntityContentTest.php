<?php

namespace Feature;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ServiceProvider;
use KusikusiCMS\Models\Entity;
use KusikusiCMS\Models\EntityContent;
use KusikusiCMS\Models\EntityRelation;
use Orchestra\Testbench\TestCase;
use function PHPUnit\Framework\assertCount;

final class EntityContentTest extends TestCase
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
    public function testAddContents(): void
    {
        $entity1 = Entity::query()->create();
        $entity2 = Entity::query()->create();
        $entity1->contents()->create([
            "lang" => "en-US",
            "field" => "title",
            "text" => "The Title"
        ]);
        $entity1->contents()->create([
            "lang" => "en-US",
            "field" => "body",
            "text" => "The Body"
        ]);
        $entity2->contents()->create([
            "lang" => "en-US",
            "field" => "body",
            "text" => "The Body 2"
        ]);
        $allContents = EntityContent::query()->get();
        $this->assertCount(3, $allContents);
        $entity1WithContents = Entity::query()
            ->with("contents")
            ->find($entity1->id);
        $this->assertCount(2, $entity1WithContents->contents);
    }
    /**
     * Testing creating contents shorthand
     */
    public function testCreateContents(): void
    {
        $entity = Entity::query()->create();
        $entity->createContent([
            "title" => "The title",
            "body" => "The body"
        ]);
        $entity->refresh();
        $this->assertCount(2, $entity->contents);
        $this->assertTrue($entity->contents->contains("text", "The title"));

        $entity->createContent([
            "summary" => "The summary"
        ]);
        $entity->refresh();
        $this->assertCount(3, $entity->contents);
        $this->assertTrue($entity->contents->contains("text", "The body"));

        $entity->createContent([
            "title" => "The title 2",
            "body" => "The body 2"
        ]);
        $entity->refresh();
        $this->assertCount(3, $entity->contents);

        $entity->createContent([
            "title" => "El título"
        ], 'es');
        $entity->refresh();
        $this->assertCount(4, $entity->contents);
        $this->assertTrue($entity->contents->contains("text", "El título"));
    }
}
