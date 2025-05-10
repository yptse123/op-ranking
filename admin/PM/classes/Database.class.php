<?php

class PM_Database
{
    protected $_readCon;
    protected $_writeCon;

    public function __construct()
    {
    }

    protected function _connectDb($host, $db, $username, $password)
    {
        return new PDO("mysql:host={$host};dbname={$db};charset=utf8", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    public function getReadCon()
    {
        if(!$this->_readCon)
        {
            $this->_readCon = $this->_connectDb(PM_Data_Setting_Database::DB_HOST, PM_Data_Setting_Database::DB_DBNAME, PM_Data_Setting_Database::DB_USERNAME, PM_Data_Setting_Database::DB_PASSWORD);
        }

        return $this->_readCon;
    }

    public function getWriteCon()
    {
        if(!$this->_writeCon)
        {
            $this->_writeCon = $this->_connectDb(PM_Data_Setting_Database::DB_HOST, PM_Data_Setting_Database::DB_DBNAME, PM_Data_Setting_Database::DB_USERNAME, PM_Data_Setting_Database::DB_PASSWORD);
        }

        return $this->_writeCon;
    }
    
    public function getDatabaseName()
    {
        $sql = "SELECT DATABASE();";
        $stmt = $this->getWriteCon()->query($sql);
        return $stmt->fetchColumn();
    }
	
	public function getWhereInBindData($key, $val){
		$conditions = array();
		$sqlBindData = array();
		$valueArray = array();
		
		if (!is_array($val["val"])) $valueArray[] = $val["val"];
		else $valueArray = $val["val"];
		$valueArray = array_values($valueArray);		//using numeric index
		
		$inParams = array_combine(
			array_map(
				function ($v) use ($key) {return ":{$key}_{$v}";},
				array_keys($valueArray)		
			),
			$valueArray
		);
		if ($inParams){
			$conditions[] = $key . ' ' . $val["oper"] . ' ('. implode(', ', array_keys($inParams)) .')';
			foreach ($inParams as $inParamsKey => $inParamsVal){
				$sqlBindData[$inParamsKey] = $inParamsVal;
			}
		}else{	//empty array, not set bind value, induce error
			$conditions[] = $key . ' ' . $val["oper"] . ' ('. ':'.$key . ')';
		}
		return array($conditions, $sqlBindData);
	}
    
    protected function _setConditions($condition_params, $conditions, $sqlBindData = array())
    {
        if($condition_params)
        {
            foreach($condition_params as $key => $val)
            {
                if(!is_array($val))
                {
                    $conditions[] = $key." = :".$key;
                    $sqlBindData[":{$key}"] = $val;
                }else{
					if (!empty($val["oper"])) $val["oper"] = strtolower($val["oper"]);
                    switch ($val["oper"]) {
                        case "<" :
                        case "<=" :
                        case ">" :
                        case ">=" :
                        case "<>" :
                        case "!=" :
                            $conditions[] = $key." ".$val["oper"]." :".$key;
                            $sqlBindData[":{$key}"] = $val["val"];
                            break;
                        case "expr" :
                            $conditions[] = $val["val"];
                            foreach($val["bind"] as $bind_key => $bind_val)
                                $sqlBindData[":{$bind_key}"] = $bind_val;
                            break;
						case "in":
						case "not in":
							//example
							// 'field_name' => array(
								// 'oper' => 'IN',
								// 'val' => array('value1','value2','value3'),
							// )
							list($inParamsConditions, $inParamsSqlBindData) = $this->getWhereInBindData($key, $val);
							foreach ($inParamsConditions as $k => $v){
								$conditions[] = $v;
							}
							foreach ($inParamsSqlBindData as $k => $v){
								$sqlBindData[$k] = $v;
							}
							break;
                        default :
                            $conditions[] = $key." = :".$key;
                            $sqlBindData[":{$key}"] = $val["val"];
                            break;
                    }
                }
            }
        }

        return array($conditions, $sqlBindData);
    }
    
    public function getCollection($table, $condition_params = array(), $order_by = null, $page = null, $limit = null, $group_by=null, $selectStatement=null)
    {
        try{
            $sqlBindData = array();
            $conditions = array("1");
            list($conditions, $sqlBindData) = $this->_setConditions($condition_params, $conditions, $sqlBindData);

            $sql = "SELECT ";
            if (!empty($selectStatement)){//$selectStatement should be string, e.g."SUM(field_name) as sum_field_name"
                $sql .= $selectStatement;
            }else{
                $sql .= "*";
            }
            $sql .= " FROM {$table} WHERE ".implode(" AND ", $conditions);

            if(!empty($group_by)){
                $sql.=" GROUP BY ".$group_by;
            }

            if(!empty($order_by))
            {
                $sql .= " ORDER BY ".$order_by;
            }

            $read = $this->getReadCon();

            if(!empty($page))
            {
                if(!empty($limit))
                {
                    $perPage = $limit;
                }
                else
                {
                    $perPage = 20;
                }
                
                $startAt = $perPage * ($page - 1);

                $sql .= " LIMIT ".$startAt.", ".$perPage;
            }
            
            $stmt = $read->prepare($sql);
            $stmt->execute($sqlBindData);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e)
        {
                echo $sql . "<br>" . $e->getMessage();
                die();
        }
    }
    
    public function getCollectionWithFields($table, $fields = array(), $condition_params = array(), $order_by = null, $conditionOper = "AND", $limit = null)
    {
        try{
            $sqlBindData = array();
            if($conditionOper == "AND")
            {
                $conditions = array("1");
            }
            else
            {
                $conditions = array();
            }
            list($conditions, $sqlBindData) = $this->_setConditions($condition_params, $conditions, $sqlBindData);
			if (empty($fields)) $fields = array("*");

            $sql = "SELECT ".implode(", ", $fields)." FROM {$table} WHERE ".implode(" ".$conditionOper." ", $conditions);

            if(!empty($order_by))
            {
                $sql .= " ORDER BY ".$order_by;
            }

            if(!empty($limit))
            {
                $sql .= " LIMIT ".$limit;
            }

            $read = $this->getReadCon();
            $stmt = $read->prepare($sql);
            $stmt->execute($sqlBindData);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e)
        {
                echo $sql . "<br>" . $e->getMessage();
                die();
        }
    }

    public function updateRow($table, $data, $condition_params = array())
    {
        try{

            // log
            if(!empty($condition_params))
            {

                $result = PM::getSingleton("Database")->getCollection($table, $condition_params);
                if(count($result))
                {
                    $dbData = reset($result);

                    $newData = array();
                    $oldData = array();

                    foreach ($data as $newKey => $newValue) 
                    {
                        if($newKey == "updated_at")
                            continue;
                        
                        foreach ($dbData as $oldKey => $oldValue) 
                        {
                            if($newKey == $oldKey && $newValue !== $oldValue && (!empty($newValue) && !empty($oldValue)))
                            {
                                $newData[$newKey] = $newValue;
                                $oldData[$oldKey] = $oldValue;
                            }
                        }
                    }
                }
            }

            $sql = "UPDATE {$table} SET ";
            $sqlBindData = array();
            foreach($data as $col => $val)
            {
                if(strpos($col, "_date") !== false ||  $col === 'dob')
                {
                    $sql .= "`{$col}` = ifnull(nullif(:{$col}, NULL), NULL) ,";

                    if(empty($val))
                    {
                        $sqlBindData[":{$col}"] = NULL;
                    }
                    else
                    {
                        if($val == "0000-00-00")
                        {
                            $sqlBindData[":{$col}"] = NULL;
                        }
                        else
                        {
                            $sqlBindData[":{$col}"] = $val;
                        }
                    }
                }
                else
                {
                    if($val === "0")
                    {
                        $sql .= "`{$col}` = '0' ,";
                    }
                    else
                    {
                        $sql .= "`{$col}` = :{$col} ,";
                        $sqlBindData[":{$col}"] = $val;
                    }
                }
            }
            $sql = rtrim($sql, ",");
            $sql .= " WHERE ";
            $conditions = array("1");
            list($conditions, $sqlBindData) = $this->_setConditions($condition_params, $conditions, $sqlBindData);
            $sql .= implode(" AND ", $conditions);

            $write = $this->getWriteCon();

            $stmt = $write->prepare($sql);
            $stmt->execute($sqlBindData);
            //print_r($stmt->errorInfo());
            return $stmt->rowCount();
        }
        catch(PDOException $e)
        {
            echo $sql . "<br>" . $e->getMessage();
            die();
        }
    }

    
    public function insertRow($table, $data, $ignore = true)
    {
        try{
            if($ignore)
                $ignoreStr = " IGNORE ";
            $sql = "INSERT ".$ignoreStr." INTO {$table} ";
            $sqlBindData = array();

            $columnStr = '';
            $valueStr = '';
            foreach($data as $col => $val)
            {
                    $columnStr .= "{$col} ,";
                    $valueStr .= ":{$col} ,";
                    $sqlBindData[":{$col}"] = $val;
            }
            $columnStr = rtrim($columnStr, ",");
            $valueStr = rtrim($valueStr, ",");
            $sql .= "( {$columnStr} ) VALUES ( {$valueStr} )";

            $write = $this->getWriteCon();

            $stmt = $write->prepare($sql);
            $stmt->execute($sqlBindData);

            $lastId = $write->lastInsertId();

            return $lastId;

        }
        catch(PDOException $e)
        {
            echo $sql . "<br>" . $e->getMessage();
            die();
        }
    }

    public function deleteRow($table, $condition_params = array())
    {
        try{
            $sql = "DELETE FROM {$table} ";
            $sqlBindData = array();

            $sql .= " WHERE ";
            $conditions = array("1");
            list($conditions, $sqlBindData) = $this->_setConditions($condition_params, $conditions, $sqlBindData);
            $sql .= implode(" AND ", $conditions);

            $write = $this->getWriteCon();

            $stmt = $write->prepare($sql);
            $stmt->execute($sqlBindData);

            return $stmt->rowCount();
        }catch(PDOException $e)
        {
            echo $sql . "<br>" . $e->getMessage();
            die();
        }
    }
	
	public function showColumn($table)
	{
        try{
            $read = $this->getReadCon();
            $stmt = $read->prepare("SHOW COLUMNS FROM {$table}");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e)
        {
			echo $sql . "<br>" . $e->getMessage();
			die();
        }
	}
	
}
