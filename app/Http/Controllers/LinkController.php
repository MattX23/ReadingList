<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkEditRequest;
use App\Http\Requests\LinkRequest;
use App\Link;
use App\ReadingList;
use App\Traits\AuthorizeSoftDeletesTrait;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LinkController extends Controller
{
    use AuthorizeSoftDeletesTrait;

    /**
     * @param Link $link
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function archive(Link $link): JsonResponse
    {
        $link->delete();

        return response()->json(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    /**
     * @param Link $link
     *
     * @return JsonResponse
     */
    public function delete(Link $link): JsonResponse
    {
        $link->forceDelete();

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function forceDelete(int $id): JsonResponse
    {
        $link = $this->authorizeSoftDeletedModel(Link::class, $id, 'delete', true);

        $link->deleteInactiveList();

        $link->forceDelete();

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param \App\User $user
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getArchivedLinks(User $user)
    {
        $links = Collection::make();

        $user->readingLists()->withTrashed()->each(function(ReadingList $readingList) use ($links) {
            $readingList->links()->onlyTrashed()->each(function(Link $link) use ($links) {
                $links->push($link);
            });
        });

        return $links;
    }

    /**
     * @param \App\User $user
     *
     * @return JsonResponse
     */
    protected function getArchives(User $user): JsonResponse
    {
        $links = $this->getArchivedLinks($user);

        if ($links->count() > 0) $this->authorizeSoftDeletedModel(Link::class, $links->first()->id, 'viewArchives');

        return response()->json($links);
    }

    /**
     * @param Link                               $link
     * @param \App\Http\Requests\LinkEditRequest $request
     *
     * @return JsonResponse
     */
    protected function rename(Link $link, LinkEditRequest $request): JsonResponse
    {
        $link->title = $request->name;
        $data = $link->toArray();

        $link->update([
            'title' => $data['title'],
        ]);

        return response()->json(Link::EDITED_SUCCESS_MESSAGE);
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
     */
    protected function restore(int $id): JsonResponse
    {
        $link = $this->authorizeSoftDeletedModel(Link::class, $id, 'restore', true);

        $link->restoreSoftDeletedList();

        $link->restore();

        return response()->json(Link::RESTORED_SUCCESS_MESSAGE);
    }

    /**
     * @param \App\Http\Requests\LinkRequest $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function store(LinkRequest $request): JsonResponse
    {
        $data = [
            'url'             => $request->name,
            'reading_list_id' => $request->id,
            'position'        => (new Link())->getNewLinkPosition($request->id),
            'title'           => $request->name,
        ];

        $link = new Link($data);

        $link->getPreview($link, $data['url']);

        $link->save();

        return response()->json(Link::SAVED_SUCCESS_MESSAGE);
    }
}
