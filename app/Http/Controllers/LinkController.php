<?php

namespace App\Http\Controllers;

use App\Link;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
            'url'             => $request->link,
            'reading_list_id' => $request->id,
        ];

        $link = new Link($data);

        if (!$link->validate($data)) {
            return response()->json($link->validationErrors(), 422);
        }

        try {
            $client = new Client();
            $url = 'https://api.linkpreview.net?key='.Config::get('linkpreview.key').'&q='.$data['url'];

            $response = $client->request('POST', $url);
            $metaData = json_decode($response->getBody());

            $link->title = $metaData->title;
            $link->description = $metaData->description;
            $link->image = $metaData->image;
        }
        catch (\Exception $e) {

            return response()->json('Link could not be saved', 422);
        }

        $link->save();

        return response()->json("Link added");
    }
}
