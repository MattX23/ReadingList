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
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(Request $request) : JsonResponse
    {
        $data = [
            'url'             => $request->name,
            'reading_list_id' => $request->id,
            'position' => (new Link())->getNewLinkPosition($request->id),
        ];

        $link = new Link($data);

        if (!$link->validate($data)) return response()->json($link->validationErrors(), 422);

        if (!$link->getPreview($link, $data['url'])) {

            $link->generateDefaultMetaData($link, $data['url']);
        }

        $link->save();

        return response()->json("Link added");
    }

    /**
     * @param Request $request
     */
    public function reorderLinks(Request $request)
    {
        $ids = $request->toArray();
        (new Link())->reorderLinks($ids);
    }

    /**
     * @param Link $link
     * @param Request $request
     * @throws Exception
     */
    public function move(Link $link, Request $request)
    {
        $oldList = $link->readingList->id;

        $link->update([
            'reading_list_id' => $request->newListId,
        ]);

        (new Link())->redefinePositions($link, $oldList);
    }

    /**
     * @param Link $link
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(Link $link) : JsonResponse
    {
        $link->delete();

        return response()->json("Link deleted");
    }
}
