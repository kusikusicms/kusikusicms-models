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

    public function testWithContentsScope(): void
    {
        $e1 = Entity::query()->create(["id" => "e1"]);
        $e1->createContent(["title" => "The title 1", "body" => "The body 1"], "en");
        $e1->createContent(["title" => "El título 1", "body" => "El cuerpo 1"], "es");
        $e2 = Entity::query()->create(["id" => "e2"]);
        $e2->createContent(["title" => "The title 2", "body" => "The body 2"], "en");
        $e2->createContent(["title" => "El título 2", "body" => "El cuerpo 2"], "es");

        $entity = Entity::query()->withContents('es')->find("e1"); ;
        $this->assertCount(2, $entity->contents);
        $this->assertTrue($this->oneContentExists($entity->contents, "title", "El título 1", "es"));

        $entity = Entity::query()->withContents('en', ["body"])->find("e2"); ;
        $this->assertCount(1, $entity->contents);
        $this->assertTrue($this->oneContentExists($entity->contents, "body", "The body 2", "en"));
    }

    public function testOrderByContentScope(): void
    {
        $e1 = Entity::query()->create(["id" => "e1"]);
        $e1->createContent(["title" => "Gamma title 1",  "body" => "Delta body 1"], "en");
        $e1->createContent(["title" => "Gamma título 1", "body" => "Gamma cuerpo 1"], "es");

        $e2 = Entity::query()->create(["id" => "e2"]);
        $e2->createContent(["title" => "Beta title 2",  "body" => "Beta body 2"], "en");
        $e2->createContent(["title" => "Beta título 2", "body" => "Delta cuerpo 2"], "es");

        $e3 = Entity::query()->create(["id" => "e3"]);
        $e3->createContent(["title" => "Alpha title 3",  "body" => "Gamma body 3"], "en");
        $e3->createContent(["title" => "Delta título 3", "body" => "Beta cuerpo 3"], "es");

        $e4 = Entity::query()->create(["id" => "e4"]);
        $e4->createContent(["title" => "Delta title 4",  "body" => "Alpha body 4"], "en");
        $e4->createContent(["title" => "Alpha título 4", "body" => "Alpha cuerpo 4"], "es");

        $entities = Entity::query()->select('id')->orderByContent('title')->get();
        $this->assertEquals(["e3", "e2", "e4", "e1"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->orderByContent('title', 'desc')->get();
        $this->assertEquals(["e1", "e4", "e2", "e3"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->orderByContent('title', 'asc', 'en')->get();
        $this->assertEquals(["e3", "e2", "e4", "e1"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->orderByContent('body', 'desc', 'es')->get();
        $this->assertEquals(["e1", "e2", "e3", "e4"], $entities->pluck('id')->all());
    }

    public function testWhereContentScope(): void
    {
        $e1 = Entity::query()->create(["id" => "e1"]);
        $e1->createContent(["title" => "Gamma title 1",  "body" => "Delta body 1"], "en");
        $e1->createContent(["title" => "Gamma título 1", "body" => "Gamma cuerpo 1"], "es");

        $e2 = Entity::query()->create(["id" => "e2"]);
        $e2->createContent(["title" => "Beta title 2",  "body" => "Beta body 2"], "en");
        $e2->createContent(["title" => "Beta título 2", "body" => "Delta cuerpo 2"], "es");

        $e3 = Entity::query()->create(["id" => "e3"]);
        $e3->createContent(["title" => "Alpha title 3",  "body" => "Gamma body 3"], "en");
        $e3->createContent(["title" => "Delta título 3", "body" => "Beta cuerpo 3"], "es");

        $e4 = Entity::query()->create(["id" => "e4"]);
        $e4->createContent(["title" => "Delta title 4",  "body" => "Alpha body 4"], "en");
        $e4->createContent(["title" => "Alpha título 4", "body" => "Alpha cuerpo 4"], "es");

        $entities = Entity::query()->select('id')->whereContent('body', 'Beta cuerpo 3')->get();
        $this->assertEquals(["e3"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->whereContent('body', '=', 'Alpha body 4')->get();
        $this->assertEquals(["e4"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->whereContent('body', 'like', 'Gamma')->get();
        $this->assertEquals(["e1", "e3"], $entities->pluck('id')->all());

        $entities = Entity::query()->select('id')->whereContent('body', 'like', 'Gamma', 'en')->get();
        $this->assertEquals(["e3"], $entities->pluck('id')->all());

    }
}
