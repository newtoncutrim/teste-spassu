<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Topic;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    /** @test Teste para criar um livro */
    public function test_it_creates_a_book_successfully()
    {
        $author = Author::factory()->create();
        $topic = Topic::factory()->create();

        $payload = [
            'title' => 'Clean Code',
            'publisher' => 'Prentice Hall',
            'edition' => '1',
            'year_of_publication' => '2008',
            'price' => '99.99',
            'authors' => [$author->id],
            'topics' => [$topic->id],
        ];

        $response = $this->postJson(route('books.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'title' => 'Clean Code',
                'publisher' => 'Prentice Hall',
            ]);
    }

    /** @test Teste para buscar um autor com seus livros e tópicos */
    public function test_it_fetches_author_with_books_and_topics()
    {
        $author = Author::factory()->create();
        $topics = Topic::factory()->count(2)->create();
        $book = Book::factory()->create();

        $book->authors()->attach($author);
        $book->topics()->attach($topics);

        $response = $this->getJson(route('authorswithbooks'));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'deleted_at',
                        'created_at',
                        'updated_at',
                        'books' => [
                            '*' => [
                                'id',
                                'title',
                                'publisher',
                                'edition',
                                'year_of_publication',
                                'price',
                                'deleted_at',
                                'created_at',
                                'updated_at',
                                'pivot' => [
                                    'author_id',
                                    'book_id',
                                ],
                                'topics' => [
                                    '*' => [
                                        'id',
                                        'description',
                                        'deleted_at',
                                        'created_at',
                                        'updated_at',
                                        'pivot' => [
                                            'book_id',
                                            'topic_id',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);


        $response->assertJsonFragment([
            'id' => $author->id,
            'name' => $author->name,
        ]);

        $response->assertJsonFragment([
            'title' => $book->title,
        ]);

        foreach ($topics as $topic) {
            $response->assertJsonFragment([
                'description' => $topic->description,
            ]);
        }
    }

    /** @test Teste para criar um livro com muitos autores e tópicos */
    public function test_it_creates_a_book_with_multiple_authors_and_topics()
    {
        $authors = Author::factory()->count(2)->create();
        $topics = Topic::factory()->count(3)->create();

        $payload = [
            'title' => 'Livro Teste Laravel',
            'publisher' => 'Editora Teste',
            'year_of_publication' => 2024,
            'price' => 150.50,
            'edition' => 1,
            'authors' => $authors->pluck('id')->toArray(),
            'topics' => $topics->pluck('id')->toArray(),
        ];

        $response = $this->postJson(route('books.store'), $payload);

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'title',
                'publisher',
                'edition',
                'year_of_publication',
                'price',
                'authors' => [
                    [
                        'id',
                        'name',
                    ]
                ],
                'topics' => [
                    [
                        'id',
                        'description',
                    ]
                ]
            ]
        ]);

        $this->assertDatabaseHas('books', ['title' => 'Livro Teste Laravel']);

        foreach ($authors as $author) {
            $this->assertDatabaseHas('book_author', [
                'author_id' => $author->id,
            ]);
        }

        foreach ($topics as $topic) {
            $this->assertDatabaseHas('book_topic', [
                'topic_id' => $topic->id,
            ]);
        }
    }

    /** @test Teste para atualizar um livro com muitos autores e tópicos */
    public function test_it_updates_a_book_with_multiple_authors_and_topics()
    {
        $book = Book::factory()->create();

        $oldAuthors = Author::factory()->count(2)->create();
        $oldTopics = Topic::factory()->count(2)->create();

        $book->authors()->attach($oldAuthors);
        $book->topics()->attach($oldTopics);

        $newAuthors = Author::factory()->count(2)->create();
        $newTopics = Topic::factory()->count(3)->create();

        $payload = [
            'title' => 'Livro Atualizado Laravel',
            'publisher' => 'Nova Editora',
            'year_of_publication' => 2025,
            'price' => 199.99,
            'edition' => 2,
            'authors' => $newAuthors->pluck('id')->toArray(),
            'topics' => $newTopics->pluck('id')->toArray(),
        ];

        $response = $this->putJson(route('books.update', ['book' => $book->id]), $payload);

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonFragment([
            'title' => 'Livro Atualizado Laravel',
            'publisher' => 'Nova Editora',
            'price' => 199.99,
            'edition' => 2,
        ]);

        $this->assertDatabaseHas('books', ['title' => 'Livro Atualizado Laravel']);

        foreach ($newAuthors as $author) {
            $this->assertDatabaseHas('book_author', [
                'author_id' => $author->id,
                'book_id' => $book->id,
            ]);
        }

        foreach ($newTopics as $topic) {
            $this->assertDatabaseHas('book_topic', [
                'topic_id' => $topic->id,
                'book_id' => $book->id,
            ]);
        }

        foreach ($oldAuthors as $author) {
            $this->assertDatabaseMissing('book_author', [
                'author_id' => $author->id,
                'book_id' => $book->id,
            ]);
        }

        foreach ($oldTopics as $topic) {
            $this->assertDatabaseMissing('book_topic', [
                'topic_id' => $topic->id,
                'book_id' => $book->id,
            ]);
        }
    }


    /** @test  Teste para criar um livro com dados inválidos */
    public function test_it_fails_to_create_book_with_invalid_data()
    {
        // Dados inválidos
        $response = $this->postJson(route('books.store'), [
            'title' => '',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['title', 'publisher', 'edition', 'year_of_publication', 'price', 'authors', 'topics']);
    }

    /** @test Teste para atualizar um livro */
    public function test_it_updates_a_book_successfully()
    {
        $authors = Author::factory()->count(2)->create();
        $topics = Topic::factory()->count(2)->create();

        $book = Book::factory()->create();
        $book->authors()->attach($authors);
        $book->topics()->attach($topics);

        $newAuthor = Author::factory()->create();

        $payload = [
            'title' => 'Refactoring',
            'publisher' => 'Addison-Wesley',
            'edition' => '2',
            'year_of_publication' => '2018',
            'price' => '150.00',
            'authors' => [$newAuthor->id],
            'topics' => $topics->pluck('id')->toArray(),
        ];

        $response = $this->putJson(route('books.update', ['book' => $book->id]), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'title' => 'Refactoring',
                'publisher' => 'Addison-Wesley',
            ]);
    }

    /** @test  Teste para atualizar um livro com um título existente */
    public function test_it_fails_to_update_book_with_existing_title()
    {
        $author = Author::factory()->create();
        $topic = Topic::factory()->create();

        $book1 = Book::factory()->create(['title' => 'Book One']);
        $book2 = Book::factory()->create(['title' => 'Book Two']);

        $book1->authors()->attach($author);
        $book1->topics()->attach($topic);

        $payload = [
            'title' => 'Book Two',
            'publisher' => 'Any Publisher',
            'edition' => '1',
            'year_of_publication' => '2022',
            'price' => '100.00',
            'authors' => [$author->id],
            'topics' => [$topic->id],
        ];

        $response = $this->putJson(route('books.update', ['book' => $book1->id]), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => 'Já existe um livro com esse nome.',
            ]);
    }

    /** @test  Teste para deletar um livro */
    public function test_it_deletes_a_book_successfully()
    {
        $author = Author::factory()->create();
        $topic = Topic::factory()->create();

        $book = Book::factory()->create();
        $book->authors()->attach($author);
        $book->topics()->attach($topic);

        $response = $this->deleteJson(route('books.destroy', ['book' => $book->id]));

        $this->assertSoftDeleted('books', [
            'id' => $book->id
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test Teste para deletar um livro inexistente */
    public function test_it_fails_to_delete_a_nonexistent_book()
    {
        $response = $this->deleteJson(route('books.destroy', ['book' => 999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
