<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Sticker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StickerAssignerController extends Controller
{
    public function index(Request $request)
    {
        $pages = Page::ordered()->get();
        $selectedPageNumber = $request->get('page', $pages->first()?->number);
        $selectedPage = $selectedPageNumber ? Page::where('number', $selectedPageNumber)->first() : null;

        $assignedStickers = Sticker::assignedToPage($selectedPageNumber)
            ->orderBy('number')
            ->get();

        $unassignedStickers = Sticker::unassigned()
            ->orderBy('number')
            ->get();

        return view('vendor.backpack.crud.sticker_assigner', [
            'pages' => $pages,
            'selectedPageNumber' => $selectedPageNumber,
            'selectedPage' => $selectedPage,
            'assignedStickers' => $assignedStickers,
            'unassignedStickers' => $unassignedStickers,
        ]);
    }

    public function assignStickers(Request $request): JsonResponse
    {
        $request->validate([
            'sticker_ids' => 'required|array',
            'sticker_ids.*' => 'exists:stickers,id',
            'page_number' => 'required|exists:pages,number',
        ]);

        Sticker::whereIn('id', $request->sticker_ids)
            ->update(['page_number' => $request->page_number]);

        return response()->json([
            'success' => true,
            'message' => count($request->sticker_ids) . ' cromo(s) asignado(s) a la página ' . $request->page_number,
        ]);
    }

    public function unassignStickers(Request $request): JsonResponse
    {
        $request->validate([
            'sticker_ids' => 'required|array',
            'sticker_ids.*' => 'exists:stickers,id',
        ]);

        Sticker::whereIn('id', $request->sticker_ids)
            ->update(['page_number' => null]);

        return response()->json([
            'success' => true,
            'message' => count($request->sticker_ids) . ' cromo(s) desasignado(s)',
        ]);
    }
}
