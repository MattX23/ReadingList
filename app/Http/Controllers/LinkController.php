<?php

namespace App\Http\Controllers;

use App\Bus\Commands\Link\CreateLinkCommand;
use App\Bus\Commands\Link\DeleteLinkCommand;
use App\Bus\Commands\Link\EditLinkCommand;
use App\Http\Requests\LinkEditRequest;
use App\Http\Requests\LinkRequest;
use App\Link;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $this->dispatch(new DeleteLinkCommand($link));

        return response()->json(Link::ARCHIVED_SUCCESS_MESSAGE);
    }

    /**
     * @param Link $link
     *
     * @return JsonResponse
     */
    public function deletePermanently(Link $link): JsonResponse
    {
        $this->dispatch(new DeleteLinkCommand($link, true));

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
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
    protected function reorderLinks(Request $request): void
    {
        $ids = $request->toArray();
        (new Link())->reorderLinks($ids);
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
