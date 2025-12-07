<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Models\Recipe;
use App\Models\User;
use App\Services\DeepL\DeepLCacheService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    use ApiResponse;

    protected DeepLCacheService $translator;

    public function __construct(DeepLCacheService $translator)
    {
        $this->translator = $translator;
    }

    public function show(Request $request, $id)
    {
        $language = strtoupper(trim($request->input('language', '')));

        $cuisines = Cuisine::all();
        $user = User::find($id);

        if (!$user) {
            return $this->error('User not found', 404);
        }

        // Loop through each cuisine and only include it if it has at least one matching recipe
        $cuisines_recipes = $cuisines->map(function ($cuisine) use ($user) {
            $recipes = Recipe::with(['user'])
                ->where('category', $cuisine->slug)
                ->where('user_id', $user->id)
                ->where('status', 'Active')
                ->get()
                ->map(function ($recipe) {
                    return [
                        'id'             => $recipe->id,
                        'image'          => $recipe->image ?? null,
                        'image_url'      => $recipe->image_url,
                        'title'          => $recipe->title,
                        'total_ready_time'    => $recipe->total_ready_time,
                        'average_rating'    => $recipe->average_rating,
                        'rating_count'    => $recipe->rating_count,
                        'is_favourite'    => $recipe->is_favourite,
                    ];
                });

            if ($recipes->isEmpty()) {
                return null; // skip this cuisine
            }

            return [
                'id'      => $cuisine->id,
                'name'    => $cuisine->name,
                'slug'    => $cuisine->slug,
                'recipes' => $recipes,
            ];
        })->filter()->values();

        // APPLY TRANSLATION for cuisine name and recipe titles
        if ($language && in_array($language, ['EN', 'DE'])) {

            $cuisines_recipes = $cuisines_recipes->map(function ($cuisine) use ($language) {

                // Translate cuisine name
                if (!empty($cuisine['name'])) {
                    $cuisine['name'] = $this->translator->translateText($cuisine['name'], $language);
                }

                // Translate each recipe title
                $cuisine['recipes'] = collect($cuisine['recipes'])->map(function ($recipe) use ($language) {
                    if (!empty($recipe['title'])) {
                        $recipe['title'] = $this->translator->translateText($recipe['title'], $language);
                    }
                    return $recipe;
                })->toArray();

                return $cuisine;
            });
        }

        $user->load('userLinks');

        $data = [
            'user' => $user,
            'cuisines_recipes' => $cuisines_recipes,
        ];

        return $this->success('User Retrieve Successfully!', $data, 200);
    }

}
