#### うったコマンド
composer create-project --prefer-dist laravel/laravel .
composer require laravel/breeze --dev
php artisan breeze:install
npm install 
npm run dev //質問ロゴがめちゃデカくなっちゃう run dev できない　そのほかにauth-validation-errors componentがない
#### app.php
'timezone' => 'Asia/Tokyo',
'locale' => 'ja',

#### 日本語化　zip持ってくる
https://github.com/Laravel-Lang/lang

#### 日本語化　このコマンド打つ
php -r "copy('https://readouble.com/laravel/8.x/ja/install-ja-lang-files.php', 'install-ja-lang.php');"
php -f install-ja-lang.php
php -r "unlink('install-ja-lang.php');"


#### RouteServiceProvider.php
protected $namespace = 'App\\Http\\Controllers';
Route::get('/home', [HomeController::class, 'index'])->name('home'); が右のようにかける  Route::get('/home', 'HomeController@index')->name('home');

#### 
php artisan make:model Task -m
php artisan make:controller TaskController --model=Task
#### コンポーネント作成
php artisan make:component Message
#### 画像の保存
php artisan storage:link

#### ブックマークモデル、テーブル追加
php artisan make:model Bookmark -m
php artisan make:controller BookmarkController --model=Bookmark



#### タスク新規作成から作る createとstoreメソッド

#### views/task/create.blade.php改造
#### 画像登録
php artisan storage:link

#### プルダウンメニューの作り方 configの使い方も載ってる
https://www.kamome-susume.com/laravel-pulldown/

#### 中間テーブルの値更新　　意味がわからない
https://qiita.com/ijohnny/items/a67f3d3c989f82a269a1#%E4%B8%AD%E9%96%93%E3%83%86%E3%83%BC%E3%83%96%E3%83%AB%E3%81%AE%E5%80%A4%E3%82%92%E6%9B%B4%E6%96%B0%EF%BC%88%E6%9C%AC%E9%A1%8C%EF%BC%81%EF%BC%89

#### 中間テーブルの値を取得してくる  foreach ($members->groups as $group) { １つしかデータない場合でもこれでループさせないといけないのが気に食わない
https://biz.addisteria.com/laravel_withpivot/
https://www.nyamucoro.com/entry/2018/02/01/213102

#### リソースコントローラ作成
php artisan make:controller TaskController --resource
// createメソッド内でviews/task/create.blade.phpを呼び出す。
// ルートの設定
Route::resource('/task', 'TaskController');
// Bootstrap4 Datepickerを設定してカレンダーを設定
参考：https://qiita.com/saka212/items/55670d43f4bf6ef070cd

--- ここまででfirst commit

--- ここから続き
#### TaskModelに$fillableを追加
#### TaskControllerのstoreメソッドを定義
#### バリデーションの定義
#### 画像の保存 シンボリックリンク作成
php artisan storage:link
#### 全部のユーザをusersテーブルから取得して表示するようにした
参考：https://readouble.com/laravel/8.x/ja/queries.html
#### タスク一覧画面の作成
#### サイドバーを作成する
#### 削除と編集作成
--- ここまででsecond commit
#### これ参考にする bookmarksテーブルに外部キー制約を入れた
https://laraweb.net/practice/4393/
#### 多対多の関係を定義
--- ここまででthird commit
#### bookmarksテーブルを → user_taskに変更
php artisan migrate:rollback
php artisan make:migration create_tasks_users_table --create=task_user
php artisan migrate
php artisan make:model TaskUser
https://biz.addisteria.com/laravel_withpivot/
#### コントローラーの作成
php artisan make:controller BookmarkController --resource

### これじゃないとダメ　　Base table or view not found: 1146 Table 'todo.task_users' doesn't exist と言われた
task_usersテーブル
php artisan make:model TaskUser -m
php artisan migrate:rollback --step=1


#### デプロイの時にやったこと
composer install
apt-get update
apt-get install vim
vi .env
php artisan migrate
chmod 777 storage/ -R // 質問これやっていいの
vi AppServiceProvider.php // 質問https://zakkuri.life/laravel-chrome-security/ これやらないと注意でる
php artisan storage:link
質問 stacktraceの表示されてるエラー画面でる　これはそれでいいのか？


やってないこと
・ログイン、アカウント登録、は全てbreezeのデフォルトのを使ってる。pathやDBのテーブル定義もそのまま使っている。
・タスクの削除・編集が誰でもできてしまう。他人のアサインされているタスクを誰でも消せちゃうから、管理者と閲覧者などロールが必要
・ボタンにcomponentのprimary-button.bladeを使っているが、色が変わらなく、編集・削除ボタンなど本当は別のcomponent作って使う必要がある




アプリとしての挙動
ブックマーク一覧ページのルーティングは /tasks/bookmarksとなっているが  /bookmarksが気持ちいい（ブックマークの登録のルーティングは /tasks/{task}/bookmark でOK）
全タスク一覧は /tasks/showalltasks じゃなくて /tasksとかが気持ちいい

実装面
BookmarkController
showbookmarksメソッド

◆クエリビルダじゃなくEloquentを使いましょう
        $tasksWithBookmarks = TASK::Join(‘bookmarks’, ‘bookmarks.task_id’, ‘=’, ‘tasks.id’) 
               ->where(‘bookmarks.user_id’, ‘=’, $user->id) 
               ->get();
