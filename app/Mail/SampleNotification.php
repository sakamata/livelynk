<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SampleNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;
    protected $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name='Mr.テスト', $text='テストです')
    {
        $this->title = sprintf('%sさん、こんにちは。ここは件名', $name);
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
     // view()メソッドでHTMLメールのビューをセット
     // text()メソッドで平文メールのビューをセット
     // subject()メソッドでメールのタイトルをセット
     // with()メソッドでビューに渡す変数をセット

    public function build()
    {
        return $this->view('emails.sample_notification')
            ->text('emails.sample_notification_plain')
            ->subject($this->title)
            ->with([
                'text' => $this->text,
            ]);
    }
}
