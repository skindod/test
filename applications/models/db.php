<?php

/**
 * Class for database connection
 * 
 * @author Syed Zulazri <zulazri@secretlab.media>
 * @copyright (c) 2015, Secretlab.Media Sdn Bhd (Project: simplepay)
 */

class db
{
    public $db_handler, $stmt_handler, $error_mode, $last_insert_id, $error_log, $transaction = false;
    private $host, $dbname, $login, $password;
    
    /**
     * Constructor. This will set the required parameter values to make a connection
     * 
     */
    function __construct()
    {
        global $slm_db;
        
        $this->host         = $slm_db['host'];
        $this->dbname       = $slm_db['name'];
        $this->login        = $slm_db['login'];
        $this->password     = $slm_db['password'];
        $this->error_mode   = $slm_db['error_mode'];
        $this->error_log    = $slm_db['error_log'];
    }
    
    /**
     * Function to open the database connection
     * this is only meant to be call internally
     */
    private function open_conn()
    {
        global $slm_db;

        try
        {
            # MySQL with PDO_MYSQL, open the connection using persistent connection
            $this->db_handler = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname, $this->login, $this->password, array(PDO::ATTR_PERSISTENT => true));

            # we set the error mode and error log
            $this->error_mode = $slm_db['error_mode'];
            $this->error_log = $slm_db['error_log'];
        }
        catch (PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }

    }
    
    /**
     * Function to close the database connection
     * most of the time this is not needed as we are using persistent connection
     * this is meant to be call internally
     */
    private function close_conn()
    {
        # close the connection, not needed most of the time as we use persistent
        $this->db_handler = null;
    }
    
    /**
     * Function to set the connection error mode
     * this is meant to be call internally
     * 
     * if not set we set by default as silent
     */
    private function set_error_mode()
    {
        if($this->error_mode == 'silent')
            $this->db_handler->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
        else if($this->error_mode == 'warning')
            $this->db_handler->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        else if($this->error_mode == 'exception')
            $this->db_handler->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        else
            $this->db_handler->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
    }
    
    /**
     * Function for select statement
     * result will be returned in array or boolean false if error
     * 
     * @param string $query The query string
     * 
     * @return array Result of the query
     */
    function get_result($query)
    {
        # this will be the array that hold the result
        $result = array();

        try
        {
            # open the connection if not open manually
            if(!$this->transaction)
                $this->open_conn();

            # execute the statement
            $this->stmt_handler = $this->db_handler->prepare($query);
            $this->stmt_handler->execute();
        
            # setting the fetch mode
            $this->stmt_handler->setFetchMode(PDO::FETCH_ASSOC);
            
            # format the result in array
            while($row = $this->stmt_handler->fetch())
                $result[] = $row;

            # return the result
            return $result;
        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Function for select statement
     * result will be returned in array or boolean false if error
     * 
     * @param string $table Table name
     * @param array $return_field The field required from the result
     * @param array $data Data array which array with key field (the field name) value (field value) operator (=, >, <, =<, >=) and type (int or string)
     * @param array $order Order by statement with array field for the field and value for either desc or asc
     * @param boolean $distinct Set to true if we want to set distinct to the return field, default is false
     * 
     * @return array Result of the query
     */
    function query($table, $return_field, $data, $order = array(), $distinct = false)
    {
        # this will be the array that hold the result
        $result = array();

        try
        {
            # if table if not provided then return immediately
            if($table == '')
                return false;
            
            # open the connection if not open manually
            if(!$this->transaction)
                $this->open_conn();

            # build the field to return
            $get_field = '';
            foreach($return_field as $field)
            {
                if($get_field != '')
                    $get_field .= ','.$field;
                else
                    $get_field .= $field;
            }
            # if not provided then return all fields
            if($get_field == '')
                $get_field = '*';
            
            # build the where clause
            $where = '';
            foreach($data as $record)
            {
                if($where != '')
                {
                    # we change the bindparam method to use ? placeholder instead of named placeholder  due to issue with 2 same field
                    //$where .= ' AND '.$record['field'].' '.$record['operator'].' :'.$record['field'];
                    $where .= ' AND '.$record['field'].' '.$record['operator'].' ?';
                }
                else
                {
                    # we change the bindparam method to use ? placeholder instead of named placeholder  due to issue with 2 same field
                    //$where .= ' WHERE '.$record['field'].' '.$record['operator'].' :'.$record['field'];
                    $where .= ' WHERE '.$record['field'].' '.$record['operator'].' ?';
                }
            }
            
            # build the order by clause
            $order_stmt = '';
            if(count($order) > 0)
            {
                if(count($order['field']) > 0)
                {
                    $field_to_order = '';
                    foreach($order['field'] as $order_field)
                    {
                        if($field_to_order == '')
                            $field_to_order .= $order_field;
                        else
                            $field_to_order .= ','.$order_field;
                    }
                    if($field_to_order != '')
                    {
                        if(isset($order['value']) && ($order['value'] == 'desc' || $order['value'] == 'asc'))
                            $order_stmt .= ' ORDER BY '.$field_to_order.' '.$order['value'];
                        else
                            $order_stmt .= ' ORDER BY '.$field_to_order;
                    }
                }
            }
            
            if($distinct)
                $distinct_str = 'distinct ';
            else
                $distinct_str = '';
      
            # build the complete SQL
            $sql = 'SELECT '.$distinct_str.$get_field.' FROM '.$table.' '.$where.' '.$order_stmt;

            # execute the statement
            $stmt = $this->db_handler->prepare($sql);

            # bind the value to the placeholder in SQL
            $bindparam = array();
            $bindparam_type = array();

            # we determine the type of data either string or int
            $counter = 1;
            foreach($data as $record)
            {
                $bindparam[$counter] = $record['value'];
                if($record['type'] == 'int')
                    $bindparam_type[$counter] = PDO::PARAM_INT;
                else if($record['type'] == 'string')
                    $bindparam_type[$counter] = PDO::PARAM_STR;
                else
                    $bindparam_type[$counter] = PDO::PARAM_STR;
                
                $counter++;
            }

            foreach($bindparam as $param_key => &$param_value)
                $stmt->bindParam($param_key, $param_value, $bindparam_type[$param_key]);
            
            # execute the statement
            if($stmt->execute())
            {
                # setting the fetch mode
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                
                # format the result in array
                while($row = $stmt->fetch())
                    $result[] = $row;
                return $result;
            }
            else
                return false;
        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Function for insert
     * input parameters will be table name and array of field value pair (array in array)
     * return the number of affected row or false on error
     * 
     * @param string $table Table name
     * @param array $data_array Data array
     * 
     * @return int Integer if true and false if failed
     */
    function insert($table, $data_array)
    {
        try
        {
            # open the connection if it was not opened manually
            if(!$this->transaction)
                $this->open_conn();

            # format the field value pair for sql stmt
            $counter_data_array = 0;
            $value_string = '';
            $bindparam = array();
            foreach($data_array as $field_value)
            {
                if($counter_data_array > 0)
                    $value_string .= ',(';
                else
                    $value_string .= '(';
                
                $counter = 0;
                foreach($field_value as $key => $item)
                {
                    if($counter > 0)
                    {
                        $field .= ", ";
                        $value .= ", ";
                    }
                    else
                    {
                        $field = "";
                        $value = "";
                    }
                    $field .= $key;
                    $value .= ':'.$key;
                    
                    # handle empty string and null, for now store a space, if not it will return error
                    if($item == '' || $item == null)
                        $bindparam[':'.$key] = ' ';
                    else
                        $bindparam[':'.$key] = $item;
                    $counter ++;
                }
                $value_string .= $value.')';
                $counter_data_array++;
            }
            
            # changing the way query executed due to sql injection
            $sql = "INSERT INTO ".$table." (".$field.") values ".$value_string;

            # execute the statement
            //$result = $this->db_handler->exec($sql);
            $stmt = $this->db_handler->prepare($sql);
            
            # bind the value to the placeholder in SQL
            foreach($bindparam as $param_key => &$param_value)
                $stmt->bindParam($param_key, $param_value);
            
            # execute the statement
            if($stmt->execute())
            {
                $this->last_insert_id = $this->db_handler->lastInsertId();
                return 1;
            }
            else
                return false;

        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Function to return the id (auto increment) for last insert statement
     */
    function get_last_insert_id()
    {
        return $this->last_insert_id;
    }
    
    /**
     * Function for delete
     * input parameters will be table name and field value pair (array)
     * return the number of affected row or false on error
     * 
     * @param string $table Table name
     * @param array $field_value Field and value
     * 
     * @return int Integer if successful or false on error
     */
    function delete($table, $field_value)
    {   
        try
        {
            # open the connection if it was not opened manually
            if(!$this->transaction)
                $this->open_conn();
            
            # format the field value pair for sql stmt
            $bindparam = array();
            $counter = 0;
            foreach($field_value as $key => $item)
            {
                if($counter > 0)
                    $condition .= " AND ";
                else
                    $condition = "";
                $condition .= $key."=:".$key;
                $bindparam[':'.$key] = $item;
                $counter ++;
            }
            
            # prepare the complete SQL
            $sql = 'DELETE FROM '.$table." WHERE ".$condition;
            
            # execute the statement
            //$result = $this->db_handler->exec($sql);
            $stmt = $this->db_handler->prepare($sql);
            
            # bind the value to the placeholder in SQL
            foreach($bindparam as $param_key => &$param_value)
                $stmt->bindParam($param_key, $param_value);
            
            # execute the statement
            if($stmt->execute())
                return 1;
            else
                return false;

        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Function for update
     * input parameters will be table name and field value pair (array)
     * return the number of affected row or false on error
     * 
     * @param string $table Table name
     * @param array $field_value Field and value pair
     * @param array $condition_field_value Condition value pair
     * 
     * @return int Integer if true and false if failed
     */
    function update($table, $field_value, $condition_field_value)
    {   
        try
        {
            # open the connection if it was not opened manually
            if(!$this->transaction)
                $this->open_conn();

            $bindparam_field_value = array();
            $bindparam_condition_field_value = array();
            
            # format the field value pair for sql stmt
            $counter = 0;
            foreach($field_value as $key => $item)
            {
                if($counter > 0)
                    $update_field .= ", ";
                else
                    $update_field = "";
                $update_field .= $key."=:".$key;
                
                # handle empty string and null, for now store a space, if not it will return error
                if($item == '' || $item == null)
                    $bindparam_field_value[':'.$key] = ' ';
                else
                    $bindparam_field_value[':'.$key] = $item;
                $counter ++;
            }
            
            # format the field value pair for condition for sql stmt
            $counter = 0;
            foreach($condition_field_value as $key => $item)
            {
                if($counter > 0)
                    $condition_field .= " AND ";
                else
                    $condition_field = "";
                $condition_field .= $key."=:".$key;
                $bindparam_condition_field_value[':'.$key] = $item;
                $counter ++;
            }
            
            # build the complete SQL
            $sql = 'UPDATE '.$table." SET ".$update_field." WHERE ".$condition_field;
            
            # prepare the statement
            $stmt = $this->db_handler->prepare($sql);
            
            # bind the value to the placeholder in SQL
            foreach($bindparam_field_value as $param_key => &$param_value)
                $stmt->bindParam($param_key, $param_value);
            foreach($bindparam_condition_field_value as $param_key => &$param_value)
                $stmt->bindParam($param_key, $param_value);
            
            # execute the statement
            if($stmt->execute())
                return 1;
            else
                return false;

        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Manually open the transaction
     */
    function open_transaction()
    {
        try
        {
            $this->open_conn();
            //$this->db_handler->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $result = $this->db_handler->beginTransaction();
            $this->transaction = true;
            
        }
        catch(PDOException $e)
        {
            error_log($e->getMessage().PHP_EOL, 3, $this->error_log);
        }
        return $result;
    }
    
    /**
     * Manually commit the chnges
     */
    function commit_transaction()
    {
        try
        {
            $result = $this->db_handler->commit();
            $this->transaction = false;
            return $result;
        }
        catch(PDOException $e)
        {
            error_log(date('j-m-Y G:i').': Commit transaction error -> '.$e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
    
    /**
     * Manually rollback the changes
     */
    function rollback_transaction()
    {
        try
        {
            $result = $this->db_handler->rollBack();
            $this->transaction = false;
            return $result;
        }
        catch(PDOException $e)
        {
            error_log(date('j-m-Y G:i').': Rollback transaction error -> '.$e->getMessage().PHP_EOL, 3, $this->error_log);
        }
    }
}
?>
