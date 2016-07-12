<?php
namespace SlsCore\Data; 

/**
* @author Mohamed Elbahja <Mohamed@elbahja.me>
* @copyright 2016 
* @version 2.0
* @package MySQLi_Manager 
* @category Database
*/

final class Mysqli extends \mysqli 
{

    /**
     * [$insert_ids get multi_insert() insert id's]
     * @var array
     */
	public $insert_ids = array();


	protected $db_connect;

    /**
     * __construct function 
     *
	 * public function __construct() { }
     */
    
    /**
     * Connect database
     * @return [type] [description]
     */
    public function conn() 
    {
        $this->db_connect = @parent::__construct(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->connect_error) {
            throw new \Exception(' Failed Connect to MySQL Database <br /> Error Info : ' . $this->connect_error);
        }
        
        $this->set_charset('utf8');
        //return $this->db_connect;         
    }


    /**
     * [to_utf8 Convert String to utf8]
     * @param  string $String 
     * @return string
     */
	protected function to_utf8($String) {
	    return mb_convert_encoding($String, 'UTF-8', mb_detect_encoding($String));     
    }
    
    /**
     * [escape : escape data]
     * @param  string $data
     * @return string
     */
	public function escape($data) {
		return $this->real_escape_string($this->to_utf8($data));
	}

    /**
     * [select : select data]
     * @param  string $select [columns ex: username, pass, email ....]
     * @param  string $from   [table name]
     * @param  string $where  [optional  ex: WHERE id='1']
     * @return object
     */
    public function select($select, $from, $where = '') {

   	  	return $this->query("SELECT {$select} FROM {$from} {$where}");
    }  

    /**
     * [select_one : select 1 row]
     * @param  string $select [columns ex: website_name, website_url, description ....]
     * @param  string $from   [table name]
     * @param  string $where  [optional]
     * @param  string $fetch  [fetch_assoc() = assoc ; fetch_row = row ; fetch_object = object]
     * @return mixed [if $fetch == assoc || row, return array ; if $fetch == object, return object; if $data == false return null]
     */
    public function select_one($select, $from, $where = '', $fetch = 'assoc') {
 
      $fetch = 'fetch_' . $fetch;
      $return = null;
   	   if ($data = $this->query("SELECT {$select} FROM {$from} {$where} LIMIT 1")) {

   	   	   $return = $data->$fetch();
   	   	   $data->close();
   	   	   unset($select, $from, $where);
   		}

      return $return;	
    }

   /**
    * [insert : insert data]
    * @param  string $into  [table name]
    * @param  array  $array [data , associative array : key = column and value = column value]
    * @return boolean
    */
    public function insert($into, array $array) {

        $return = FALSE;
        $data   = array();

		foreach ($array as $key => $value) {
			$data[] = $this->escape($key)."='".$this->escape($value)."'";
		}

		$data = implode(', ', $data);

			if ($this->query("INSERT INTO {$into} SET {$data}") ) {

			  	unset($into, $array, $data);

			  	$return = TRUE;
			}

		return $return;  
    }  

    /**
     * [multi_insert Multi Insert data]
     * @param  string $into  [tablse name]
     * @param  array  $array [data , Multidimensional Associative Array]
     * @return boolean
     */
 	public function multi_insert($into, array $array) {

        $ids = array();

        foreach($array as $val) {

	          if(!is_array($val)) {

	             unset($into, $array);
	             return FALSE;
	          }   
        }

        foreach ($array as $key => $value) {

          if($this->insert($into, $value) === TRUE) {

             $ids[$key] = $this->insert_id;

          } else {

             $ids[$key] = FALSE;
          }

        }

        $this->insert_ids = $ids;
		unset($into, $array, $ids);

        $f = array_filter($this->insert_ids);

		if (!empty($f)) {
			return TRUE; 
		}

	   return FALSE; 		
	} 

    /**
     * [update : update data]
     * @param  string $table [table name]
     * @param  array $array  [data , associative array : key = column and value = column value]
     * @param  string $where [optional]
     * @return boolean
     */
	public function update($table, $array, $where = '') {

		$return = FALSE;
        $data   = array();

		foreach ($array as $key => $value) {
			$data[] = $this->escape($key)."='".$this->escape($value)."'";
		}

	   $data = implode(', ', $data);

		  if ($this->query("UPDATE {$table} SET {$data} {$where}") ) {
		  	unset($table, $array, $where, $data);
		  	$return = TRUE;
		  }

		return $return;	   
	}

   /**
    * [delete : delete data]
    * @param  string $from  [tabel name]
    * @param  string $where [optional]
    * @return boolean
    */
	public function delete($from, $where = '') {

		$return = FALSE;

		 if ($this->query("DELETE FROM {$from} {$where}")) {
		 	unset($from, $where);
		 	$return = TRUE;
		 }

       return $return;
	}	

    /**
     * [optimize_table : optimize table]
     * @param  string $table_name [table name]
     * @return boolean
     */
	public function optimize_table($table_name) {
    
	   $data = $this->query("OPTIMIZE TABLE `{$table_name}`");
	   $return = FALSE;
	   $msg = $data->fetch_assoc();

	   if ($msg['Msg_type'] === 'status') {

	   	   $return = TRUE;
	   }
        
	   return $return;

	} 
    
    /**
     * [optimize_db Optimize All Tables]
     * @return Mixed [if success return array ; else return false]
     */
	public function optimize_db() {

		$tables = $this->query('SHOW TABLES');
		$status = array();

		while ($table = $tables->fetch_row()) {

			if ($this->optimize_table($table[0])) {

				$status[$table[0]] = TRUE;

			} else {

				$status[$table[0]] = FALSE;
			}
			
		}

		$tables->close();

        $f = array_filter($status);

	    if (!empty($f)) {

			return $status; 
		}	

      return FALSE;
	} 

 }