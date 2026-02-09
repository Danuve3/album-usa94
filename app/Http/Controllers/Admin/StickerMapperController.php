<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Sticker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StickerMapperController extends Controller
{
    private const DEFAULT_BACK_NUMBER_CONFIG = [
        'enabled' => false,
        'position_x' => 15,
        'position_y' => 85,
        'font_size' => 12,
        'font_weight' => 'bold',
        'font_family' => 'Arial, sans-serif',
        'color' => '#000000',
    ];

    /**
     * Display the sticker mapper tool.
     */
    public function index(Request $request): View
    {
        $pages = Page::ordered()->get();
        $selectedPage = null;
        $stickers = collect();

        if ($request->has('page')) {
            $selectedPage = Page::where('number', $request->page)->first();
            if ($selectedPage) {
                $stickers = Sticker::where('page_number', $selectedPage->number)
                    ->orderBy('number')
                    ->get();
            }
        }

        return view('vendor.backpack.crud.sticker_mapper', [
            'pages' => $pages,
            'selectedPage' => $selectedPage,
            'stickers' => $stickers,
        ]);
    }

    /**
     * Update sticker position and dimensions.
     */
    public function updatePosition(Request $request, Sticker $sticker): JsonResponse
    {
        $validated = $request->validate([
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
            'width' => 'sometimes|integer|min:1',
            'height' => 'sometimes|integer|min:1',
            'is_horizontal' => 'sometimes|boolean',
        ]);

        $sticker->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Posición actualizada correctamente',
            'sticker' => $sticker->fresh(),
        ]);
    }

    /**
     * Update sticker dimensions.
     */
    public function updateDimensions(Request $request, Sticker $sticker): JsonResponse
    {
        $validated = $request->validate([
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $sticker->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Dimensiones actualizadas correctamente',
            'sticker' => $sticker->fresh(),
        ]);
    }

    /**
     * Toggle sticker horizontal orientation.
     */
    public function toggleHorizontal(Sticker $sticker): JsonResponse
    {
        $sticker->update([
            'is_horizontal' => ! $sticker->is_horizontal,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Orientación actualizada correctamente',
            'is_horizontal' => $sticker->is_horizontal,
        ]);
    }

    /**
     * Display the back number configuration page.
     */
    public function backNumberConfig(): View
    {
        $vertical = Setting::get('sticker_back_number_vertical', self::DEFAULT_BACK_NUMBER_CONFIG);
        $horizontal = Setting::get('sticker_back_number_horizontal', self::DEFAULT_BACK_NUMBER_CONFIG);

        if (! is_array($vertical)) {
            $vertical = self::DEFAULT_BACK_NUMBER_CONFIG;
        }
        if (! is_array($horizontal)) {
            $horizontal = self::DEFAULT_BACK_NUMBER_CONFIG;
        }

        return view('vendor.backpack.crud.sticker_back_number_config', [
            'vertical' => array_merge(self::DEFAULT_BACK_NUMBER_CONFIG, $vertical),
            'horizontal' => array_merge(self::DEFAULT_BACK_NUMBER_CONFIG, $horizontal),
        ]);
    }

    /**
     * Save the back number configuration.
     */
    public function saveBackNumberConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orientation' => 'required|in:vertical,horizontal',
            'enabled' => 'required|boolean',
            'position_x' => 'required|numeric|min:0|max:100',
            'position_y' => 'required|numeric|min:0|max:100',
            'font_size' => 'required|numeric|min:1|max:50',
            'font_weight' => 'required|in:normal,500,600,bold,800,900',
            'font_family' => 'required|string|max:100',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $key = 'sticker_back_number_'.$validated['orientation'];

        $config = [
            'enabled' => $validated['enabled'],
            'position_x' => $validated['position_x'],
            'position_y' => $validated['position_y'],
            'font_size' => $validated['font_size'],
            'font_weight' => $validated['font_weight'],
            'font_family' => $validated['font_family'],
            'color' => $validated['color'],
        ];

        Setting::set($key, $config, 'json');

        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente',
        ]);
    }
}
