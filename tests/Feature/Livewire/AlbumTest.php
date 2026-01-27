<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Album;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlbumTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertStatus(200);
    }

    public function test_renders_all_pages(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSet('totalPages', 5)
            ->assertSee('de 5');
    }

    public function test_pages_are_loaded_in_order(): void
    {
        $user = User::factory()->create();
        Page::factory()->create(['number' => 3, 'image_path' => 'pages/3.webp']);
        Page::factory()->create(['number' => 1, 'image_path' => 'pages/1.webp']);
        Page::factory()->create(['number' => 2, 'image_path' => 'pages/2.webp']);

        $component = Livewire::actingAs($user)->test(Album::class);

        $pages = $component->get('pages');

        $this->assertEquals(1, $pages[0]['number']);
        $this->assertEquals(2, $pages[1]['number']);
        $this->assertEquals(3, $pages[2]['number']);
    }

    public function test_shows_page_indicator(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(10)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSee('de 10');
    }

    public function test_can_navigate_to_next_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSet('currentPage', 0)
            ->call('nextPage')
            ->assertSet('currentPage', 1);
    }

    public function test_can_navigate_to_previous_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->set('currentPage', 2)
            ->call('previousPage')
            ->assertSet('currentPage', 1);
    }

    public function test_can_go_to_first_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->set('currentPage', 3)
            ->call('goToFirstPage')
            ->assertSet('currentPage', 0);
    }

    public function test_can_go_to_last_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->call('goToLastPage')
            ->assertSet('currentPage', 4);
    }

    public function test_cannot_navigate_before_first_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSet('currentPage', 0)
            ->call('previousPage')
            ->assertSet('currentPage', 0);
    }

    public function test_cannot_navigate_past_last_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->set('currentPage', 4)
            ->call('nextPage')
            ->assertSet('currentPage', 4);
    }

    public function test_can_go_to_specific_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->call('goToPage', 3)
            ->assertSet('currentPage', 3)
            ->assertDispatched('album-go-to-page', page: 3);
    }

    public function test_go_to_page_validates_bounds(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->call('goToPage', 10)
            ->assertSet('currentPage', 0)
            ->call('goToPage', -1)
            ->assertSet('currentPage', 0);
    }

    public function test_page_flipped_updates_current_page(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->call('pageFlipped', 2)
            ->assertSet('currentPage', 2);
    }

    public function test_shows_empty_album_state_when_no_pages(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSet('totalPages', 0)
            ->assertSee('Álbum vacío');
    }

    public function test_renders_navigation_buttons(): void
    {
        $user = User::factory()->create();
        Page::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(Album::class)
            ->assertSee('Primera')
            ->assertSee('Anterior')
            ->assertSee('Siguiente')
            ->assertSee('Última');
    }
}
