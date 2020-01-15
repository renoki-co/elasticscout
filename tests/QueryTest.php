<?php

namespace Rennokki\ElasticScout\Tests;

use Rennokki\ElasticScout\Tests\Models\Book;
use Rennokki\ElasticScout\Tests\Models\Post;
use Rennokki\ElasticScout\Tests\Models\Restaurant;

class QueryTest extends TestCase
{
    public function test_search_book_by_name()
    {
        $book = factory(Book::class)->make(['name' => 'Sun Tzu: Art of War']);
        $book->getIndex()->sync();
        $book->save();
        $book->searchable();

        $searchResult = Book::search($book->name)->first();

        $this->assertTrue($searchResult->is($book));
    }

    public function test_search_book_by_partial_starting_name_leads_to_no_results()
    {
        $book = factory(Book::class)->make(['name' => 'Rumpelstiltskin']);
        $book->getIndex()->sync();
        $book->save();
        $book->searchable();

        $this->assertNull(
            Book::search('Rumpelstil')->first()
        );
    }

    public function test_search_restaurant_by_partial_name_leads_to_results_due_to_ngram_analyzer()
    {
        $restaurant = factory(Restaurant::class)->make(['name' => 'Dominos']);
        $restaurant->getIndex()->sync();
        $restaurant->save();
        $restaurant->searchable();

        $searchResult = Restaurant::search('Domin')->first();

        $this->assertTrue($searchResult->is($restaurant));
    }
}
