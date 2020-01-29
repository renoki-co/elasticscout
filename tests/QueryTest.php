<?php

namespace Rennokki\ElasticScout\Tests;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Rennokki\ElasticScout\Tests\Models\Book;
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

    public function test_where_equals()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWithPrice200 = Book::elasticsearch()->where('price', 200)->get();
        $booksWithPrice100 = Book::elasticsearch()->where('price', 100)->get();

        $this->assertEquals(
            1, $booksWithPrice200->count()
        );

        $this->assertTrue($booksWithPrice200->first()->is($twilight));

        $this->assertEquals(
            1, $booksWithPrice100->count()
        );

        $this->assertTrue($booksWithPrice100->first()->is($tfios));
    }

    public function test_where_not_equals()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWithPricesOtherThan200 = Book::elasticsearch()->where('price', '!=', 200)->get();
        $booksWithPricesOtherThan100 = Book::elasticsearch()->where('price', '<>', 100)->get();

        $this->assertEquals(
            1, $booksWithPricesOtherThan200->count()
        );

        $this->assertTrue($booksWithPricesOtherThan200->first()->is($tfios));

        $this->assertEquals(
            1, $booksWithPricesOtherThan100->count()
        );

        $this->assertTrue($booksWithPricesOtherThan100->first()->is($twilight));
    }

    public function test_where_ranges()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWithPricesGreaterThan150 = Book::elasticsearch()->where('price', '>', 150)->get();
        $booksWithPricesLessThan150 = Book::elasticsearch()->where('price', '<', 150)->get();
        $booksWithPricesGreaterThanOrEqualTo100 = Book::elasticsearch()->where('price', '>=', 100)->get();
        $booksWithPricesLessThanOrEqualTo200 = Book::elasticsearch()->where('price', '<=', 200)->get();

        $booksWithPricesGreaterThan200 = Book::elasticsearch()->where('price', '>', 200)->get();

        // Greater Than
        $this->assertEquals(
            1, $booksWithPricesGreaterThan150->count()
        );

        $this->assertTrue($booksWithPricesGreaterThan150->first()->is($twilight));

        // Less Than
        $this->assertEquals(
            1, $booksWithPricesLessThan150->count()
        );

        $this->assertTrue($booksWithPricesLessThan150->first()->is($tfios));

        // Greater Than or Equal To
        $this->assertEquals(
            2, $booksWithPricesGreaterThanOrEqualTo100->count()
        );

        // Less Than or Equal To
        $this->assertEquals(
            2, $booksWithPricesLessThanOrEqualTo200->count()
        );

        $this->assertEquals(
            0, $booksWithPricesGreaterThan200->count()
        );
    }

    public function test_where_in()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWherePricesAre100Or300 = Book::elasticsearch()->whereIn('price', [100, 300])->get();
        $booksWherePricesAre1000Or2000 = Book::elasticsearch()->whereIn('price', [1000, 2000])->get();

        $booksWherePricesAreNot100Or300 = Book::elasticsearch()->whereNotIn('price', [100, 300])->get();
        $booksWherePricesAreNot1000Or2000 = Book::elasticsearch()->whereNotIn('price', [1000, 2000])->get();

        // Where In
        $this->assertEquals(
            1, $booksWherePricesAre100Or300->count()
        );

        $this->assertTrue($booksWherePricesAre100Or300->first()->is($tfios));

        $this->assertEquals(
            0, $booksWherePricesAre1000Or2000->count()
        );

        // Where Not In
        $this->assertEquals(
            1, $booksWherePricesAreNot100Or300->count()
        );

        $this->assertTrue($booksWherePricesAreNot100Or300->first()->is($twilight));

        $this->assertEquals(
            2, $booksWherePricesAreNot1000Or2000->count()
        );
    }

    public function test_where_between()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWherePricesAreBetween150And300 = Book::elasticsearch()->whereBetween('price', [150, 300])->get();
        $booksWherePricesAreBetween1000And2000 = Book::elasticsearch()->whereBetween('price', [1000, 2000])->get();

        $booksWherePricesAreNotBetween100And300 = Book::elasticsearch()->whereNotBetween('price', [100, 300])->get();
        $booksWherePricesAreNotBetween1000And2000 = Book::elasticsearch()->whereNotBetween('price', [1000, 2000])->get();

        // Where Between
        $this->assertEquals(
            1, $booksWherePricesAreBetween150And300->count()
        );

        $this->assertTrue($booksWherePricesAreBetween150And300->first()->is($twilight));

        $this->assertEquals(
            0, $booksWherePricesAreBetween1000And2000->count()
        );

        // Where Not Between
        $this->assertEquals(
            0, $booksWherePricesAreNotBetween100And300->count()
        );

        $this->assertEquals(
            2, $booksWherePricesAreNotBetween1000And2000->count()
        );
    }

    public function test_where_exists()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksWithPrice = Book::elasticsearch()->whereExists('price')->get();
        $booksWithoutPrice = Book::elasticsearch()->whereNotExists('price')->get();

        $this->assertEquals(
            2, $booksWithPrice->count()
        );

        $this->assertEquals(
            0, $booksWithoutPrice->count()
        );
    }

    public function test_order_by()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $booksByExpensiveFirst = Book::elasticsearch()->orderBy('price', 'desc')->get();

        $this->assertTrue(
            $booksByExpensiveFirst->first()->is($twilight)
        );
    }

    public function test_scopes()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $query = Book::elasticsearch()->withPriceBelow(200);

        $this->assertInstanceOf(\Rennokki\ElasticScout\Builders\ElasticsearchBuilder::class, $query);

        $bookWithPriceBelow200 = $query->get();

        $this->assertEquals(1, $bookWithPriceBelow200->count());
        $this->assertTrue($bookWithPriceBelow200->first()->is($tfios));
    }

    public function test_unless()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $query = Book::elasticsearch()->unless(false, function ($query) {
            return $query->where('price', 200);
        });

        $this->assertInstanceOf(\Rennokki\ElasticScout\Builders\ElasticsearchBuilder::class, $query);

        $twilightBook = $query->get();

        $this->assertEquals(1, $twilightBook->count());
        $this->assertTrue($twilightBook->first()->is($twilight));
    }

    public function test_when()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $query = Book::elasticsearch()->when(true, function ($query) {
            return $query->where('price', 200);
        });

        $this->assertInstanceOf(\Rennokki\ElasticScout\Builders\ElasticsearchBuilder::class, $query);

        $twilightBook = $query->get();

        $this->assertEquals(1, $twilightBook->count());
        $this->assertTrue($twilightBook->first()->is($twilight));
    }

    public function test_dynamic_where()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $query = Book::elasticsearch()->wherePrice(200);

        $this->assertInstanceOf(\Rennokki\ElasticScout\Builders\ElasticsearchBuilder::class, $query);

        $twilightBook = $query->get();

        $this->assertEquals(1, $twilightBook->count());
        $this->assertTrue($twilightBook->first()->is($twilight));
    }

    public function test_dynamic_where_for_snake_case()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100, 'fan_votes' => 50]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200, 'fan_votes' => 0]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $twilight->save();
        $twilight->searchable();

        $query = Book::elasticsearch()->whereFanVotes(50);

        $this->assertInstanceOf(\Rennokki\ElasticScout\Builders\ElasticsearchBuilder::class, $query);

        $tfiosBook = $query->get();

        $this->assertEquals(1, $tfiosBook->count());
        $this->assertTrue($tfiosBook->first()->is($tfios));
    }

    public function test_append_to_body()
    {
        $payload =
            Book::elasticsearch()
                ->appendToBody('minimum_should_match', 1)
                ->getPayload()
                ->toArray();

        $this->assertEquals(
            1, Arr::get($payload[0], 'body.minimum_should_match')
        );
    }

    public function test_append_to_query()
    {
        $payload =
            Book::elasticsearch()
                ->appendToQuery('should', 'yes')
                ->getPayload()
                ->toArray();

        $this->assertEquals(
            'yes', Arr::get($payload[0], 'body.query.should')
        );
    }

    public function test_debugging()
    {
        $book = factory(Book::class)->make();
        $tfios = factory(Book::class)->make(['name' => 'The Fault In Our Stars', 'price' => 100]);
        $twilight = factory(Book::class)->make(['name' => 'Twilight', 'price' => 200]);

        $book->getIndex()->sync();

        $tfios->save();
        $tfios->searchable();

        $this->assertTrue(
            is_array(Book::elasticsearch()->explain())
        );

        $this->assertTrue(
            is_array(Book::elasticsearch()->profile())
        );

        $this->assertTrue(
            Book::elasticsearch()->getPayload() instanceof Collection
        );
    }
}
