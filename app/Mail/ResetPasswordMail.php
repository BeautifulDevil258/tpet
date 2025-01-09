<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
{
    $resetLink = url('password/reset/' . $this->token); // Tạo liên kết với token

    return $this->view('emails.reset_password')
                ->with([
                    'resetLink' => $resetLink,
                    'email' => $this->email,
                ]);
}
}

