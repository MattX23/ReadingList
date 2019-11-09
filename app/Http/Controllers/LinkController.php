<?php

namespace App\Http\Controllers;

use App\Link;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(Request $request) : JsonResponse
    {
        $data = [
            'url'             => $request->name,
            'reading_list_id' => $request->id,
        ];

        $link = new Link($data);

        if (!$link->validate($data)) return response()->json($link->validationErrors(), 422);

        if (!$link->getPreview($link, $data['url'])) return response()->json('Link could not be saved', 422);;

        $link->save();

        return response()->json("Link added");
    }

    /**
     * @param Link $link
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function move(Link $link, Request $request) : JsonResponse
    {
        $newList = $request->newListId;

        $link->update([
           'reading_list_id' => $newList,
        ]);

        return response()->json("Link moved");
    }

    /**
     * @param Link $link
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(Link $link) : JsonResponse
    {
        $link->delete();

        return response()->json("Link deleted");
    }
}
