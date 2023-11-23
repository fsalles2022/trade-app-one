<?php

declare(strict_types=1);

namespace TradeAppOne\Mail;

use Illuminate\Mail\Mailable;
use TradeAppOne\Domain\Models\Tables\User;

class UserPasswordChangedMail extends Mailable
{

    /**
     * @var string
     */
    private $hashedPassword;

    /**
     * @var User
     */
    private $user;

    /**
     * @var bool
     */
    private $userExists;

    public function __construct(User $user, string $hashedPassword, bool $userExists = true)
    {
        $this->hashedPassword = $hashedPassword;
        $this->user           = $user;
        $this->userExists     = $userExists;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = 'Acesso TradeAppOne';

        if (! $this->userExists) {
            $title = 'Primeiro acesso TradeAppOne';
        }

        return $this
            ->subject($title)
            ->to($this->user->email)
            ->view('emails.verify', [
                'hashedPassword' => $this->hashedPassword,
                'userExists' => $this->userExists,
                'name' => $this->getUserFullName(),
                "cpf" => $this->getUserCpf(),
            ]);
    }

    private function getUserFullName(): string
    {
        return "{$this->user->firstName} {$this->user->lastName}";
    }

    private function getUserCpf(): string
    {
        return mb_substr($this->user->cpf, 0, 3) . ".***.***-**";
    }
}
