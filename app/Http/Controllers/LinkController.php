<?php

namespace App\Http\Controllers;

use App\Bus\Commands\Link\CreateLinkCommand;
use App\Bus\Commands\Link\DeleteLinkCommand;
use App\Bus\Commands\Link\EditLinkCommand;
use App\Bus\Commands\Link\RestoreLinkCommand;
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
use Illuminate\Support\Facades\Config;

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
        $this->dispatch(new DeleteLinkCommand($link));

        return response()->json(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    /**
     * @param Link $link
     *
     * @return JsonResponse
     */
    public function delete(Link $link): JsonResponse
    {
        $this->dispatch(new DeleteLinkCommand($link, true));

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function deleteFromArchives(int $id): JsonResponse
    {
        $link = $this->authorizeSoftDeletedModel(
            Link::class,
            $id,
            Config::get('policies.policy.delete'),
            true
        );

        $this->dispatch(new DeleteLinkCommand($link, true));

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param \App\User $user
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getArchivedLinks(User $user): Collection
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

        if ($links->count() > 0) $this->authorizeSoftDeletedModel(
            Link::class,
            $links->first()->id,
            Config::get('policies.policy.view_archives')
        );

        return response()->json($links);
    }

    /**
     * @param Link                               $link
     * @param \App\Http\Requests\LinkEditRequest $request
     *
     * @return JsonResponse
     */
    protected function edit(Link $link, LinkEditRequest $request): JsonResponse
    {
        $this->dispatch(new EditLinkCommand($link, $request->name));

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
        $link = $this->authorizeSoftDeletedModel(
            Link::class,
            $id,
            Config::get('policies.policy.restore'),
            true
        );

        $this->dispatch(new RestoreLinkCommand($link));

        return response()->json(Link::RESTORED_SUCCESS_MESSAGE);
    }

    /**
     * @param \App\Http\Requests\LinkRequest $request
     *
     * @return JsonResponse
     */
    protected function store(LinkRequest $request): JsonResponse
    {
        $this->dispatch(new CreateLinkCommand(
            $request->name,
            $request->id,
            $request->name
        ));

        return response()->json(Link::SAVED_SUCCESS_MESSAGE);
    }
}
