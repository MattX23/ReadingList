<?php

namespace App\Http\Controllers;

use App\Link;
use App\ReadingList;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LinkController extends Controller
{
    /**
     * @var string
     */
    const ARCHIVED_SUCCESS_MESSAGE = 'Link archived';

    /**
     * @var string
     */
    const DELETED_SUCCESS_MESSAGE = 'Link permanently deleted';

    /**
     * @var string
     */
    const EDITED_SUCCESS_MESSAGE = 'Link title updated';

    /**
     * @var string
     */
    const RESTORED_SUCCESS_MESSAGE = 'Link restored';

    /**
     * @var string
     */
    const SAVED_SUCCESS_MESSAGE = 'Link added';

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

        (new ReadingList())->deleteInactiveList($link);

        $link->forceDelete();

        return response()->json(self::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param \App\User $user
     *
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function getArchives(User $user): JsonResponse
    {
        $links = Collection::make();

        $user->readingLists()->withTrashed()->each(function(ReadingList $readingList) use ($links) {
            $readingList->links()->onlyTrashed()->each(function(Link $link) use ($links) {
                $links->push($link);
            });
        });

        if ($links->count() > 0) $this->authorize('viewArchives', $links->first());

        return response()->json($links);
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

        return response()->json(self::EDITED_SUCCESS_MESSAGE);
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

        if (!in_array($link->reading_list_id, $readingListIds)) (new ReadingList())->restoreList($link);

        $link->restore();

        return response()->json(self::RESTORED_SUCCESS_MESSAGE);
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

        return response()->json(self::SAVED_SUCCESS_MESSAGE);
    }
}
