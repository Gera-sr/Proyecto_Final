<?php
/**
 * CRUD library
 * 
 * @author Isaacrml08
 * @version 0.1
**/

class myConnexion {
    private $__host;
    private $__db_name;
    private $__user;
    private $__passwd;
    private $__port;
    private $__db_connection;

    public function __construct($host, $db_name, $user, $passwd, $port)
    {
        $this->__host = $host;
        $this->__db_name = $db_name;
        $this->__user = $user;
        $this->__passwd = $passwd;
        $this->__port = $port;
    }

    public function connect()
    {
        $this->__db_connection = new mysqli(
            $this->__host,
            $this->__user,
            $this->__passwd,
            $this->__db_name,
            $this->__port
        );

        if($this->__db_connection->connect_error)
        {
            return false;
        }

        return $this->__db_connection;
    }

    // Does what you think it does
    public function get_connection()
    {
        return $this->__db_connection;
    }

    public function close_connection()
    {
        if($this->__db_connection)
        {
            $this->__db_connection->close();
        }
    }
};

class sqlHelper {
    private $__table_name;
    private $__sql_connection;

    public function __construct($table_name, $connection)
    {
        $this->__table_name = $table_name;
        $this->__sql_connection = $connection;
    }

    /**
     * @param array $row_data an string array of the data to insert in the provided columns
     * @example $row_data = [ 'user' => 'Champinion', 'color' => 'dark-red' ];
     * @return insert_id|false in case of succees or failure
    **/
    public function insert_into(array $row_data)
    {
        if (empty($row_data))
        {
            throw new InvalidArgumentException('Row data cannot be empty');
        }
        
        $columns = array_keys($row_data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->__table_name,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $statement = $this->__sql_connection->prepare($sql);
        if (!$statement)
        {
            return false;
        }
        
        $types = str_repeat('s', count($row_data));
        $values = array_values($row_data);
        $statement->bind_param($types, ...$values);
        
        if (!$statement->execute())
        {
            throw new RuntimeException("Execution failed: " . $statement->error);
        }
        
        $insertId = $this->__sql_connection->insert_id;
        $statement->close();
        
        return $insertId;
    }

    /**
     * Select records from the table
     * @param array $columns Columns to select (empty array = all columns)
     * @param array $where WHERE conditions ['column' => 'value'] or ['column' => ['operator' => 'value']]
     * @param array $order_by ORDER BY clause ['column' => 'ASC|DESC']
     * @param int|null $limit LIMIT clause
     * @param int|null $offset OFFSET clause
     * @return array|false Returns array of results or false on failure
    **/
    public function select(
        array $columns = [], 
        array $where = [], 
        array $order_by = [], 
        $limit = null, 
        $offset = null
    ) {
        $select_clause = empty($columns) ? '*' : implode(', ', array_map(function($col) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                throw new InvalidArgumentException("Invalid column name: {$col}");
            }
            return "`{$col}`";
        }, $columns));

        $where_clause = '';
        $where_values = [];
        $where_types = '';

        if (!empty($where)) {
            $where_conditions = [];
            foreach ($where as $column => $condition) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                    throw new InvalidArgumentException("Invalid column name in WHERE: {$column}");
                }
                
