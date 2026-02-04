<?php

namespace Tests\Unit;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::clearCache();
    }

    public function test_can_set_and_get_string_setting(): void
    {
        Setting::set('app_name', 'Test App');

        $this->assertEquals('Test App', Setting::get('app_name'));
    }

    public function test_can_set_and_get_integer_setting(): void
    {
        Setting::set('stickers_per_pack', 5);

        $value = Setting::get('stickers_per_pack');

        $this->assertSame(5, $value);
        $this->assertIsInt($value);
    }

    public function test_can_set_and_get_float_setting(): void
    {
        Setting::set('shiny_probability', 0.05);

        $value = Setting::get('shiny_probability');

        $this->assertSame(0.05, $value);
        $this->assertIsFloat($value);
    }

    public function test_can_set_and_get_boolean_setting(): void
    {
        Setting::set('maintenance_mode', true);

        $value = Setting::get('maintenance_mode');

        $this->assertTrue($value);
        $this->assertIsBool($value);
    }

    public function test_can_set_and_get_array_setting(): void
    {
        Setting::set('allowed_formats', ['jpg', 'png', 'webp']);

        $value = Setting::get('allowed_formats');

        $this->assertEquals(['jpg', 'png', 'webp'], $value);
        $this->assertIsArray($value);
    }

    public function test_returns_default_value_when_setting_not_found(): void
    {
        $value = Setting::get('non_existent', 'default');

        $this->assertEquals('default', $value);
    }

    public function test_returns_null_when_setting_not_found_and_no_default(): void
    {
        $value = Setting::get('non_existent');

        $this->assertNull($value);
    }

    public function test_setting_is_cached(): void
    {
        Setting::set('cached_value', 'test');

        // First call should cache
        Setting::get('cached_value');

        $this->assertTrue(Cache::has('settings'));
    }

    public function test_cache_is_cleared_on_set(): void
    {
        Setting::set('test_key', 'value1');
        Setting::get('test_key'); // This caches

        Setting::set('test_key', 'value2');

        $this->assertEquals('value2', Setting::get('test_key'));
    }

    public function test_can_update_existing_setting(): void
    {
        Setting::set('stickers_per_pack', 5);
        Setting::set('stickers_per_pack', 10);

        $this->assertSame(10, Setting::get('stickers_per_pack'));
        $this->assertDatabaseCount('settings', 1);
    }
}
