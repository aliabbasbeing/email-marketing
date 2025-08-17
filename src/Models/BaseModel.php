<?php

namespace Models;

/**
 * Base Model Class
 * All models should extend this class
 */
abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $timestamps = true;
    
    public function __construct()
    {
        $this->db = \App::getInstance()->getDatabase();
    }
    
    /**
     * Find a record by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find all records
     */
    public function all($limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Find records with conditions
     */
    public function where($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Find first record with conditions
     */
    public function whereFirst($conditions, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Create a new record
     */
    public function create($data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add timestamps if enabled
        if ($this->timestamps) {
            $filteredData['created_at'] = date('Y-m-d H:i:s');
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = array_keys($filteredData);
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        
        return $this->find($this->db->lastInsertId());
    }
    
    /**
     * Update a record
     */
    public function update($id, $data)
    {
        // Filter data to only include fillable fields
        $filteredData = $this->filterFillable($data);
        
        // Add updated timestamp if enabled
        if ($this->timestamps) {
            $filteredData['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $fields = array_keys($filteredData);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($filteredData);
        $values[] = $id;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        
        return $this->find($id);
    }
    
    /**
     * Delete a record
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Count records
     */
    public function count($conditions = null, $params = [])
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Get paginated results
     */
    public function paginate($page = 1, $perPage = 15, $conditions = null, $params = [])
    {
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $this->count($conditions, $params);
        
        // Get records for current page
        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    /**
     * Execute custom query
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Execute custom query and return first result
     */
    public function queryFirst($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit()
    {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback()
    {
        return $this->db->rollBack();
    }
    
    /**
     * Filter data to only include fillable fields
     */
    private function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Validate data against rules
     */
    public function validate($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $ruleSet);
            
            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field] = ucfirst($field) . ' is required';
                        }
                        break;
                    
                    case 'unique':
                        if (!empty($value)) {
                            $existing = $this->whereFirst("{$field} = ?", [$value]);
                            if ($existing) {
                                $errors[$field] = ucfirst($field) . ' already exists';
                            }
                        }
                        break;
                    
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = ucfirst($field) . ' must be a valid email';
                        }
                        break;
                    
                    case 'min':
                        if (!empty($value) && strlen($value) < $ruleValue) {
                            $errors[$field] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;
                    
                    case 'max':
                        if (!empty($value) && strlen($value) > $ruleValue) {
                            $errors[$field] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
}