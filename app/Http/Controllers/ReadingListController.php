<?php

namespace App\Http\Controllers;

use App\ReadingList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingListController extends Controller
{
    protected $name;
    protected $user_id;

    public function __construct(string $name = '', int $user_id = null)
    {
        $this->name     = $name;
        $this->user_id  = $user_id;
    }

    /**
     * @return JsonResponse
     */
    public function get() : JsonResponse
    {
        $readingLists = Auth::user()->readingLists;

        return response()->json(['readingLists' => $readingLists]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $data = [
            'name'    => $request->name,
            'user_id' => Auth::user()->id,
        ];

        $list = new ReadingList($data);

        if (!$list->validate($data)) {
            return response()->json($list->validationErrors());
        }

        $list->save();

        return response()->json("New list created");
    }
}
