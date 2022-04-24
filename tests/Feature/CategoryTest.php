<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_can_return_all_public_categories()
    {
        Category::factory(2)->forUser()->private()->create();
        Category::factory(3)->forUser()->public()->create();

        $response = $this->getJson(route('categories.index'));

        $response->assertJsonCount(3, 'data')
            ->assertOk();
    }

    public function test_user_can_search_public_categories()
    {
        Category::factory(3)->forUser()->public()->create();
        Category::factory()->forUser()->public()->create([
            'name' => 'foo'
        ]);

        $response = $this->getJson(route('categories.index', ['query' => 'foo']));

        $response->assertJsonCount(1, 'data')
            ->assertOk();
    }

    public function test_guest_user_cannot_get_user_categories()
    {
        $user = User::factory()->create();
        $response = $this->getJson(route('users.categories.index', [$user]));

        $response->assertUnauthorized();
    }

    public function test_user_can_get_his_categories()
    {
        $user = User::factory()->create();
        Category::factory(3)->for($user)->private()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('users.categories.index', [$user]));

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_get_another_user_categories()
    {
        $user = User::factory()->create();
        Category::factory(3)->for($user)->private()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('users.categories.index', [User::factory()->create()]));

        $response->assertForbidden();
    }

    public function test_user_can_get_public_category()
    {
        $category = Category::factory()->forUser()->public()->create();

        $response = $this->getJson(route('categories.show', [$category]));

        $response->assertOk();
    }

    public function test_user_can_get_his_private_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()->for($user)->private()->create();

        $response = $this->getJson(route('categories.show', [$category]));

        $response->assertOk();
    }

    public function test_user_cannot_get_another_user_private_category()
    {
        $category = Category::factory()->forUser()->private()->create();

        $response = $this->getJson(route('categories.show', [$category]));

        $response->assertForbidden();
    }

    public function test_guest_user_can_not_create_category()
    {
        $response = $this->postJson(route('categories.store'), [
            'name' => 'test',
            'is_public' => true,
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_create_category()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('categories.store'), [
            'name' => 'test',
            'is_public' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'test');
    }

    public function test_guest_user_can_not_update_category()
    {
        $category = Category::factory()->forUser()->create();

        $response = $this->patchJson(route('categories.update', $category), [
            'name' => 'test',
            'is_public' => true,
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->for($user)->create();

        $response = $this->patchJson(route('categories.update', $category), [
            'name' => 'test',
            'is_public' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'test');
    }

    public function test_guest_user_can_not_delete_category()
    {
        $category = Category::factory()->forUser()->create();

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertUnauthorized();
    }

    public function test_user_can_delete_his_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->for($user)->create();

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertOk();
    }

    public function test_user_can_not_delete_another_user_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::factory()->forUser()->create();

        $response = $this->deleteJson(route('categories.destroy', $category));

        $response->assertForbidden();
    }
}