                if (is_array($condition)) {
                    foreach ($condition as $operator => $value) {
                        $valid_operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN'];
                        if (!in_array($operator, $valid_operators)) {
                            throw new InvalidArgumentException("Invalid operator: {$operator}");
                        }
                        
                        if (in_array($operator, ['IN', 'NOT IN'])) {
                            if (!is_array($value)) {
                                $value = [$value];
                            }
                            $placeholders = implode(', ', array_fill(0, count($value), '?'));
                            $where_conditions[] = "`{$column}` {$operator} ({$placeholders})";
                            $where_values = array_merge($where_values, $value);
                            $where_types .= str_repeat('s', count($value));
                        } else {
                            $where_conditions[] = "`{$column}` {$operator} ?";
                            $where_values[] = $value;
                            $where_types .= 's';
                        }
                    }
                } else {
                    $where_conditions[] = "`{$column}` = ?";
                    $where_values[] = $condition;
                    $where_types .= 's';
                }
            }
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }

        $order_clause = '';
        if (!empty($order_by)) {
            $order_conditions = [];
            foreach ($order_by as $column => $direction) {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                    throw new InvalidArgumentException("Invalid column name in ORDER BY: {$column}");
                }
                $direction = strtoupper($direction);
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    throw new InvalidArgumentException("Invalid ORDER BY direction: {$direction}");
                }
                $order_conditions[] = "`{$column}` {$direction}";
            }
            $order_clause = 'ORDER BY ' . implode(', ', $order_conditions);
        }

        $limit_clause = '';
        if ($limit !== null) {
            if (!is_int($limit) || $limit < 0) {
                throw new InvalidArgumentException("LIMIT must be a positive integer");
            }
            $limit_clause = "LIMIT ?";
            $where_values[] = $limit;
            $where_types .= 'i';
            
            if ($offset !== null) {
                if (!is_int($offset) || $offset < 0) {
                    throw new InvalidArgumentException("OFFSET must be a positive integer");
                }
                $limit_clause .= " OFFSET ?";
                $where_values[] = $offset;
                $where_types .= 'i';
            }
        }

        $sql = "SELECT {$select_clause} FROM `{$this->__table_name}`";
        $sql .= $where_clause ? " {$where_clause}" : "";
        $sql .= $order_clause ? " {$order_clause}" : "";
        $sql .= $limit_clause ? " {$limit_clause}" : "";

        try {
            $stmt = $this->__sql_connection->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Prepare failed: " . $this->__sql_connection->error);
            }

            if (!empty($where_values)) {
                $stmt->bind_param($where_types, ...$where_values);
            }

            if (!$stmt->execute()) {
                throw new RuntimeException("Execution failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            
            $stmt->close();
            
            return $rows;
            
        } catch (Exception $e) {
            error_log("Select error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Select a single record
     * @param array $columns Columns to select
     * @param array $where WHERE conditions
     * @return array|false Single row or false
     */
    public function selectOne(array $columns = [], array $where = [])
    {
        $results = $this->select($columns, $where, [], 1);
        return $results ? $results[0] : false;
    }

    /**
     * Count records
     * @param array $where WHERE conditions
     * @return int Count of records
     */
    public function count(array $where = [])
    {
        $result = $this->select(['COUNT(*) as count'], $where);
        return $result ? (int)$result[0]['count'] : 0;
    }
    
    /**
     * Update records in the table
     * @param array $data Data to update ['column' => 'value']
     * @param array $where WHERE conditions (REQUIRED for safety)
     * @param int|null $limit LIMIT clause
     * @return int|false Number of affected rows or false on failure
    **/
    public function update(array $data, array $where, $limit = null)
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Update data cannot be empty');
        }
        
        if (empty($where)) {
            throw new InvalidArgumentException('WHERE clause is required for update operations');
        }

        $set_clause = [];
        $set_values = [];
        $types = '';

        foreach ($data as $column => $value) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException("Invalid column name: {$column}");
            }
            $set_clause[] = "`{$column}` = ?";
            $set_values[] = $value;
            $types .= 's';
        }

        $where_clause = '';
        $where_values = [];

        $where_conditions = [];
        foreach ($where as $column => $condition) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException("Invalid column name in WHERE: {$column}");
            }
            
            if (is_array($condition)) {
                foreach ($condition as $operator => $value) {
                    $valid_operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE'];
                    if (!in_array($operator, $valid_operators)) {
                        throw new InvalidArgumentException("Invalid operator: {$operator}");
                    }
                    $where_conditions[] = "`{$column}` {$operator} ?";
                    $where_values[] = $value;
                    $types .= 's';
                }
            } else {
                $where_conditions[] = "`{$column}` = ?";
                $where_values[] = $condition;
                $types .= 's';
            }
        }
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

        $limit_clause = '';
        if ($limit !== null) {
            if (!is_int($limit) || $limit < 0) {
                throw new InvalidArgumentException("LIMIT must be a positive integer");
            }
            $limit_clause = "LIMIT ?";
            $where_values[] = $limit;
            $types .= 'i';
        }

        $all_values = array_merge($set_values, $where_values);

        $sql = "UPDATE `{$this->__table_name}` SET " . implode(', ', $set_clause);
        $sql .= " {$where_clause}";
        $sql .= $limit_clause ? " {$limit_clause}" : "";

        try {
            $stmt = $this->__sql_connection->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Prepare failed: " . $this->__sql_connection->error);
            }

            $stmt->bind_param($types, ...$all_values);

            if (!$stmt->execute()) {
                throw new RuntimeException("Execution failed: " . $stmt->error);
            }

            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $affected_rows;
            
        } catch (Exception $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete records from the table
     * @param array $where WHERE conditions (REQUIRED for safety)
     * @param int|null $limit LIMIT clause
     * @return int|false Number of affected rows or false on failure
    **/
    public function delete(array $where, $limit = null)
    {
        if (empty($where)) {
            throw new InvalidArgumentException('WHERE clause is required for delete operations');
        }

        $where_clause = '';
        $where_values = [];
        $types = '';

        $where_conditions = [];
        foreach ($where as $column => $condition) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
                throw new InvalidArgumentException("Invalid column name in WHERE: {$column}");
            }
            
            if (is_array($condition)) {
                foreach ($condition as $operator => $value) {
                    $valid_operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE'];
                    if (!in_array($operator, $valid_operators)) {
                        throw new InvalidArgumentException("Invalid operator: {$operator}");
                    }
                    $where_conditions[] = "`{$column}` {$operator} ?";
                    $where_values[] = $value;
                    $types .= 's';
                }
            } else {
                $where_conditions[] = "`{$column}` = ?";
                $where_values[] = $condition;
                $types .= 's';
            }
        }
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

        $limit_clause = '';
        if ($limit !== null) {
            if (!is_int($limit) || $limit < 0) {
                throw new InvalidArgumentException("LIMIT must be a positive integer");
            }
            $limit_clause = "LIMIT ?";
            $where_values[] = $limit;
            $types .= 'i';
        }

        $sql = "DELETE FROM `{$this->__table_name}` {$where_clause}";
        $sql .= $limit_clause ? " {$limit_clause}" : "";

        try {
            $stmt = $this->__sql_connection->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException("Prepare failed: " . $this->__sql_connection->error);
            }

            if (!empty($where_values)) {
                $stmt->bind_param($types, ...$where_values);
            }

            if (!$stmt->execute()) {
                throw new RuntimeException("Execution failed: " . $stmt->error);
            }

            $affected_rows = $stmt->affected_rows;
            $stmt->close();
            
            return $affected_rows;
            
        } catch (Exception $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    function close()
    {
        if ($this->__sql_connection)
        {
            $this->__sql_connection->close();
        }
    }
};

/*
// Example usage
$db = new myConnexion('localhost', 'test_db', 'user', 'password', 3306);
$connection = $db->connect();

$helper = new sqlHelper('users', $connection);

// INSERT
$userId = $helper->insert_into([
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'age' => 30
]);

// SELECT with conditions
$users = $helper->select(
    ['id', 'username', 'email'], // columns
    ['age' => ['>=' => 18], 'active' => 1], // where
    ['created_at' => 'DESC'], // order by
    10, // limit
    0   // offset
);

// SELECT single record
$user = $helper->selectOne(
    ['id', 'username'],
    ['id' => $userId]
);

// UPDATE
$affected = $helper->update(
    ['email' => 'new_email@example.com', 'active' => 0], // data
    ['id' => $userId] // where
);

// DELETE
$deleted = $helper->delete(
    ['active' => 0, 'last_login' => ['<' => '2023-01-01']], // where
    100 // limit
);

// COUNT
$totalUsers = $helper->count(['active' => 1]);

$db->close_connection();
*/
?>