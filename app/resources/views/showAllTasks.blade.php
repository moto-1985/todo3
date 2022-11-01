<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            全タスク一覧
        </h2>

        <x-message :message="session('message')" />

    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if (count($tasks) == 0)
        <p class="mt-4">
        まだタスクが１つも作成されていません
        </p>
        @else
        @foreach ($tasks as $task)
            <div class="mx-4 sm:p-8">
                <div class="mt-4">
                    <div class="bg-white w-full  rounded-2xl px-10 py-8 shadow-lg hover:shadow-2xl transition duration-500">
                        <div class="mt-4">
                            {{-- 追加部分 --}}
                            <div class="mt-4 mb-12">
                                @php
                                    $is_bookmarked = false;
                                    @endphp
                                @foreach ($task->bookmarks as $bookmark)
                                    @if ($bookmark->user->id === $user->id)
                                    @php
                                        $is_bookmarked = true;
                                    @endphp
                                    <form method="post" action="{{route('tasks.bookmark', $task)}}">
                                        @csrf
                                        <input type="hidden" name='is_bookmarked' value="{{$is_bookmarked}}">
                                        <x-primary-button class="float-right mr-4 mb-12">ブックマーク解除</x-primary-button>
                                    </form>
                                    @endif
                                @endforeach
                                @if ($is_bookmarked === false)
                                    <form method="post" action="{{route('tasks.bookmark', $task)}}">
                                        @csrf
                                        <input type="hidden" name='is_bookmarked' value="{{$is_bookmarked}}">
                                        <x-primary-button class="float-right mr-4 mb-12">ブックマーク</x-primary-button>
                                    </form>
                                @endif
                            </div>
                            {{-- 追加部分終わり --}}
                            <h1 class="text-lg text-gray-700 font-semibold hover:underline cursor-pointer float-left pt-4">
                                <a href="{{route('tasks.show', $task)}}">{{Str::limit ($task->title, 100, ' …' )}} </a>
                            </h1>
                            <hr class="w-full">
                            <h2 class="text-lg text-gray-700 font-semibold mt-4">内容</h2>
                            <p class=" text-gray-600">{{Str::limit ($task->content, 100, ' …' )}} </p>
                            @if($task->attached_file_path)
                                <img src="{{ asset('storage/images/'.$task->attached_file_path)}}" class="mx-auto" style="height:300px;">
                                <div class="mt-4 flex flex-row-reverse">
                                    (添付ファイル：{{$task->attached_file_path}})
                                </div>
                            @endif
                            <p class=" text-gray-600 pt-4">ステータス：{{ config('task.task_status.' . $task->status) }}</p>
                            <p class=" text-gray-600 pt-4">開始日：{{ $task->start_date }} </p>
                            <p class=" text-gray-600 pt-4">終了日：{{ $task->end_date }} </p>
                            <div class="text-sm font-semibold flex flex-row-reverse">
                                <p> 担当者：{{ $task->user->name}} •タスク作成日 {{$task->updated_at->format('Y/m/d H:i:s')}}</p>
                            </div>
                            {{-- 追加部分 --}}
                            <hr class="w-full mb-2">
                            {{-- @if ($task->comments->count()) --}}
                            <span class="badge">
                                {{-- 返信 {{$task->comments->count()}}件 --}}
                            </span>
                            {{-- @else --}}
                            {{-- <span>コメントはまだありません。</span> --}}
                            {{-- @endif --}}
                            {{-- <a href="{{route('task.show', $task)}}" style="color:white;"> --}}
                                   {{-- <x-primary-button class="float-right">コメントする</x-primary-button> --}}
                            {{-- </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @endif
    </div>
</x-app-layout>