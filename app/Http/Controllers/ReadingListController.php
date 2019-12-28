<?php

namespace App\Http\Controllers;

use App\Link;
use App\ReadingList;
use App\Traits\AuthorizeSoftDeletesTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReflectionException;

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
     */
    public function delete(int $id): JsonResponse
    {
        $readingList = $this->authorizeSoftDeletedModel(ReadingList::class, $id, 'delete', true);

        if (!$readingList->links()->exists())

            if ($readingList->hasTrash() ? $readingList->delete() : $readingList->forceDelete()) {
                $this->reorderListsAfterDelete();

                return response()->json(ReadingList::DELETED_SUCCESS_MESSAGE);
            }

        return response()->json(ReadingList::DELETED_FAILED_MESSAGE, 422);
    }

    /**
     * @param ReadingList $readingList
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function edit(ReadingList $readingList, Request $request): JsonResponse
    {
        $data = [
            'name'     => $request->name,
            'user_id'  => Auth::user()->id,
        ];

        if (!$readingList->validate($data)) return response()->json($readingList->validationErrors(), 422);

        $readingList->update($data);

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
                'position'        => $index + 1,
                'reading_list_id' => $list_id,
            ]);
        }
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

        return response()->json(ReadingList::CREATED_SUCCESS_MESSAGE);
    }
}
