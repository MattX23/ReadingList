<?php

namespace App\Http\Controllers;

use App\Link;
use App\ReadingList;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReflectionException;

class ReadingListController extends Controller
{
    /**
     * @var string
     */
    const DELETED_SUCCESS_MESSAGE = "List deleted";

    /**
     * @var string
     */
    const DELETED_FAILED_MESSAGE = "List not empty";

    /**
     * @var string
     */
    const UPDATED_SUCCESS_MESSAGE = "List name updated";

    /**
     * @var string
     */
    const CREATED_SUCCESS_MESSAGE = "New list created";

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
     * @param string $name
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
     * @throws AuthorizationException
     */
    public function delete(int $id): JsonResponse
    {
        $readingList = ReadingList::find($id);

        $this->authorize('delete', $readingList);

        if (!$readingList->links()->exists())

            if ($readingList->hasTrash() ? $readingList->delete() : $readingList->forceDelete()) {
                $this->reorderListsAfterDelete();

                return response()->json(self::DELETED_SUCCESS_MESSAGE);
            }

        return response()->json(self::DELETED_FAILED_MESSAGE, 422);
    }

    /**
     * @param ReadingList $readingList
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ReflectionException
     * @throws AuthorizationException
     */
    public function edit(ReadingList $readingList, Request $request): JsonResponse
    {
        $this->authorize('edit', $readingList);

        $user = Auth::user();

        $data = [
            'name'     => $request->name,
            'user_id'  => $user->id,
        ];

        if (!$readingList->validate($data)) return response()->json($readingList->validationErrors(), 422);

        $readingList->update($data);

        return response()->json(self::UPDATED_SUCCESS_MESSAGE);
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
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        $data = [
            'name'     => $request->name,
            'user_id'  => Auth::user()->id,
            'position' => (new ReadingList())->getNewReadingListPosition($user),
        ];

        $list = new ReadingList($data);

        if (!$list->validate($data)) return response()->json($list->validationErrors(), 422);

        $list->save();

        return response()->json(self::CREATED_SUCCESS_MESSAGE);
    }

    /**
     * @param Request $request
     */
    public function reorderList(Request $request): void
    {
        $ids = $request->toArray();
        (new ReadingList())->reorderLists($ids);
    }

    protected function reorderListsAfterDelete(): void
    {
        $lists = ReadingList::all()->sortBy('position');

        $i = 1;

        foreach ($lists as $list) {
            $list->update([
               'position' => $i
            ]);
            $i++;
        }
    }

    /**
     * @param Request $request
     */
    public function reorderMultipleLists(Request $request): void
    {
        $list_id = $request->id;

        foreach ($request->links as $index => $link) {
            Link::where('id', '=', $link['id'])->update([
                'position'        => $index,
                'reading_list_id' => $list_id,
            ]);
        }
    }
}
