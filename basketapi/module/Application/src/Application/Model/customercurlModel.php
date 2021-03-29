<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql;
use Zend\Db\Sql\Expression;
class customercurlModel  {
    public $adapter;
    public $sql;
    public function __construct() {
        $this->adapter = new Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'stage_afro_accrabasket',
            'username' => 'root',
            'password' => 'truefalse',
        ));
        $this->sql = new Sql\Sql($this->adapter);
    }
    public function productList($optional) {
        try {
            $where = new \Zend\Db\Sql\Where();

            $query = $this->sql->select('merchant_inventry');
            $query = $query->join('product_master', 'product_master.id = merchant_inventry.product_id',array('product_name', 'product_desc', 'default_discount_type'=>'discount_type', 'default_discount_value'=>'discount_value', 'category_id','custom_info','brand_name'));
            $query = $query->join('product_attribute', 'product_attribute.id = merchant_inventry.attribute_id',array('commission_type', 'commission_value', 'discount_type','discount_value', 'unit', 'quantity'));
            if(!empty($optional['merchant_inventry_id'])) {
                $query->columns(array('id'=>'id','price' => 'price', 'product_id' => 'product_id', 'merchant_id', 'store_id','merchant_product_code'));
                $query = $query->where(array('merchant_inventry.id' => $optional['merchant_inventry_id']));
            }else {
                $query->columns(array('product_id' => new \Zend\Db\Sql\Expression('DISTINCT(merchant_inventry.product_id)')));
            }            
            if(!empty($optional['category_id'])) {
                $query = $query->where(array('product_master.category_id' => $optional['category_id']));
            }            
            if(!empty($optional['product_name'])){
                $query = $query->Where($where->nest->or->like('product_master.product_name',"%".$optional['product_name']."%"), "OR");
            }            
            if(!empty($optional['store_id'])) {
                $query = $query->where(array('merchant_inventry.store_id' => $optional['store_id']));
            }            
            if(!empty($optional['merchant_id'])) {
                $query = $query->where(array('merchant_inventry.merchant_id' => $optional['merchant_id']));
            }            
            $query = $query->where(array('product_master.status' => 1));
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }            
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
    }
    function fetchImage($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->select('image_master');
                $query = $query->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                
                return $result;                
            }else {
                return false;
            }
        }  catch (\Exception $ex) {
            echo $ex->getMessage();die;
            return false;
        }
    } 
    
    function getStoreListById($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->select('merchant_store');
                $query = $query->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                
                return $result;                
            }else {
                return false;
            }
        }  catch (\Exception $ex) {
            $ex->getMessage();
            return false;
        }        
    }
    
    function getRiderList($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->select('rider_master');
                $query = $query->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                
                return $result;                
            }else {
                return false;
            }
        }  catch (\Exception $ex) {
            $ex->getMessage();
            return false;
        }        
    }
    public function updateInventry($params, $where) {
        if(isset($params['stock'])) {
           $data['stock'] = $params['stock']; 
        }
        if(!empty($params['number_of_item'])) {
            $data['stock'] = new \Zend\Db\Sql\Expression("stock-".$params['number_of_item']);
        }
        
        try {
            if(!empty($where)) {
                $query = $this->sql->update('merchant_inventry')
                        ->set($data)
                        ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                
                $result = $satements->execute();
                return true;
            }else{
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }      
    }     
}
