<?php

namespace App\Http\Controllers;

use App\Bus\Commands\ReadingList\CreateReadingListCommand;
use App\Bus\Commands\ReadingList\DeleteReadingListCommand;
use App\Bus\Commands\ReadingList\EditReadingListCommand;
use App\Http\Requests\ReadingListRequest;
use App\Link;
use App\ReadingList;
use App\Traits\AuthorizeSoftDeletesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ReadingListController extends Controller
{
    use AuthorizeSoftDeletesTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * ReadingListController constructor.
     *
     * @param string   $name
     * @param int|null $user_id
     */
    public function __construct(string $name = '', int $user_id = null)
    {
        $this->name     = $name;
        $this->user_id  = $user_id;
    }

    /**
     * @param integer $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $readingList = $this->authorizeSoftDeletedModel(
            ReadingList::class,
            $id,
            Config::get('policies.policy.delete'),
            true
        );

        $this->dispatch(new DeleteReadingListCommand($readingList));

        return response()->json(ReadingList::DELETED_SUCCESS_MESSAGE);
    }

    /**
     * @param ReadingList                           $readingList
     * @param \App\Http\Requests\ReadingListRequest $request
     *
     * @return JsonResponse
     */
    public function edit(ReadingList $readingList, ReadingListRequest $request): JsonResponse
    {
        $this->dispatch(new EditReadingListCommand(
            $readingList,
            $request->name
        ));

        return response()->json(ReadingList::UPDATED_SUCCESS_MESSAGE);
    }

    /**
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        $readingLists = Auth::user()->readingLists;

        return response()->json(['readingLists' => $readingLists]);
    }

    /**
     * @param Request $request
     */
    public function reorderList(Request $request): void
    {
        $ids = $request->toArray();
        (new ReadingList())->reorderLists($ids);
    }

    /**
     * @param Request $request
     */
    public function reorderMultipleLists(Request $request): void
    {
        $list_id = $request->id;

        foreach ($request->links as $index => $link) {
            Link::where('id', '=', $link['id'])->update([
                'position'        => $index + 1,
                'reading_list_id' => $list_id,
            ]);
        }
    }

    /**
     * @param \App\Http\Requests\ReadingListRequest $request
     *
     * @return JsonResponse
     */
    public function store(ReadingListRequest $request): JsonResponse
    {
        $this->dispatch(new CreateReadingListCommand(
            $request->name,
            Auth::user()
        ));

        return response()->json(ReadingList::CREATED_SUCCESS_MESSAGE);
    }

}
