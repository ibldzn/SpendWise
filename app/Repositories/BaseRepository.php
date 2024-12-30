<?php

namespace App\Repositories;

use App\Database\Database;
use PDO;

class BaseRepository
{
    protected string $table = '';
    protected PDO $pdo;
    private array $query;
    private array $bindings = [];

    /**
     * BaseRepository constructor.
    */
    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->resetQuery();
    }

    /**
     * Reset the query builder
    */
    private function resetQuery(): void
    {
        $this->query = [
            'select' => '*',
            'where' => [],
            'limit' => null,
            'offset' => null,
            'lock' => null,
        ];
        $this->bindings = [];
    }

    /**
     * Lock the selected rows for update
     *
     * @return static
    */
    public function lockForUpdate(): static
    {
        $this->query['lock'] = 'FOR UPDATE';
        return $this;
    }

    /**
     * Lock the selected rows for share
     *
     * @return static
    */
    public function lockForShare(): static
    {
        $this->query['lock'] = 'LOCK IN SHARE MODE';
        return $this;
    }

    /**
     * Select the columns to fetch
     *
     * @param string|array $columns The columns to select
     * @return static
    */
    public function select(string|array $columns): static
    {
        $this->query['select'] = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * Add a where clause to the query
     *
     * @param array<string,mixed> $conditions The conditions to add to the where clause, where the key is the column and the value is the value to match. Multiple conditions are treated as an OR whereas multiple keys in a condition are treated as an AND
     * @return static
     */
    public function where(array ...$conditions): static
    {
        foreach ($conditions as $condition) {
            $whereClause = [];
            foreach ($condition as $column => $value) {
                $placeholder = ':' . $column . '_' . count($this->bindings);
                $whereClause[] = "$column = $placeholder";
                $this->bindings[$placeholder] = $value;
            }
            $this->query['where'][] = '(' . implode(' AND ', $whereClause) . ')';
        }
        return $this;
    }

    /**
     * Limit the number of rows to fetch
     *
     * @param int $limit The number of rows to fetch
     * @return static
    */
    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    /**
     * Offset the rows to fetch
     *
     * @param int $offset The number of rows to skip
     * @return static
    */
    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    /**
     * Fetch all rows that match the query
     *
     * @return array The rows that match the query
    */
    public function fetch(): array
    {
        $sql = $this->buildQuery();
        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);
        $this->resetQuery();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch the first row that matches the query
     *
     * @return array|null The first row that matches the query, or null if no rows match
    */
    public function first(): ?array
    {
        $sql = $this->buildQuery();
        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);
        $this->resetQuery();
        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Create a new row in the table
     *
     * @param array<string,mixed> $data The data to insert into the table, where the key is the column and the value is the value to insert
     * @return int The ID of the newly created row
    */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn ($key) => ":$key", array_keys($data)));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);
        return $this->pdo->lastInsertId();
    }

    /**
     * Update rows in the table
     *
     * @param array<string,mixed> $data The data to update in the table, where the key is the column and the value is the value to update
     * @param array<string,mixed> $where The conditions to match the rows to update, where the key is the column and the value is the value to match
     * @return bool true if the update was successful, false otherwise
    */
    public function update(array $data, array $where): bool
    {
        $setClause = implode(', ', array_map(fn ($column) => "$column = :$column", array_keys($data)));
        $whereClause = $this->buildWhereClause($where);

        $sql = "UPDATE {$this->table} SET $setClause WHERE $whereClause";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(array_merge($data, $this->bindings));
        return $statement->rowCount() > 0;
    }

    /**
     * Delete rows from the table
     *
     * @param array<string,mixed> $where The conditions to match the rows to delete, where the key is the column and the value is the value to match
     * @return bool true if the delete was successful, false otherwise
    */
    public function delete(array $where): bool
    {
        $whereClause = $this->buildWhereClause($where);
        $sql = "DELETE FROM {$this->table} WHERE $whereClause";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);
        return $statement->rowCount() > 0;
    }

    /**
     * Build the SQL query
     *
     * @return string The SQL query
    */
    private function buildQuery(): string
    {
        $sql = "SELECT {$this->query['select']} FROM {$this->table}";

        if (!empty($this->query['where'])) {
            $sql .= ' WHERE ' . implode(' OR ', $this->query['where']);
        }

        if ($this->query['limit']) {
            $sql .= " LIMIT {$this->query['limit']}";
        }

        if ($this->query['offset']) {
            $sql .= " OFFSET {$this->query['offset']}";
        }

        if ($this->query['lock']) {
            $sql .= " {$this->query['lock']}";
        }

        return $sql;
    }

    /**
     * Build the WHERE clause for the query
     *
     * @param array<string,mixed> $conditions The conditions to match the rows, where the key is the column and the value is the value to match
     * @return string The WHERE clause
    */
    private function buildWhereClause(array $conditions): string
    {
        $whereClause = [];
        foreach ($conditions as $column => $value) {
            $placeholder = ':' . $column . '_' . count($this->bindings);
            $whereClause[] = "$column = $placeholder";
            $this->bindings[$placeholder] = $value;
        }
        return implode(' AND ', $whereClause);
    }

    /**
     * Execute a transaction
     *
     * @param callable $callback The callback to execute within the transaction
     * @throws \Exception if the transaction fails
    */
    public function onTransaction(callable $callback): void
    {
        try {
            if (!$this->pdo->beginTransaction()) {
                throw new \Exception('Failed to start transaction');
            }
            $callback($this->pdo);
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
