<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
CI Database Model | Designed By Anfath Hifans
 */

class Database_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->__settings();       
    }
    
    public function __settings(){
        // [sql_mode] privilege set [NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION] is works for all
        $this->db->simple_query("SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");        
    }
    
    public function run($sql_query, $rowFormat=false){
        $query = $this->db->query($sql_query);
        //return $this->db->last_query();
        //return $this->db->get_compiled_select(); 
        return ($rowFormat) ? $query->row() : $query->result();
    }
    
    public function insert($table, $valueArray){
        $this->db->insert($table, $valueArray);
        return ($this->db->trans_status()) ? $this->db->insert_id() : false;
    }
    
    public function insertAll($table, $valueArray){
        $this->db->insert_batch($table, $valueArray);
        return ($this->db->trans_status()) ? true : false;
    }
    
    public function update($table, $searchArray, $valueArray){
        $this->db->where($searchArray);
        $this->db->update($table, $valueArray);
        return ($this->db->trans_status()) ? true : false;        
    }
    
    public function updateAll($table, $columnName, $valueArray, $whereClauseArray = null){
        if(!is_null($whereClauseArray)){ $this->db->where($whereClauseArray); }
        $this->db->update_batch($table, $valueArray, $columnName);
        return ($this->db->trans_status()) ? true : false;
    }
    
    public function delete($table, $whereClauseArray, $options=null){
        if(!is_null($options)){ extract($options); }        
        if($whereInClauseArray){
            $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']);
        }
        $this->db->delete($table, $whereClauseArray);
        return ($this->db->trans_status()) ? true : false;
    }
    
    public function getAlls($options=null){
        $this->db->cache_on();

        if(!is_null($options)){ extract($options); }
        $this->db->select(($columns) ? $columns : '*');

        $this->db->from($dbtable . ' AS db'); 

        if(!is_null($whereClauseArray)){ $this->db->where($whereClauseArray); }        
        if(!is_null($whereInClauseArray)){ $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']); }        
        $this->db->group_by(($groupBy) ? $groupBy : 'db.id');

        if($countRecords){
            return $this->db->count_all_results();
        }else{
            if($filterList){ $this->db->limit($filterList['limit'], $filterList['offset']); }
            $this->db->order_by(($orderBy) ? $orderBy : 'db.id ASC');        
            $query = $this->db->get();

            $this->db->cache_off();

            if($query->num_rows() > 0){
                return $query->result();
            }else{ return false; }
        }

    }

    public function getAll($table, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $query = $this->db->order_by($sortOrder)->get($table, $limit, $offset);        
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function getWhere($table, $whereClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $query = $this->db->get_where($table, $whereClauseArray, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getWhereIn($table, $column, $whereInClauseArray, $sortOrder = 'id ASC', $limit = null, $offset = null){        
        $this->db->order_by($sortOrder);        
        $this->db->where_in($column, $whereInClauseArray);
        $query = $this->db->get($table, $limit, $offset);        
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function getWhereNotIn($table, $column, $whereNotInClauseArray, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->where_not_in($column, $whereNotInClauseArray);
        $query = $this->db->get($table, $limit, $offset);        
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function getWhereAndOrWhere($table, $whereClauseArray, $orWhereClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->group_start();
        $this->db->where($whereClauseArray);
        $this->db->group_end();
        $this->db->or_where($orWhereClauseArray);        
        $query = $this->db->get($table, $limit, $offset);
        //return $this->db->last_query();
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getWhereAndWhereIn($table, $whereClauseArray, $whereInClauseArray, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->where($whereClauseArray);
        $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']);
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? $query->result() : false;
    }
    
    public function getWhereAndWhereNotIn($table, $whereClauseArray, $whereNotInClauseArray, $sortOrder = 'id ASC', $limit = null, $offset = null, $rowFormat=false){
        $this->db->order_by($sortOrder);
        $this->db->where($whereClauseArray);
        $this->db->where_not_in($whereNotInClauseArray['column'], $whereNotInClauseArray['values']);
        $query = $this->db->get($table, $limit, $offset);        
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getLike($table, $likeClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->like($likeClauseArray);
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }

    public function getLikeAndOrLike($table, $likeClauseArray, $orLikeClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->like($likeClauseArray);
        $this->db->or_like($orLikeClauseArray);
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getWhereAndLike($table, $whereClauseArray, $likeClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->where($whereClauseArray);
        $this->db->like($likeClauseArray);
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getWhereNotInAndLike($table, $whereNotInClauseArray, $likeClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){
        $this->db->order_by($sortOrder);
        $this->db->where_not_in($whereNotInClauseArray['column'], $whereNotInClauseArray['values']);
        $this->db->like($likeClauseArray);
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getWhereAndWhereInAndLike($table, $whereClauseArray, $whereInClauseArray, $likeClauseArray, $rowFormat=false, $sortOrder = 'id ASC', $limit = null, $offset = null){       
        $this->db->order_by($sortOrder);
        $this->db->where($whereClauseArray);
        $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']);
        $this->db->group_start();
        $this->db->like($likeClauseArray);
        $this->db->group_end();
        $query = $this->db->get($table, $limit, $offset);
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;      
    }
    
    public function getByColumn($table, $columnArray, $whereClauseArray=null, $sortOrder = 'id ASC', $likeClauseArray=null, $whereInClauseArray=null, $limit = null, $offset = null, $rowFormat=false){
        $this->db->order_by($sortOrder);
        if($whereClauseArray){
            $this->db->where($whereClauseArray);
        }
        if($whereInClauseArray){
            $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']);
        }
        if($likeClauseArray){
            $this->db->group_start();
            $this->db->like($likeClauseArray);
            $this->db->group_end();
        }
        $query = $this->db->select($columnArray)->get($table, $limit, $offset);          
        return ($query->num_rows() > 0) ? (($rowFormat) ? $query->row() : $query->result()) : false;
    }
    
    public function getColumnValue($table, $column, $type, $whereClauseArray=null, $likeClauseArray=null, $whereInClauseArray=null){
        switch($type){
            case 'MAX' : $this->db->select_max($column); break;
            case 'MIN' : $this->db->select_min($column); break;
            case 'AVG': $this->db->select_avg($column); break;
            case 'SUM': $this->db->select_sum($column); break;
        }
        if($whereClauseArray){ $this->db->where($whereClauseArray); }
        if($likeClauseArray){ $this->db->like($likeClauseArray); }
        if($whereInClauseArray){ $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']); }        
        return $this->db->get($table)->row()->$column;        
    }
    
    public function countAll($table, $filter=false, $whereClauseArray=null, $orWhereClauseArray=null, $likeClauseArray=null, $orLikeClauseArray=null, $whereInClauseArray=null, $whereNotInClauseArray=null){
        if($filter){
            if($whereClauseArray){ $this->db->where($whereClauseArray); }
            if($orWhereClauseArray){ $this->db->or_where($orWhereClauseArray); }
            if($likeClauseArray){
                $this->db->group_start();
                $this->db->like($likeClauseArray);
                if($orLikeClauseArray){ $this->db->or_like($orLikeClauseArray); }
                $this->db->group_end();
            }
            if($whereInClauseArray){ $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']); }
            if($whereNotInClauseArray){ $this->db->where_not_in($whereNotInClauseArray['column'], $whereNotInClauseArray['values']); }
            $this->db->from($table);
            return $this->db->count_all_results();
        }else{
            return $this->db->count_all($table);
        }
    }
    
    public function findNextInsertId($table){       
        return $this->db->query("SHOW TABLE STATUS LIKE '{$table}'")->row()->Auto_increment;
    }
    
    public function findRecord($table, $type=null, $conditions=null, $sort_order = 'id ASC'){
        if(!is_null($conditions)){ extract($conditions); }
        if($whereClauseArray){ $this->db->where($whereClauseArray); }
        if($whereInClauseArray){
            $this->db->where_in($whereInClauseArray['column'], $whereInClauseArray['values']);
        }
        if($likeClauseArray){ $this->db->like($likeClauseArray); }
        $this->db->order_by($sort_order);
        $query = $this->db->get($table);
        switch($type){
            case "row" :
                return $query->row();
                break;
            case "first" :
                return $query->first_row();
                break;
            case "last" :
                return $query->last_row();
                break;
            case "next" :
                return $query->next_row();
                break;
            case "previous" :
                return $query->previous_row();
                break;
            default :
                return $query->result();
        }        
    }
    
}

?>