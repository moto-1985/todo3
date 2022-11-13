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
        // $tasksWithBookmarks = TASK::Join('bookmarks', 'bookmarks.task_id', '=', 'tasks.id')
        //     ->where('bookmarks.user_id', '=', $user->id)
        //     ->get();
        // dd($tasksWithBookmarks);
        // 指摘箇所
        // クエリビルダを使わないでEloquentを使用
        $tasksWithBookmarks = Task::whereHas('bookmarks', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        // dd($tasksWithBookmarks);
        return view('bookmarks', compact('tasksWithBookmarks', 'user'));
    }

    public function bookmark(Request $request, Task $task)
    {
        // if ($request->is_bookmarked) 
        // {
        //     $task->bookmarks()->where('user_id', auth()->user()->id )->delete();
        //     return back()->with('message', 'ブックマーク解除しました');
        // } else {
        //     $bookmark = Bookmark::create([
        //     'user_id'=>auth()->user()->id,
        //     'task_id'=>$task->id,
        // ]);
        // 指摘箇所
        // 不要なelse文を削除 早期リターン（ガード節）を使用　auth()->user()->idはauth()->id()でIDだけ取れる
        if ($request->is_bookmarked) 
        {
            $task->bookmarks()->where('user_id', auth()->id() )->delete();
            return back()->with('message', 'ブックマーク解除しました');
        };

        $bookmark = Bookmark::create([ 
            'user_id'=>auth()->id(), 
            'task_id'=>$task->id,
        ]);

        return back()->with('message', 'ブックマークしました');
    }
}
