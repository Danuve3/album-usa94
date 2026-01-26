<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PackPile;
use App\Models\Pack;
use App\Models\Sticker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackPileTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_can_be_rendered(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertStatus(200);
    }

    public function test_shows_unopened_pack_count(): void
    {
        $user = User::factory()->create();
        Pack::factory()->count(3)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('3')
            ->assertSee('sobres disponibles');
    }

    public function test_shows_singular_text_for_one_pack(): void
    {
        $user = User::factory()->create();
        Pack::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('1')
            ->assertSee('sobre disponible');
    }

    public function test_shows_message_when_no_packs_available(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('No tienes sobres disponibles')
            ->assertSee('Vuelve mañana para recibir más sobres');
    }

    public function test_does_not_count_opened_packs(): void
    {
        $user = User::factory()->create();
        Pack::factory()->count(2)->create(['user_id' => $user->id]);
        Pack::factory()->create(['user_id' => $user->id, 'opened_at' => now()]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('2')
            ->assertSee('sobres disponibles');
    }

    public function test_does_not_count_other_users_packs(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Pack::factory()->count(2)->create(['user_id' => $user->id]);
        Pack::factory()->count(5)->create(['user_id' => $otherUser->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('2')
            ->assertSee('sobres disponibles');
    }

    public function test_can_open_pack(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();
        Pack::factory()->count(3)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('3')
            ->call('openPack')
            ->assertSee('2')
            ->assertSee('¡Cromos obtenidos!');
    }

    public function test_open_pack_updates_count_in_real_time(): void
    {
        Sticker::factory()->count(100)->create();
        $user = User::factory()->create();
        Pack::factory()->count(2)->create(['user_id' => $user->id]);

        $component = Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('2');

        $component->call('openPack')
            ->assertSee('1');

        $component->call('openPack')
            ->assertSee('No tienes sobres disponibles');
    }

    public function test_open_pack_does_nothing_when_no_packs_available(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('No tienes sobres disponibles')
            ->call('openPack')
            ->assertSee('No tienes sobres disponibles')
            ->assertDontSee('¡Cromos obtenidos!');
    }

    public function test_shows_stickers_after_opening_pack(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();
        Pack::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->call('openPack')
            ->assertSee('¡Cromos obtenidos!')
            ->assertSee('Continuar');
    }

    public function test_can_clear_last_opened_stickers(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();
        Pack::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->call('openPack')
            ->assertSee('¡Cromos obtenidos!')
            ->call('clearLastOpened')
            ->assertDontSee('¡Cromos obtenidos!');
    }

    public function test_dispatches_event_when_pack_opened(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();
        Pack::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->call('openPack')
            ->assertDispatched('pack-opened');
    }

    public function test_opens_oldest_pack_first(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();

        $oldPack = Pack::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);
        $newPack = Pack::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->call('openPack');

        $this->assertNotNull($oldPack->fresh()->opened_at);
        $this->assertNull($newPack->fresh()->opened_at);
    }

    public function test_refresh_count_updates_from_database(): void
    {
        $user = User::factory()->create();
        Pack::factory()->count(2)->create(['user_id' => $user->id]);

        $component = Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('2');

        Pack::factory()->create(['user_id' => $user->id]);

        $component->call('refreshCount')
            ->assertSee('3');
    }

    public function test_component_renders_pack_pile_visual(): void
    {
        $user = User::factory()->create();
        Pack::factory()->count(3)->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(PackPile::class)
            ->assertSee('USA 94')
            ->assertSee('Abrir');
    }
}
