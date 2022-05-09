<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $id = request('id');
        $title = request('title');

        $foodsQueryBuilder = Food::query();

        if ($id) {
            $foodsQueryBuilder->where('id', '=', $id);
        }

        if ($title) {
            $foodsQueryBuilder->where('title', 'LIKE', '%' . $title . '%');
        }

        return response()->json(['data' => $foodsQueryBuilder->get()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'max:50'],
            'size' => ['required'],
            'clr' => ['required'],
            'carb' => ['required'],
            'protein' => ['required'],
            'fat' => ['required'],
            'insaturatedFat' => ['required'],
            'sugar' => ['required'],
        ]);

        $food = Food::query()
            ->create([
                'title' => $request->get('title'),
                'size' => $request->get('size'),
                'clr' => $request->get('clr'),
                'carb' => $request->get('carb'),
                'protein' => $request->get('protein'),
                'fat' => $request->get('fat'),
                'insaturatedFat' => $request->get('insaturatedFat'),
                'sugar' => $request->get('sugar'),
            ])->save();

        $this->updateLastTimeDataUpdated();
        return response()->json([
            'data' => $food,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Food $food
     * @return JsonResponse
     */
    public function show(Food $food): JsonResponse
    {
        return response()->json([
            'data' => $food
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Food $food
     * @return Response
     */
    public function update(Request $request, Food $food)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Food $food
     * @return JsonResponse
     */
    public function destroy(Food $food): JsonResponse
    {
        $this->updateLastTimeDataUpdated();
        return response()->json([
            'deleted' => $food->delete()
        ]);
    }


    /**
     * Insert initial data to database
     */
    public function initialize(): JsonResponse
    {
        // get the passed json tree
        $is_saved = false;
        if (Food::all()->count() == 0) {
            $foods = json_decode(file_get_contents(storage_path() . "/foods.json"), true);
            foreach ($foods as $object) {
                Food::query()
                    ->create([
                        'title' => $object['title'],
                        'size' => $object['size'],
                        'clr' => $object['clr'],
                        'carb' => $object['carb'],
                        'protein' => $object['protein'],
                        'fat' => $object['fat'],
                        'insaturatedFat' => $object['insaturatedFat'],
                        'sugar' => $object['sugar'],
                    ])->save();
            }
            $is_saved = true;
            $this->updateLastTimeDataUpdated(); // update date which data were updated
        }

        return response()->json([
            'saved' => $is_saved,
            'data' => Food::all(),
        ]);
    }

    public function isUpdated(Request $request): JsonResponse
    {

        // Read Log File
        $jsonLog = file_get_contents(storage_path() . '/log.json');
        $logData = json_decode($jsonLog, true);


        $user_last_update_date = Carbon::parse($request->get('update_date'));
        $data_last_update_date = Carbon::parse($logData['last_updated']);

        $isUpdated = $data_last_update_date->gte($user_last_update_date);

        return response()->json([
            'is_updated' => $isUpdated
        ]);
    }

    private function updateLastTimeDataUpdated()
    {
        // Read last update date
        $jsonLog = file_get_contents(storage_path() . '/log.json');
        $logData = json_decode($jsonLog, true);

        // Update Value
        $logData['last_updated'] = now()->setTimezone('GMT+3')->format('Y-m-d h:i:s');

        // Write File
        $newJsonString = json_encode($logData, JSON_PRETTY_PRINT);
        file_put_contents(storage_path() . '/log.json', $newJsonString);
    }
}
