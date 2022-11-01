<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    public function showbookmarks()
    {
        $user = auth()->user();
        $tasksWithBookmarks = TASK::Join('bookmarks', 'bookmarks.task_id', '=', 'tasks.id')
            ->where('bookmarks.user_id', '=', $user->id)
            ->get();
        return view('bookmarks', compact('tasksWithBookmarks', 'user'));
    }

    public function bookmark(Request $request, Task $task)
    {
        if ($request->is_bookmarked) 
        {
            $task->bookmarks()->where('user_id', auth()->user()->id )->delete();
            return back()->with('message', 'ブックマーク解除しました');
        } else {
            $bookmark = Bookmark::create([
            'user_id'=>auth()->user()->id,
            'task_id'=>$task->id,
        ]);
        return back()->with('message', 'ブックマークしました');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return \Illuminate\Http\Response
     */
    public function show(Bookmark $bookmark)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return \Illuminate\Http\Response
     */
    public function edit(Bookmark $bookmark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bookmark  $bookmark
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bookmark  $bookmark
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bookmark $bookmark)
    {
        //
    }
}
