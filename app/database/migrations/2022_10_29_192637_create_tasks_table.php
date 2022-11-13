<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // 指摘事項ユーザーが消えたらタスクは残って、誰かが引き継ぐとかしないといけないと思うので、タスクが消えちゃうのはまずいかと！set nullあたりが妥当かと思います。
            // readoubleになかった　laracastsで調べた https://laracasts.com/discuss/channels/eloquent/in-migrations-how-i-can-use-something-like-set-null?page=1&replyId=313544
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('attached_file_path')->nullable();
            $table->enum('status', ['notstarted', 'doing', 'done'])->default('notstarted');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->softDeletes('deleted_at');
            $table->timestamps();
            // 外部キー制約
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
