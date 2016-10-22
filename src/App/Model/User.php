<?php
namespace App\Model;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\TableGateway\TableGateway;

class User
{
    protected $table;

    public function __construct(AdapterInterface $adapter)
    {
        $this->table  = new TableGateway('users', $adapter);
    }

    /**
     * Get all user
     *
     * @return array
     */
    public function getAll()
    {
        return $this->table->select()->toArray();
    }

    /**
     * Get a user by email
     *
     * @param $email string
     * @return bool|array
     */
    public function getUser($id)
    {
        $user = $this->table->select([ 'id' => $id ]);
        return $user ? (array) $user->current() : false;
    }

    /**
     * Add a user
     *
     * @param $data array
     * @return bool|integer
     */
    public function addUser(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $rows = $this->table->insert($data);
        return ($rows === 1) ? $this->table->lastInsertValue : false;
    }

    /**
     * Update a user
     *
     * @param integer $id
     * @param array $data
     * @return bool|array
     */
    public function updateUser($id, array $data)
    {
        $rows = $this->table->update($data, [ 'id' => $id ]);
        return ($rows === 1) ? $this->getUser($id) : false;
    }

    /**
     * Remove a user
     *
     * @param integer $id
     * @return bool
     */
    public function deleteUser($id)
    {
        $rows = $this->table->delete([ 'id' => $id ]);
        return ($rows === 1);
    }
}
