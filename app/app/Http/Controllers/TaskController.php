<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function __construct()
    {
        // 指摘箇所$this->task = new Task();　をしてますが、storeメソッドでしか使ってないので、storeメソッド側に書いてもいいかと思います。
        // $this->task = new Task();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mypage()
    {
        // $tasks = $this->task::all();
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)->orderBy('updated_at','desc')->get();
        return view('mypage', compact('tasks', 'user'));
    }

    public function showAllTasks()
    {
        // $tasks = $this->task::all();
        $tasks = Task::orderBy('updated_at','desc')->get();
        // 指摘箇所auth()ヘルパはどこからでも呼べるのでコントローラで無理に毎回渡さずともblade側でauth()ヘルパを直接呼び出した方がいいかと思います。
        // $user = auth()->user();
        return view('showAllTasks', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // これはDBから全てのユーザをとってる。これは何千件いても取ってきてしまう危ない処理！
        // ->all()とか ->where->get()ok  ->get()これはやばい可能性あり。
        // select * from users
        // DB:: クエリビルダ。SQLを直で書いてる。Laravelっぽくない書き方
        // 一昔前のPHP
        // $pdo = new PDO( PDO_DSN, DATABASE_USER, DATABASE_PASSWORD );
        // $smt = $pdo->prepare('select * from users where id = ?'); 
        // $smt->bindValue(1, $id, PDO::PARAM_STR);
        // $smt->execute();
        //- それに比較したクエリビルダ すこしSQLっぽさ残ってる。
        // DB::table('users')->where('id', 1)->get();
        // query builder SQLをビルドするもの。関数で書けるように
        // - モデル（eloquent) eloquentはライブラりーの名前 php ORMをやるときのライブラリー　eloquentを制するものはLaravelを制す。Laravelを使う理由にもなるようなもの
        // Laravel も便利なライブラリーを全部オールインワンで持ってきたような感じ
        // User::find(1);
        // User::where('id', 1)->first();
        // ORMapper クラスとテーブルが一対一になっている。クラスをいじればOK！
        // $myself = auth()->user();
        $users = User::all();
        return view('task.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $task = new Task();
        // 指摘箇所リクエストのattached_file_pathは名前が不適切だと思います。file自身を取得してる
        // $inputs = $request->validate([
        //     'title'=>'required|max:255',
        //     'content'=>'required|max:255',
        //     'user_id'=>'required',
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date|after:start_date',
        //     // 'attached_file_path'=>'image|max:1024'
        //     'attached_file'=>'image|max:1024'
        // ]);
        $task->title = $request['title'];
        $task->content = $request['content'];
        $task->user_id = $request['user_id'];
        $task->start_date = $request['start_date'];
        $task->end_date = $request['end_date'];
        if (request('attached_file')){
            $original = request()->file('attached_file')->getClientOriginalName();
             // 日時追加
            $name = date('Ymd_His').'_'.$original;
            request()->file('attached_file')->move('storage/images', $name);
            $task->attached_file_path = $name;
        }
        $task->save();
        return redirect()->route('mypage')->with('message', 'タスクを作成しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return view('task.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $users = User::all();
        return view('task.edit', compact('task','users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        // $inputs = $request->validate([
        //     'title'=>'required|max:255',
        //     'content'=>'required|max:255',
        //     'user_id'=>'required',
        //     'start_date' => 'required|date',
        //     'end_date' => 'required|date|after:start_date',
        //     'status'=>'required',
        //     'attached_file'=>'image|max:1024'
        // ]);
        $task->title = $request['title'];
        $task->content = $request['content'];
        $task->user_id = $request['user_id'];
        $task->start_date = $request['start_date'];
        $task->end_date = $request['end_date'];
        $task->status = $request['status'];
        if (request('attached_file')){
            $original = request()->file('attached_file')->getClientOriginalName();
             // 日時追加
            $name = date('Ymd_His').'_'.$original;
            request()->file('attached_file')->move('storage/images', $name);
            $task->attached_file_path = $name;
        }
        $task->save();
        return redirect()->route('mypage')->with('message', 'タスクを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        // 指摘事項　bookmarkを先に消してますがDBのテーブルでFKを貼っていれば、わざわざコードで消さずともDBの機能で消すことができます。FKをテーブル間の連携IDには貼っておくのが良いです
        // $task->bookmarks()->delete();
        $task->delete();
        return redirect()->route('mypage')->with('message', 'タスクを削除しました');
    }
}