ですが、Eloquntの機能を使った方がいいです。上記はクエリビルダでSQLを書いてる感じであまり良くないです。（あとTASKじゃなくてTaskです）
リレーションを使ってこんな感じで取れます。
$tasksWithBookmarks = Task::whereHas('bookmarks', function($query) use ($user) {
        $query->where('user_id', $user->id);
    })
    ->get();
JOINしてしまうと、単一の表になってしまい、ループしづらいです。
リレーションでやれば、テーブルの入れ子構造のまま取得できるので、ループしやすいです。


bookmarkメソッド

◆早期リターン（ガード節）を使いましょう！
if ($request->is_bookmarked) 
{
    $task->bookmarks()->where(‘user_id’, auth()->user()->id )->delete();
    return back()->with(‘message’, ‘ブックマーク解除しました’);
} else { 
    $bookmark = Bookmark::create([ 
           ‘user_id’=>auth()->user()->id, 
           ‘task_id’=>$task->id,
    ]);
    return back()->with(‘message’, ‘ブックマークしました’);
}
ここは、elseなど不要です！
if ($request->is_bookmarked) {
    $task->bookmarks()->where('user_id', auth()->user()->id )->delete();
    return back()->with('message', 'ブックマーク解除しました');
}

$bookmark = Bookmark::create([ 
    'user_id'=>auth()->user()->id, 
    'task_id'=>$task->id,
]);
return back()->with('message', 'ブックマークしました');
if のtrue側にreturnがあるので、elseがなくとも同じになります。
代わりにネスト（インデント）がなくなるので可読性が上がります。

◆ログイン中ユーザーのID
auth()->user()->id
もちろんこれでもいいですが、
auth()->id()
でIDだけ取りたいならこちらもあります。
（別に無理に変えなくても、知識として知っておいて損はないです！）

◆不要なアクションメソッドは消しときましょう
リソースコントローラの作成方法で自動で生まれているコードでしょうが、後でみた時になんだろう？ってなりやすいので、不要なメソッドは消しておくのが良いです！
indexやcreateなど


TaskController
全体的に
Viewに$userを毎回渡してますが、auth()ヘルパはどこからでも呼べるのでコントローラで無理に毎回渡さずともblade側でauth()ヘルパを直接呼び出した方がいいかと思います。そうすれば、本質的なその機能に必要なことだけがコントローラに残るので「何に注視すればいいのか」がはっきりしてきます。
construct
$this->task = new Task();　をしてますが、storeメソッドでしか使ってないので、storeメソッド側に書いてもいいかと思います。

storeメソッド
バリデーションをコントローラに書いてますが、フォームリクエストクラスというのがあり、バリデーション部分を別クラスに分けることができます。これによりFatコントローラを防ぐことができます！
リクエストのattached_file_pathは名前が不適切だと思います。リクエストされているのはファイルの「パス」ではなく、「ファイルそのもの」が届いてるので変数名があまりよくないです！attached_fileとかがいいかと！

フォームリクエストクラス調べたリンク
https://readouble.com/laravel/8.x/ja/validation.html

```
php artisan make:request StoreUpdateTaskRequest
```

destoryメソッド
bookmarkを先に消してますがDBのテーブルでFKを貼っていれば、わざわざコードで消さずともDBの機能で消すことができます。FKをテーブル間の連携IDには貼っておくのが良いです

テーブル定義
tasksテーブル
$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); でFKを貼ったまではいいですが、これだと該当ユーザが削除されると紐づいたタスクも消えてしまいます。意味合い的には担当者なので、ユーザーが消えたらタスクは残って、誰かが引き継ぐとかしないといけないと思うので、タスクが消えちゃうのはまずいかと！set nullあたりが妥当かと思います。


blade
bookmark.blade.php
                                @php
	                                    $is_bookmarked = false;
	                                @endphp
	                                @if ($task->user_id === $user->id)
	                                    @php
	                                        $is_bookmarked = true;
	                                    @endphp
	                                    <form method="post" action="{{route('tasks.bookmark', $task->task_id)}}">
	                                        @csrf
	                                        <input type="hidden" name='is_bookmarked' value="{{$is_bookmarked}}">
	                                        <x-primary-button class="float-right mr-4 mb-12">ブックマーク解除</x-primary-button>
	                                    </form>
	                                @endif
	                                
	                                @if ($is_bookmarked === false)
	                                    <form method="post" action="{{route('tasks.bookmark', $task->task_id)}}">
	                                        @csrf
	                                        <input type="hidden" name='is_bookmarked' value="{{$is_bookmarked}}">
	                                        <x-primary-button class="float-right mr-4 mb-12">ブックマーク</x-primary-button>
	                                    </form>
	                                @endif
このあたりの分岐のロジックが怪しいような感じがします。
コントローラーで自分に紐づくタスクしか取ってないのに、@if ($task->user_id === $user->id) の分岐は不要かと思います。
つまり @if ($is_bookmarked === false) 側の中に入ることはないんじゃないですかね？


タスク一覧で内容の改行が表現できてない。あと画像も出てない（画像５）