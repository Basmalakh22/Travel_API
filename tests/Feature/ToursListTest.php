<?php
namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_returns_paginated_data_correctly()
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tours_price_is_shown_correctly()
    {
        $travel = Travel::factory()->create();
        Tour::factory()->create(['travel_id' => $travel->id, 'price' => 45.00]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '45.00']);
    }

    public function test_tours_list_returns_pagination()
    {
        $travel = Travel::factory()->create();
        Tour::factory(16)->create(['travel_id' => $travel->id]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tours_list_sorts_by_starting_date_correctly()
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?sortBy=starting_date&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly()
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $response = $this->get('api/v1/travels/'.$travel->slug.'/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }

    public function test_tours_list_filters_by_price_correctly()
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 200]);
        $cheapTour = Tour::factory()->create(['travel_id' => $travel->id, 'price' => 100]);

        $endpoint = 'api/v1/travels/'.$travel->slug.'/tours';

        $response = $this->get($endpoint.'?priceFrom=50&priceTo=150');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);

        $response = $this->get($endpoint.'?priceFrom=150&priceTo=250');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
    }

    public function test_tours_list_filters_by_starting_date_correctly()
    {
        $travel = Travel::factory()->create();
        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(1),
        ]);

        $endpoint = 'api/v1/travels/'.$travel->slug.'/tours';

        $response = $this->get($endpoint.'?dateFrom='.now()->toDateString().'&dateTo='.now()->addDays(5)->toDateString());
        $response->assertJsonCount(2, 'data');
    }

    public function test_tour_list_returns_validation_errors()
    {
        $travel = Travel::factory()->create();

        $response = $this->getJson('api/v1/travels/'.$travel->slug.'/tours?dateFrom=abcde');
        $response->assertStatus(422);

        $response = $this->getJson('api/v1/travels/'.$travel->slug.'/tours?priceFrom=abcde');
        $response->assertStatus(422);
    }
}
