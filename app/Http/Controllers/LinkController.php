<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $data = [
            'url'             => $request->link,
            'reading_list_id' => $request->id,
        ];

        $link = new Link($data);

        if (!$link->validate($data)) {
            return response()->json($link->validationErrors(), 422);
        }

        $link->save();

        return response()->json("Link added");
    }
}
