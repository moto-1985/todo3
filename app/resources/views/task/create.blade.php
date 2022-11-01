<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            タスク新規作成
        </h2>

        {{-- <x-validation-errors class="mb-4" :errors="$errors" /> --}}

        <x-message :message="session('message')" />
    </x-slot>
    
    {{-- 最初に作成した部分 --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
        <div class="text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="mx-4 sm:p-8">
            <form enctype="multipart/form-data" action="{{route('tasks.store')}}" method="post">
                @csrf
                <div class="md:flex items-center mt-8">
                    <div class="w-full flex flex-col">
                    <label for="title" class="font-semibold leading-none mt-4">タイトル</label>
                    <input type="text" name="title" class="w-auto py-2 placeholder-gray-300 border border-gray-300 rounded-md" id="title" value="{{old('title')}}"  placeholder="Enter Title">
                    </div>
                </div>

                <div class="w-full flex flex-col">
                    <label for="content" class="font-semibold leading-none mt-4">内容</label>
                    <textarea name="content" class="w-auto py-2 placeholder-gray-300 border border-gray-300 rounded-md" id="content" cols="30" rows="10">{{old('content')}}</textarea>
                </div>

                <div class="w-full flex flex-col">
                    <label for="attached_file_path" class="font-semibold leading-none mt-4">添付ファイル(1MBまで) </label>
                    <div>
                    <input id="attached_file_path" type="file" name="attached_file_path">
                    </div>
                </div>

                <label for="user_id" class="block font-semibold leading-none  mt-4">担当者</label>
                    <select name="user_id" id="user_id" class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $myself->id === $user->id ? "selected": '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>

                <label for="start_date" class="block font-semibold leading-none mt-4">開始日付</label>
                <input type="date" id="start_date" name="start_date" value="{{ empty(old('start_date'))? \Carbon\Carbon::today(): old('start_date') }}" class="block">

                <label for="end_date" class="block font-semibold leading-none mt-4">終了日付</label>
                <input type="date" id="end_date" name="end_date" value="{{ empty(old('end_date'))? \Carbon\Carbon::today(): old('start_date') }}" class="block">

                <x-primary-button class="mt-4">
                    送信する
                </x-primary-button>
                
            </form>
        </div>
    </div>
    {{-- 最初に作成した部分ここまで --}}

</x-app-layout>