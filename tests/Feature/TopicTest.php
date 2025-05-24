<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Topic;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    /** @test Valida que não é possível criar um tópico sem descrição */
    public function test_fail_to_create_topic_without_description()
    {
        $response = $this->postJson(route('topics.store'), [
            'description' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test Valida que não é possível criar um tópico com descrição maior que 20 caracteres */
    public function test_fail_to_create_topic_with_description_too_long()
    {
        $response = $this->postJson(route('topics.store'), [
            'description' => str_repeat('a', 21)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test Valida que não é possível atualizar um tópico com descrição vazia */
    public function test_fail_to_update_topic_without_description()
    {
        $topic = Topic::factory()->create();

        $response = $this->putJson(route('topics.update', $topic->id), [
            'description' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test Valida que não é possível atualizar um tópico com descrição maior que 20 caracteres */
    public function test_fail_to_update_topic_with_description_too_long()
    {
        $topic = Topic::factory()->create();

        $response = $this->putJson(route('topics.update', $topic->id), [
            'description' => str_repeat('a', 21)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('description');
    }

    /** @test Valida que é possível criar um tópico com dados válidos */
    public function test_can_create_topic_with_valid_data()
    {
        $response = $this->postJson(route('topics.store'), [
            'description' => 'Topico válido'
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'description' => 'Topico válido'
            ]);

        $this->assertDatabaseHas('topics', [
            'description' => 'Topico válido'
        ]);
    }

    /** @test Valida que é possível atualizar um tópico com dados válidos */
    public function test_can_update_topic_with_valid_data()
    {
        $topic = Topic::factory()->create([
            'description' => 'Antigo'
        ]);

        $response = $this->putJson(route('topics.update', $topic->id), [
            'description' => 'Novo'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'description' => 'Novo'
            ]);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'description' => 'Novo'
        ]);
    }

    /** @test Valida que é possível deletar um tópico */
    public function test_can_delete_topic()
    {
        $topic = Topic::factory()->create();

        $response = $this->deleteJson(route('topics.destroy', $topic->id));

        $response->assertStatus(200);

        $this->assertSoftDeleted('topics', [
            'id' => $topic->id
        ]);
    }

    /** @test  Valida que é impossível excluir um tópico que possui livros cadastrados */
    public function test_it_fails_to_delete_topic_with_books()
    {
        $topic = Topic::factory()->create();
        $book = Book::factory()->create();
        $book->topics()->attach($topic);

        $response = $this->deleteJson(route('topics.destroy', $topic->id));

        $response->assertStatus(400)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Não é possível excluir um tópico que possui livros cadastrados.'
            ]);

        $this->assertDatabaseHas('topics', ['id' => $topic->id]);
    }
}
