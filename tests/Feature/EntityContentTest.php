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
     * Filter function
     */
    private function oneContentExists (Collection $contents, string $field, string $text, string $lang = null): bool {
        $lang = $lang ?? (Config::get('kusikusicms-models.default_language', 'en'));
        return $contents->filter(function (EntityContent $item) use ($field, $text, $lang) {
            return $item->field === $field && $item->text === $text && $item->lang === $lang;
        })->count() === 1;
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
        $this->assertTrue($this->oneContentExists($entity->contents, "title", "The title"));
        $this->assertTrue($this->oneContentExists($entity->contents, "body", "The body"));

        $entity->createContent([
            "summary" => "The summary"
        ]);
        $entity->refresh();
        $this->assertCount(3, $entity->contents);
        $this->assertTrue($this->oneContentExists($entity->contents, "summary", "The summary"));

        $entity->createContent([
            "title" => "The title 2",
            "body" => "The body 2"
        ]);
        $entity->refresh();
        $this->assertCount(3, $entity->contents);
        $this->assertTrue($this->oneContentExists($entity->contents, "title", "The title 2"));
        $this->assertTrue($this->oneContentExists($entity->contents, "body", "The body 2"));

        $entity->createContent([
            "title" => "El título"
        ], 'es');
        $entity->refresh();
        $this->assertCount(4, $entity->contents);
        $this->assertTrue($this->oneContentExists($entity->contents, "title", "The title 2"));
        $this->assertTrue($this->oneContentExists($entity->contents, "title", "El título", "es"));
    }
}
