<?php
namespace App\User;

use DomainException;
use PDOException;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbTableGateway;
use Zend\Paginator\Paginator;

class UserModel
{
    protected $table;

    public function __construct(AdapterInterface $adapter)
    {
        $resultSet = new HydratingResultSet();
        $resultSet->setObjectPrototype(new UserEntity());
        $this->table = new TableGateway('users', $adapter, null, $resultSet);
    }

    /**
     * Get all user
     */
    public function getAll(): Paginator
    {
        $dbTableGatewayAdapter = new DbTableGateway($this->table);
        $paginator = new UserCollection($dbTableGatewayAdapter);
        return $paginator;
    }

    /**
     * Get a user by $id
     */
    public function getUser(int $id): ?UserEntity
    {
        $user = $this->table->select([ 'id' => $id ]);
        $result = $user->current();
        if ($result instanceof UserEntity) {
            return $result;
        }
        return null;
    }

    /**
     * Add a user with $data values
     */
    public function addUser(array $data): ?int
    {
        if (!isset($data['email'])) {
            throw new DomainException('Email is a required field');
        }
        if (!isset($data['password'])) {
            throw new DomainException('Password is a required field');
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $rows = $this->table->insert($data);
        return ($rows === 1) ? $this->table->lastInsertValue : null;
    }

    /**
     * Update the user with $id and $data
     */
    public function updateUser(int $id, array $data): ?UserEntity
    {
        try {
            $rows = $this->table->update($data, [ 'id' => $id ]);
        } catch (PDOException $e) {
            throw new DomainException($e->getMessage());
        }
        return ($rows === 1) ? $this->getUser($id) : null;
    }

    /**
     * Remove the user with $id
     */
    public function deleteUser($id): bool
    {
        $rows = $this->table->delete([ 'id' => $id ]);
        return ($rows === 1);
    }
}
