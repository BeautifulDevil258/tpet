<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The account instance (User or Admin).
     *
     * @var mixed
     */
    protected $account;

    /**
     * Create a new message instance.
     *
     * @param mixed $account User or Admin instance
     * @return void
     */
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $accountType = $this->account instanceof \App\Models\Admin ? 'Admin' : 'User';

        return $this->subject("Thông Báo: {$accountType} Đổi Mật Khẩu Thành Công")
                    ->view('emails.password_changed')
                    ->with([
                        'account' => $this->account,
                        'accountType' => $accountType,
                    ]);
    }
}
