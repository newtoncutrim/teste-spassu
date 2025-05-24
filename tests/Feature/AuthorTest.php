<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthorTest extends TestCase
{
    use DatabaseMigrations;

    /** @test Testa se é possível listar os autores cadastrados */
    public function test_it_can_list_authors()
    {
        Author::factory()->count(3)->create();

        $response = $this->getJson(route('authors.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at']
                ]
            ]);
    }

    /** @test Testa se é possível criar um autor */
    public function test_it_can_create_an_author()
    {
        $data = ['name' => 'New Author'];

        $response = $this->postJson(route('authors.store'), $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Author'
            ]);

        $this->assertDatabaseHas('authors', $data);
    }

    /** @test Testa se é possível visualizar os dados de um autor específico */
    public function test_it_can_show_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->getJson(route('authors.show', $author->id));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $author->id,
                'name' => $author->name,
            ]);
    }

    /** @test Testa se é possível atualizar os dados de um autor especifico */
    public function test_it_can_update_an_author()
    {
        $author = Author::factory()->create();

        $data = ['name' => 'Updated Author'];

        $response = $this->putJson(route('authors.update', $author->id), $data);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Author'
            ]);

        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'name' => 'Updated Author'
        ]);
    }

    /** @test Testa se é possível excluir (soft delete) um autor */
    public function test_it_can_delete_an_author()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson(route('authors.destroy', $author->id));

        $response->assertStatus(200);

        $this->assertSoftDeleted('authors', [
            'id' => $author->id
        ]);
    }

    /** @test Testa se a criação de um autor falha quando os dados são inválidos */
    public function test_it_fails_to_create_author_with_invalid_data()
    {
        // Nome vazio (obrigatório)
        $response = $this->postJson(route('authors.store'), ['name' => '']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Nome muito curto (mínimo de caracteres)
        $response = $this->postJson(route('authors.store'), ['name' => 'ab']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Nome muito longo (máximo de caracteres)
        $longName = str_repeat('a', 251);
        $response = $this->postJson(route('authors.store'), ['name' => $longName]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test Testa se a atualização de um autor falha quando os dados são inválidos */
    public function test_it_fails_to_update_author_with_invalid_data()
    {
        $author = Author::factory()->create();

        // Nome vazio
        $response = $this->putJson(route('authors.update', $author->id), ['name' => '']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Nome muito curto
        $response = $this->putJson(route('authors.update', $author->id), ['name' => 'ab']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Nome muito longo
        $longName = str_repeat('a', 251);
        $response = $this->putJson(route('authors.update', $author->id), ['name' => $longName]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // Nome duplicado
        $otherAuthor = Author::factory()->create(['name' => 'Author Unique']);
        $response = $this->putJson(route('authors.update', $author->id), ['name' => 'Author Unique']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /** @test Testa se a criação falha ao tentar criar um autor com nome duplicado */
    public function test_it_fails_to_create_author_with_duplicate_name()
    {
        Author::factory()->create(['name' => 'Autor Duplicado']);

        $response = $this->postJson(route('authors.store'), ['name' => 'Autor Duplicado']);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors('name');
    }

    /** @test Testa se retorna 404 ao tentar atualizar um autor inexistente */
    public function test_it_returns_not_found_when_updating_nonexistent_author()
    {
        $nonExistentId = 9999;

        $data = ['name' => 'Nome Atualizado'];

        $response = $this->putJson(route('authors.update', $nonExistentId), $data);

        $response->assertStatus(404);
    }

    /** @test Testa se retorna 404 ao tentar excluir um autor inexistente */
    public function test_it_returns_not_found_when_deleting_nonexistent_author()
    {
        $nonExistentId = 9999;

        $response = $this->deleteJson(route('authors.destroy', $nonExistentId));

        $response->assertStatus(404);
    }

    /** @test  Testa se a exclusão de um autor falha quando ele possui livros cadastrados */
    public function test_it_fails_to_delete_author_with_books()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create();

        $author->books()->attach($book);

        $response = $this->deleteJson(route('authors.destroy', $author->id));

        $response->assertStatus(400)
            ->assertJsonFragment([
                'success' => false,
                'message' => 'Não é possível excluir um autor que possui livros cadastrados.',
            ]);

        $this->assertDatabaseHas('authors', ['id' => $author->id]);
    }
}
