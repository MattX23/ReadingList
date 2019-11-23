<?php

namespace App\Http\Controllers;

use App\Link;
use App\ReadingList;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    /**
     * @param Link $link
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function archive(Link $link): JsonResponse
    {
        $link->delete();

        return response()->json("Link archived");
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $link = Link::withTrashed()->find($id);

        $link->forceDelete();

        return response()->json("Link permanently deleted");
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    protected function getArchives(): JsonResponse
    {
        $links = Link::onlyTrashed()->orderByDesc('deleted_at')->get();

        return response()->json($links);
    }

    /**
     * @return ReadingList|null
     */
    protected function getRestoredList(): ?ReadingList
    {
        return ReadingList::where('user_id', '=', Auth::user()->id)
            ->where('name', '=', ReadingList::RESTORED_LIST)
            ->first();
    }

    /**
     * @param Link $link
     * @param Request $request
     * @throws Exception
     */
    protected function move(Link $link, Request $request)
    {
        $oldList = $link->readingList->id;

        $link->update([
            'reading_list_id' => $request->newListId,
        ]);

        (new Link())->redefinePositions($link, $oldList);
    }

    /**
     * @param Link $link
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \ReflectionException
     */
    protected function rename(Link $link, Request $request): JsonResponse
    {
        $link->title = $request->name;
        $data = $link->toArray();

        if (!$link->validate($data)) return response()->json($link->validationErrors(), 422);

        $link->update([
            'title' => $data['title'],
        ]);

        return response()->json("Link title updated");
    }

    /**
     * @param Request $request
     */
    protected function reorderLinks(Request $request)
    {
        $ids = $request->toArray();
        (new Link())->reorderLinks($ids);
    }

    /**
     * @param  int           $id
     * @return JsonResponse
     */
    protected function restore(int $id): JsonResponse
    {
        $link = Link::withTrashed()->find($id);

        $readingListIds = (new ReadingList())->getReadingListIds();

        if (!in_array($link->reading_list_id, $readingListIds)) {
            $restoredList = $this->getRestoredList();

            if ($restoredList) (new ReadingList())->updateReadingList($link, $restoredList->id);

            if (!$restoredList) {
                $readingList = (new ReadingList())->createRestoredLinksList();

                (new ReadingList())->updateReadingList($link, $readingList->id);
            }
        }

        $link->restore();

        return response()->json("Link restored");
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \ReflectionException
     */
    protected function store(Request $request): JsonResponse
    {
        $data = [
            'url'             => $request->name,
            'reading_list_id' => $request->id,
            'position'        => (new Link())->getNewLinkPosition($request->id),
            'title'           => $request->name,
        ];

        $link = new Link($data);

        if (!$link->validate($data)) return response()->json($link->validationErrors(), 422);

        if (!$link->getPreview($link, $data['url'])) $link->generateDefaultMetaData($link, $data['url']);

        $link->save();

        return response()->json("Link added");
    }
}
