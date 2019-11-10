<?php

namespace App\Http\Controllers;

use App\Link;
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
     * @param  ReadingList  $readingList
     * @param  Request      $request
     * @return JsonResponse
     */
    public function edit(ReadingList $readingList, Request $request) : JsonResponse
    {
        $user = Auth::user();

        $data = [
            'name'     => $request->name,
            'user_id'  => $user->id,
        ];

        if (!$readingList->validate($data)) {
            return response()->json($readingList->validationErrors(), 422);
        }

        $readingList->update($data);

        return response()->json("List name updated");
    }

    /**
     * @param Request $request
     */
    public function reorderList(Request $request)
    {
        $ids = $request->toArray();
        (new ReadingList())->reorderLists($ids);
    }

    /**
     * @param Request $request
     */
    public function reorderMultipleLists(Request $request)
    {
        $i = 1;
        $list_id = $request->id;

        foreach ($request->links as $link) {
            Link::where('id', '=', $link['id'])->update([
                'position'        => $i,
                'reading_list_id' => $list_id,
            ]);
            $i++;
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $user = Auth::user();

        $data = [
            'name'     => $request->name,
            'user_id'  => Auth::user()->id,
            'position' => (new ReadingList())->getNewReadingListPosition($user),
        ];

        $list = new ReadingList($data);

        if (!$list->validate($data)) {
            return response()->json($list->validationErrors(), 422);
        }

        $list->save();

        return response()->json("New list created");
    }
}
