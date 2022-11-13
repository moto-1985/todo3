<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            タスク詳細
        </h2>

        <x-message :message="session('message')" />

    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mx-4 sm:p-8">
            <div class="px-10 mt-4">

                <div class="bg-white w-full  rounded-2xl px-10 py-8 shadow-lg hover:shadow-2xl transition duration-500">
                    <div class="mt-4">
                        <h1 class="text-lg text-gray-700 font-semibold ">
                            {{ $task->title }}
                        </h1>
                        <hr class="w-full">
                    </div>
                    <div class="flex justify-end mt-4">
                        <a href="{{route('tasks.edit', $task)}}"><x-primary-button class="bg-teal-700 float-right">編集</x-primary-button></a>
                        <form method="post" action="{{route('tasks.destroy', $task)}}">
                            @csrf
                            @method('delete')
                            <x-primary-button class="bg-red-700 float-right ml-4" onClick="return confirm('本当に削除しますか？');">削除</x-primary-button>
                        </form>
                    </div>
                    {{-- div 追加部分 --}}
                    <div>
                        <h2 class="text-lg text-gray-700 font-semibold mt-4">内容</h2>
                        <p class="text-gray-600">{!!nl2br(e(Str::limit($task->content, 100, ' …' )))!!}</p>
                        @if($task->attached_file_path)
                            <img src="{{ asset('storage/images/'.$task->attached_file_path)}}" class="mx-auto" style="height:300px;">
                            <div class="mt-4 flex flex-row-reverse">
                                (添付ファイル：{{$task->attached_file_path}})
                            </div>
                        @endif
                        <p class=" text-gray-600 pt-4">ステータス：{{ config('task.task_status.' . $task->status) }} </p>
                        <p class=" text-gray-600 pt-4">開始日：{{ $task->start_date }} </p>
                        <p class=" text-gray-600 pt-4">終了日：{{ $task->end_date }} </p>
                        <div class="text-sm font-semibold flex flex-row-reverse">
                            <p> 担当者：{{ $task->user->name}} •タスク作成日 {{$task->updated_at->format('Y/m/d H:i:s')}}</p>
                        </div>
                    </div>
                    {{-- コメント表示 --}}
                    {{-- @foreach ($task->comments as $comment)
                    <div class="bg-white w-full  rounded-2xl px-10 py-8 shadow-lg hover:shadow-2xl transition duration-500 mt-8">
                        {{$comment->body}}
                        <div class="text-sm font-semibold flex flex-row-reverse">
                            <p> {{ $comment->user->name }} • {{$comment->created_at->format('Y/m/d H:i:s')}}</p>
                        </div>
                    </div>
                    @endforeach --}}
                    {{-- 追加部分 --}}
                    {{-- <div class="mt-4 mb-12">
                        <form method="post" action="{{route('comment.store')}}">
                            @csrf
                            <input type="hidden" name='task_id' value="{{$task->id}}">
                            <textarea name="body" class="bg-white w-full  rounded-2xl px-4 mt-4 py-4 shadow-lg hover:shadow-2xl transition duration-500" id="body" cols="30" rows="3" placeholder="コメントを入力してください">{{old('body')}}</textarea>
                            <x-primary-button class="float-right mr-4 mb-12">コメントする</x-primary-button>
                        </form>
                    </div> --}}
                    {{-- 追加部分終わり --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
