<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Sticker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StickerMapperController extends Controller
{
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
}
