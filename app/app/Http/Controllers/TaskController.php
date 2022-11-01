<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->task = new Task();
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
        $user = auth()->user();
        return view('showAllTasks', compact('tasks', 'user'));
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
        $myself = auth()->user();
        $users = User::all();
        return view('task.create', compact('users', 'myself'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->validate([
            'title'=>'required|max:255',
            'content'=>'required|max:255',
            'user_id'=>'required',
            'start_date' => 'required|date|after:tomorrow',
            'end_date' => 'required|date|after:start_date',
            'attached_file_path'=>'image|max:1024'
        ]);
        $this->task->title = $inputs['title'];
        $this->task->content = $inputs['content'];
        $this->task->user_id = $inputs['user_id'];
        $this->task->start_date = $inputs['start_date'];
        $this->task->end_date = $inputs['end_date'];
        if (request('attached_file_path')){
            $original = request()->file('attached_file_path')->getClientOriginalName();
             // 日時追加
            $name = date('Ymd_His').'_'.$original;
            request()->file('attached_file_path')->move('storage/images', $name);
            $this->task->attached_file_path = $name;
        }
        $this->task->save();
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
    public function update(Request $request, Task $task)
    {
        $inputs = $request->validate([
            'title'=>'required|max:255',
            'content'=>'required|max:255',
            'user_id'=>'required',
            'start_date' => 'required|date|after:tomorrow',
            'end_date' => 'required|date|after:start_date',
            'status'=>'required',
            'attached_file_path'=>'image|max:1024'
        ]);
        $task->title = $inputs['title'];
        $task->content = $inputs['content'];
        $task->user_id = $inputs['user_id'];
        $task->start_date = $inputs['start_date'];
        $task->end_date = $inputs['end_date'];
        $task->status = $inputs['status'];
        if (request('attached_file_path')){
            $original = request()->file('attached_file_path')->getClientOriginalName();
             // 日時追加
            $name = date('Ymd_His').'_'.$original;
            request()->file('attached_file_path')->move('storage/images', $name);
            $this->task->attached_file_path = $name;
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
        $task->bookmarks()->delete();
        $task->delete();
        return redirect()->route('mypage')->with('message', 'タスクを削除しました');
    }
}
