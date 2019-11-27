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
    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', ReadingList::find($id));

        if (!ReadingList::find($id)->links()->exists())

            if (ReadingList::destroy($id)) {
                $this->reorderListsAfterDelete();

                return response()->json("List deleted");
            }

        return response()->json('List not empty', 422);
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

        return response()->json("List name updated");
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

        return response()->json("New list created");
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

        $oldList = ReadingList::find($list_id);

        if ($oldList->name === ReadingList::RESTORED_LIST &&
            count($oldList->links) < 1) ReadingList::destroy($oldList->id);

        $i = 1;

        foreach ($request->links as $link) {
            Link::where('id', '=', $link['id'])->update([
                'position'        => $i,
                'reading_list_id' => $list_id,
            ]);
            $i++;
        }
    }
}
