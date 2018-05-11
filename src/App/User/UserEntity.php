<?php
declare(strict_types=1);

namespace App\User;

class UserEntity
{
    public $id;
    public $name;
    public $email;

    public function getArrayCopy()
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
        ];
    }

    public function exchangeArray(array $array)
    {
        $this->id    = $array['id'];
        $this->name  = $array['name'];
        $this->email = $array['email'];
    }
}
