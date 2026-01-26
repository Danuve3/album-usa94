<?php

namespace Tests\Feature;

use App\Enums\StickerRarity;
use App\Models\Pack;
use App\Models\Setting;
use App\Models\Sticker;
use App\Models\User;
use App\Models\UserSticker;
use App\Services\PackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackServiceTest extends TestCase
{
    use RefreshDatabase;

    private PackService $packService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->packService = new PackService;
    }

    public function test_open_generates_five_stickers(): void
    {
        Sticker::factory()->count(10)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $userStickers = $this->packService->open($pack);

        $this->assertCount(Pack::STICKERS_PER_PACK, $userStickers);
        $this->assertDatabaseCount('user_stickers', 5);
    }

    public function test_open_marks_pack_as_opened(): void
    {
        Sticker::factory()->count(5)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $this->assertNull($pack->opened_at);

        $this->packService->open($pack);

        $pack->refresh();
        $this->assertNotNull($pack->opened_at);
    }

    public function test_open_creates_user_sticker_records(): void
    {
        Sticker::factory()->count(5)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $userStickers = $this->packService->open($pack);

        foreach ($userStickers as $userSticker) {
            $this->assertInstanceOf(UserSticker::class, $userSticker);
            $this->assertEquals($user->id, $userSticker->user_id);
            $this->assertFalse($userSticker->is_glued);
            $this->assertNotNull($userSticker->obtained_at);
        }
    }

    public function test_open_throws_exception_if_pack_already_opened(): void
    {
        Sticker::factory()->count(5)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create([
            'user_id' => $user->id,
            'opened_at' => now(),
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Pack is already opened.');

        $this->packService->open($pack);
    }

    public function test_open_throws_exception_if_no_stickers_available(): void
    {
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No stickers available in the database.');

        $this->packService->open($pack);
    }

    public function test_open_respects_shiny_probability(): void
    {
        Sticker::factory()->count(10)->create(['rarity' => StickerRarity::Common]);
        Sticker::factory()->count(10)->shiny()->create();

        Setting::set('shiny_probability', 100);

        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $userStickers = $this->packService->open($pack);

        $shinyCount = $userStickers->filter(function ($userSticker) {
            return $userSticker->sticker->rarity === StickerRarity::Shiny;
        })->count();

        $this->assertEquals(Pack::STICKERS_PER_PACK, $shinyCount);
    }

    public function test_open_uses_default_shiny_probability_when_not_configured(): void
    {
        Sticker::factory()->count(50)->create(['rarity' => StickerRarity::Common]);
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $userStickers = $this->packService->open($pack);

        $this->assertCount(Pack::STICKERS_PER_PACK, $userStickers);
    }

    public function test_open_is_atomic_transaction(): void
    {
        Sticker::factory()->count(5)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $this->packService->open($pack);

        $pack->refresh();
        $this->assertNotNull($pack->opened_at);
        $this->assertDatabaseCount('user_stickers', 5);
    }

    public function test_open_returns_collection_of_user_stickers(): void
    {
        Sticker::factory()->count(5)->create();
        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $result = $this->packService->open($pack);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(Pack::STICKERS_PER_PACK, $result);
        $this->assertContainsOnlyInstancesOf(UserSticker::class, $result);
    }

    public function test_open_falls_back_to_any_sticker_when_no_shiny_available(): void
    {
        Sticker::factory()->count(10)->create(['rarity' => StickerRarity::Common]);

        Setting::set('shiny_probability', 100);

        $user = User::factory()->create();
        $pack = Pack::factory()->create(['user_id' => $user->id]);

        $userStickers = $this->packService->open($pack);

        $this->assertCount(Pack::STICKERS_PER_PACK, $userStickers);
    }
}
