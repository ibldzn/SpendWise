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

    public function __construct()
    {
        $this->pdo = Database::getInstance();
        $this->resetQuery();
    }

    private function resetQuery(): void
    {
        $this->query = [
            'select' => '*',
            'where' => [],
            'limit' => null,
            'offset' => null
        ];
        $this->bindings = [];
    }

    public function select(string|array $columns): static
    {
        $this->query['select'] = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    /**
     * @param array<string,mixed> $conditions
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

    public function limit(int $limit): static
    {
        $this->query['limit'] = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->query['offset'] = $offset;
        return $this;
    }

    public function fetch(): array
    {
        $sql = $this->buildQuery();
        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);
        $this->resetQuery();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array<string,mixed> $data
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
     * @param array<int,mixed> $data
     * @param array<int,mixed> $where
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
     * @param array<int,mixed> $where
     */
    public function delete(array $where): bool
    {
        $whereClause = $this->buildWhereClause($where);
        $sql = "DELETE FROM {$this->table} WHERE $whereClause";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($this->bindings);
        return $statement->rowCount() > 0;
    }

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

        return $sql;
    }

    /**
     * @param array<string,mixed> $conditions
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
}
