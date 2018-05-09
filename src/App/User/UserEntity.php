<?php
namespace App\User;

class UserEntity
{
    public $id;
    public $name;
    public $email;
    public $password;

    public function getArrayCopy()
    {
        return array(
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password
        );
    }

    public function exchangeArray(array $array)
    {
        $this->id       = $array['id'];
        $this->name     = $array['name'];
        $this->email    = $array['email'];
        $this->password = $array['password'];
    }
}
