<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TvPlayerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'live');
        $selectedCategory = $request->get('category');
        $selectedSubcategory = $request->get('subcategory');
        $selectedChannelId = $request->get('channel');

        $baseQuery = Channel::query()
            ->where('is_active', true)
            ->where('type', $type);

        $categories = (clone $baseQuery)
            ->select('category_name')
            ->whereNotNull('category_name')
            ->distinct()
            ->orderBy('category_name')
            ->pluck('category_name');

        if (!$selectedCategory && $categories->count()) {
            $selectedCategory = $categories->first();
        }

        $subcategories = collect();

        if ($selectedCategory) {
            $subcategories = (clone $baseQuery)
                ->where('category_name', $selectedCategory)
                ->select('subcategory_name')
                ->whereNotNull('subcategory_name')
                ->distinct()
                ->orderBy('subcategory_name')
                ->pluck('subcategory_name');
        }

        if (!$selectedSubcategory && $subcategories->count()) {
            $selectedSubcategory = $subcategories->first();
        }

        $channelsQuery = (clone $baseQuery);

        if ($selectedCategory) {
            $channelsQuery->where('category_name', $selectedCategory);
        }

        if ($selectedSubcategory) {
            $channelsQuery->where('subcategory_name', $selectedSubcategory);
        }

        $channels = $channelsQuery
            ->orderByRaw('channel_number IS NULL')
            ->orderBy('channel_number')
            ->orderBy('name')
            ->get();

        $selectedChannel = null;

        if ($selectedChannelId) {
            $selectedChannel = Channel::with(['epgProgrammes' => function ($query) {
                $query->where('end_at', '>=', now()->subHours(2))
                    ->orderBy('start_at')
                    ->limit(20);
            }])->find($selectedChannelId);
        }

        if (!$selectedChannel && $channels->count()) {
            $selectedChannel = Channel::with(['epgProgrammes' => function ($query) {
                $query->where('end_at', '>=', now()->subHours(2))
                    ->orderBy('start_at')
                    ->limit(20);
            }])->find($channels->first()->id);
        }

        return view('customer.tv.index', [
            'type' => $type,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'subcategories' => $subcategories,
            'selectedSubcategory' => $selectedSubcategory,
            'channels' => $channels,
            'selectedChannel' => $selectedChannel,
            'now' => Carbon::now(),
        ]);
    }
}
