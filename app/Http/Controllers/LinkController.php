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
    const ARCHIVED_SUCCESS_MESSAGE = 'Link archived';
    const DELETED_SUCCESS_MESSAGE = 'Link permanently deleted';

    /**
     * @param Link $link
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function archive(Link $link): JsonResponse
    {
        $this->authorize('archive', $link);

        $link->delete();

        return response()->json(self::ARCHIVED_SUCCESS_MESSAGE);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id): JsonResponse
    {
        $link = Link::withTrashed()->find($id);

        $this->authorize('forceDelete', $link);

        $link->forceDelete();

        return response()->json(self::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    protected function getArchives(): JsonResponse
    {
        $links = Link::onlyTrashed()->orderByDesc('deleted_at')->get();

        if (count($links) > 0) $this->authorize('viewArchives', $links->first());

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
    public function move(Link $link, Request $request)
    {
        $oldList = $link->readingList->id;

        $this->authorize('move', $link);

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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function rename(Link $link, Request $request): JsonResponse
    {
        $this->authorize('rename', $link);

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
     * @param int $id
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function restore(int $id): JsonResponse
    {
        $link = Link::withTrashed()->find($id);

        $this->authorize('restore', $link);

        $readingListIds = (new ReadingList())->getReadingListIds();

        if (!in_array($link->reading_list_id, $readingListIds)) {
            $restoredList = $this->getRestoredList();

            if ($restoredList) (new Link())->updateReadingList($link, $restoredList->id);

            if (!$restoredList) {
                $readingList = (new ReadingList())->createRestoredLinksList();

                (new Link())->updateReadingList($link, $readingList->id);
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
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function store(Request $request): JsonResponse
    {
        $this->authorize('store', Link::class);

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
