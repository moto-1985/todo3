<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Message extends Component
{
    // ここから編集した部分
    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // この部分は編集不要
    public function render()
    {
        return view('components.message');
    }
}

