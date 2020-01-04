<?php

namespace App\Http\Controllers;

use App\Archive;
use App\Bus\Commands\Archive\DeleteArchiveCommand;
use App\Bus\Commands\Link\DeleteLinkCommand;
use App\Bus\Commands\Link\RestoreLinkCommand;
use App\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArchiveController extends Controller
{
    /**
     * @param \App\Archive $archive
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Archive $archive): JsonResponse
    {
        $link = Link::withTrashed()->where('id', '=', $archive->link_id)->first();

        $this->dispatch(new DeleteLinkCommand($link, true));

        $this->dispatch(new DeleteArchiveCommand($archive));

        return response()->json(Link::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArchives(): JsonResponse
    {
        $archives = Auth::user()->archivedLinks()->pluck('link_id');

        $links = Link::withTrashed()->whereIn('id', $archives)->get();

        return response()->json($links);
    }

    /**
     * @param \App\Archive $archive
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Archive $archive): JsonResponse
    {
        $link = Link::withTrashed()->where('id', '=', $archive->link_id)->first();

        $this->dispatch(new RestoreLinkCommand($link));

        $this->dispatch(new DeleteArchiveCommand($archive));

        return response()->json(Link::RESTORED_SUCCESS_MESSAGE);
    }
}
