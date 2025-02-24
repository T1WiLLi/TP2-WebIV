<?php

use Zephyrus\Database\DatabaseBroker;

/**
 * @template T of object
 */
abstract class BaseBroker extends DatabaseBroker
{
    protected string $tableName;
    protected string $entityClass;

    /**
     * @param string $tableName
     * @param class-string<T> $entityClass
     */
    public function __construct(string $tableName, string $entityClass)
    {
        parent::__construct();
        $this->tableName = $tableName;
        $this->entityClass = $entityClass;
    }

    /**
     * Fetches all entities from the table.
     * 
     * @return array<T>
     */
    public function findAll(): array
    {
        return $this->select("SELECT * FROM {$this->tableName}", [], function ($row) {
            return $this->mapToEntity($row);
        });
    }

    /**
     * Finds an entity by its ID.
     * 
     * @param int $id
     * @return T|null
     */
    public function findById(int $id): ?object
    {
        $result = $this->selectSingle("SELECT * FROM {$this->tableName} WHERE id = ?", [$id]);
        return $result ? $this->mapToEntity($result) : null;
    }

    /**
     * Inserts a new entity into the database.
     * 
     * @param T $entity
     * @return int The ID of the newly inserted entity
     */
    public function insert(object $entity): int
    {
        $columns = [];
        $values = [];
        $params = [];

        foreach (get_object_vars($entity) as $key => $value) {
            if ($key !== 'id') {
                $columns[] = $key;
                $values[] = "?";
                $params[] = $value;
            }
        }

        $query = "INSERT INTO {$this->tableName} (" . implode(", ", $columns) . ") 
                  VALUES (" . implode(", ", $values) . ") RETURNING id";

        return $this->selectSingle($query, $params)->id;
    }

    /**
     * Updates an existing entity in the database.
     * 
     * @param T $entity
     * @return int Number of affected rows
     */
    public function update(object $entity): int
    {
        $sets = [];
        $params = [];

        foreach (get_object_vars($entity) as $key => $value) {
            if ($key !== 'id') {
                $sets[] = "$key = ?";
                $params[] = $value;
            }
        }

        $params[] = $entity->id;

        $query = "UPDATE {$this->tableName} 
                  SET " . implode(", ", $sets) . " 
                  WHERE id = ?";

        $this->query($query, $params);
        return $this->getLastAffectedCount();
    }

    /**
     * Deletes an entity from the database.
     * 
     * @param T $entity
     * @return int Number of affected rows
     */
    public function delete(object $entity): int
    {
        $this->query("DELETE FROM {$this->tableName} WHERE id = ?", [$entity->id]);
        return $this->getLastAffectedCount();
    }

    /**
     * Maps a database row to an entity object.
     * 
     * @param stdClass $row
     * @return T
     */
    protected function mapToEntity(stdClass $row): object
    {
        $entity = new $this->entityClass();
        foreach (get_object_vars($row) as $property => $value) {
            $entity->$property = $value;
        }
        return $entity;
    }

    /**
     * Gets the table name.
     * 
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Gets the entity class name.
     * 
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
