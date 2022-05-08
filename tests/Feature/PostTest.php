<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_his_posts()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()
            ->for($user)
            ->has(Post::factory(3)->for($user))
            ->private()
            ->create();

        $response = $this->get(route('categories.posts.index', [$category]));

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_get_posts_from_public_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()
            ->for($user)
            ->has(Post::factory(3)->for($user))
            ->public()
            ->create();

        $response = $this->get(route('categories.posts.index', [$category]));

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_cannot_get_another_user_posts_from_private_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()
            ->for($user)
            ->has(Post::factory(3)->for($user))
            ->private()
            ->create();

        $response = $this->get(route('categories.posts.index', [$category]));

        $response->assertForbidden();
    }

    public function test_user_can_create_post_in_his_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('categories.posts.store', [$category]), [
            'word' => 'test',
            'meaning' => 'testing',
            'body' => 'test body',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.word', 'test');
    }

    public function test_guest_user_cannot_create_post()
    {
        $category = Category::factory()->forUser()->create();
        $response = $this->postJson(route('categories.posts.store', [$category]));

        $response->assertUnauthorized();
    }

    public function test_user_cannot_create_post_in_another_user_category()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()->forUser()->create();

        $response = $this->postJson(route('categories.posts.store', [$category]));

        $response->assertForbidden();
    }

    public function test_user_can_get_post_from_public_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->public()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        $response = $this->get(route('categories.posts.show', [$category, $post]));

        $response->assertOk();
    }

    public function test_user_cannot_get_another_user_post_from_private_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->private()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        $response = $this->get(route('categories.posts.show', [$category, $post]));

        $response->assertForbidden();
    }

    public function test_guest_user_cannot_delete_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->private()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        $response = $this->deleteJson(route('categories.posts.destroy', [$category, $post]));

        $response->assertUnauthorized();
    }

    public function test_user_cannot_delete_another_user_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->for($user)->private()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        $response = $this->deleteJson(route('categories.posts.destroy', [$category, $post]));

        $response->assertForbidden();
    }

    public function test_user_can_delete_his_post()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $category = Category::factory()->for($user)->private()->create();
        $post = Post::factory()->for($user)->for($category)->create();

        $response = $this->deleteJson(route('categories.posts.destroy', [$category, $post]));

        $response->assertOk();
    }

    public function test_guest_user_cannot_update_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();
        $post = Post::factory()->for($category)->for($user)->create();

        $response = $this->putJson(route('categories.posts.update', [$post->category, $post]), [
            'word' => 'test',
            'meaning' => 'testing',
            'body' => 'test body',
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_can_update_his_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();
        $post = Post::factory()->for($category)->for($user)->create();
        Sanctum::actingAs($user);

        $data = [
            'word' => 'test',
            'meaning' => 'testing',
            'body' => 'test body',
        ];

        $response = $this->putJson(route('categories.posts.update', [$post->category, $post]), $data);

        $response->assertOk();
        $this->assertDatabaseHas('posts', [...$data, 'user_id' => $user->id]);
    }

    public function test_user_can_update_another_user_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create();
        $post = Post::factory()->for($category)->for($user)->create();
        Sanctum::actingAs(User::factory()->create());

        $data = [
            'word' => 'test',
            'meaning' => 'testing',
            'body' => 'test body',
        ];

        $response = $this->putJson(route('categories.posts.update', [$post->category, $post]), $data);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', $data);
    }
}
