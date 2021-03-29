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
class customerModel  {
    public $adapter;
    public $sql;
    public function __construct() {
        $this->adapter = new Adapter(array(
            'driver' => 'Mysqli',
            'database' => 'stage_customerbaseket',
            'username' => 'root',
            'password' => 'truefalse',
        ));
        $this->sql = new Sql\Sql($this->adapter);
    }
    
    function getItemIntoCart($optional) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('cart_item');
            if(!empty($optional['merchant_inventry_id'])) {
                $query = $query->where(array('merchant_inventry_id'=>$optional['merchant_inventry_id']));
            }
            if(!empty($optional['user_id'])){
                $query = $query->where(array('user_id'=>$optional['user_id']));
            }
            if(!empty($optional['guest_user_id'])) {
                $query = $query->where(array('guest_user_id'=>$optional['guest_user_id']));
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function addToCart($params) {
        try {
            $query = $this->sql->insert('cart_item')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
	print_R($params);
	//echo $ex->getMessage();die;
            return false;
        } 
    }
    
    function updateCart($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('cart_item')
                            ->set($params)
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
    
    function deleteCart($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->delete('cart_item')
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }         
    }
    function getUserDetail($whereParams, $optional = array()) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $orQuery = "";
            $query = $this->sql->select('user_master'); 
            if(!empty($optional['columns'])) {
                $query->columns($optional['columns']);
            }            
            if(!empty($whereParams['id'])) {
                $query = $query->where(array('user_master.id' => $whereParams['id']));
            } 
            if(!empty($whereParams['email'])) {
                $query = $query->where($where->nest->or->equalTo('user_master.email', $whereParams['email']), "OR");
            }            
            if(!empty($whereParams['mobile_number'])){
                $query = $query->Where($where->nest->or->equalTo('user_master.mobile_number', $whereParams['mobile_number']), "OR");
            }  
                      
            if(!empty($whereParams['password'])) {
                $query = $query->where(array('user_master.password' => $whereParams['password']));
            }            
            if(!empty($whereParams['name'])) {
                $query = $query->where(new \Zend\Db\Sql\Predicate\Like('user_master.name', $whereParams['name']));
            }            
            //$query = $query->where(array('user_master.status' => 1));
            if(!empty($whereParams['status'])) {
                $query = $query->where(array('user_master.status' => $whereParams['status']));
            }
            if(isset($whereParams['verified_mobile'])) {
                $query = $query->where(array('user_master.verified_mobile' => $whereParams['verified_mobile']));
            }
            if(isset($whereParams['verified_email'])) {
                $query = $query->where(array('user_master.verified_email' => $whereParams['verified_email']));

            }            
            if(isset($whereParams['key'])) {
                $query = $query->where(array('user_master.key' => $whereParams['key']));
            }            
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }   
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count_row'])) {
                $result = $result->current();
            }            
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
    }
    
    function updateUser($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('user_master')
                            ->set($params)
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }        
    }
    function addUser($params) {
        try {
            $query = $this->sql->insert('user_master')
                        ->values($params);
            //echo $query->getSqlString();die;
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function addDeliveryAddress($params) {
        try {
            $query = $this->sql->insert('delivery_address')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function updateDeliveryAddress($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('delivery_address')
                            ->set($params)
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }                
    }
    
    function getAddressList($where, $optional=array()) {
        try {
            $query = $this->sql->select('delivery_address');
            if(!empty($where['id'])) {
                $query = $query->where(array('id'=>$where['id']));
            }            
            if(!empty($where['user_id'])){
                $query = $query->where(array('user_id'=>$where['user_id']));
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count'])) {
                $result = $result->current();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    function updateOrderSeq($orderName) {
        try {
            $response = array();
            if(!empty($orderName)) {
                $query = $this->sql->select('order_seq');
                $query = $query->where(array('order_name'=>$orderName));
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute()->current();
                if(!empty($result)) {
                    $response[$orderName] = $params['seq'] = $result['seq']+1;
                    $where = array('order_name'=>$orderName);
                    if(!empty($where)) {
                        $query = '';
                        $query = $this->sql->update('order_seq')
                                    ->set($params)
                                    ->where($where);
                        $satements = $this->sql->prepareStatementForSqlObject($query);
                        $result = $satements->execute();
                    }
                }else{
                    $orderData = array();
                    $orderData['order_name'] = $orderName;
                    $response[$orderName] = $orderData['seq'] = 1;
                    $query = $this->sql->insert('order_seq')
                        ->values($orderData);
                    $satements = $this->sql->prepareStatementForSqlObject($query);
                    $result = $satements->execute();
                }
                return $response;                
            }else {
                return false;
            }
        }  catch (\Exception $ex) {
            echo $ex->getMessage();die;
            return false;
        }
    }
    
    function createOrder($orderData) {
        try {
            $query = $this->sql->insert('order_master')
                        ->values($orderData);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }         
    }
 
    function updateOrder($params, $where) {
        try {
            if(!empty($where) && !empty($params)) {
                $query = $this->sql->update('order_master')
                            ->set($params)
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute()->getAffectedRows();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;
        }         
    }
    
    function orderList($where, $optional=array()) {
        try {
             $sorting = true;
            $whereQuery = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('order_master');
            if(!empty($optional['columns'])) {

                $query->columns($optional['columns']);
            }
            if(!empty($where['order_id'])) {
                $query = $query->where(array('order_id'=>$where['order_id']));                
            } 
            if(!empty($where['parent_order_id'])) {
                $query = $query->where(array('parent_order_id'=>$where['parent_order_id']));                
            }
            if(!empty($where['store_id'])) {
                $query = $query->where(array('store_id'=>$where['store_id']));
            }                          
            if(!empty($where['merchant_id'])) {
                $query = $query->where(array('merchant_id'=>$where['merchant_id']));
            }                         
            if(!empty($where['order_status'])){
                $query = $query->where(array('order_status'=>$where['order_status']));
            }  
            if(!empty($where['payment_status'])){
                $query = $query->where(array('payment_status'=>$where['payment_status']));
            }               
            if(!empty($where['user_id'])) {
                $query = $query->where(array('user_id'=>$where['user_id']));
            }else {
                if(!empty($where['order_id'])) {
                    $query = $query->Where($whereQuery->nest->or->equalTo('parent_order_id', $where['order_id']), "OR");
                }else{
                    $query = $query->where(new \Zend\Db\Sql\Predicate\NotLike('order_id', 'order_p%'));
                }
                $sorting = false;
            }            
            if(!empty($optional['short_by'])) {
                $query->order(array('created_date '.$optional['short_type']));
            }else if($sorting){
                $query->order(array('id DESC'));
            }
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count_row'])) {
                $result = $result->current();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function getOrderItem($where,$optional=array()) {
        try {
            $query = $this->sql->select('order_items');
            $query = $query->where(array('order_id'=>$where['order_id']));
            if(!empty($optional['id'])) {
                $query = $query->where(array('id'=>$optional['id']));
            }
            if(!empty($optional['status'])) {
                $query = $query->where(array('status'=>$optional['status']));
            }
            $query->order(array('id DESC'));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function assignedOrderToRider($where, $optional = array()) {
        try {
            $query = $this->sql->select('order_assignments');
            $query = $query->join('order_master', 'order_master.order_id = order_assignments.order_id',array('parent_order_id', 'store_id','shipping_address_id','payment_status', 'order_status', 'payable_amount','user_id','merchant_id','delivery_date','time_slot_id','amount','commission_amount','discount_amount','updated_date', 'shipping_charges'));            
            if(!empty($optional['columns'])) {
                $query->columns($optional['columns']);
            }
            if(!empty($where['order_id'])) {
                $query = $query->where(array('order_assignments.order_id'=>$where['order_id']));
            }            
            if(!empty($where['user_id'])) {
                $query = $query->where(array('order_assignments.rider_id'=>$where['user_id']));
            }                        
            if(!empty($where['order_status'])){
                $query = $query->where(array('order_master.order_status'=>$where['order_status']));
            }
            $query = $query->where(array('order_assignments.status'=>1));
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }   
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count_row'])) {
                $result = $result->current();
            }
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }         
    }

    function getOrderAssignment($where){
        try {
            $query = $this->sql->select('order_assignments');
            if(!empty($where['order_id'])) {
                $query = $query->where(array('order_assignments.order_id'=>$where['order_id']));
            }            
            if(!empty($where['user_id'])) {
                $query = $query->where(array('order_assignments.rider_id'=>$where['user_id']));
            }
            $query = $query->where(array('order_assignments.status'=>1));
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }   
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count_row'])) {
                $result = $result->current();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;

        }        
    }
    function assignOrder($params) {
        try {
            $query = $this->sql->insert('order_assignments')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }         
    }
    
    function updateOrderAssignment($params, $where) {
        $result = false;
        try {        
            if(!empty($where) && !empty($params)) {            
                $query = $this->sql->update('order_assignments')
                            ->set($params)
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                
            }   
            return $result;
        } catch (\Exception $ex) {
            return false;
        }
    }
    function insertIntoOtpMaster($params) {
        try {
            $query = $this->sql->insert('otp_master')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function smsqueue($params) {
        try {
            $query = $this->sql->insert('sms_queue')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            file_get_contents("http://172.104.239.54/rabbitmq/amqp_publisher.php");
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function updatesmsfromusmsqueue($param,$where) {
       
        try {            
            $query = $this->sql->update('sms_queue')
                        ->set($param)
                        ->where(array('id'=>$where));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->getAffectedRows();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }
        
    }
    
    function deleteOtp($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->delete('otp_master')
                            ->where($where);
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
        
    }
    
    function verifyOtp($where) {
        try {
            $limit = 1;
            $query = $this->sql->select('otp_master');
            $query->columns(array('count' => new \Zend\Db\Sql\Expression('count(*)')));
            if(isset($where['mobile_number'])) {
                $query = $query->where(array('mobile_number'=>$where['mobile_number']));
            }
            if(isset($where['otp'])) {
                $query = $query->where(array('otp'=>$where['otp']));
            }
            if(isset($where['otp_type'])) {
                $query = $query->where(array('otp_type'=>$where['otp_type']));
            }            
            if(isset($where['expiry_date'])) {
                $query = $query->where("expiry_date >= '$where[expiry_date]'");
            }                        
            $satements = $this->sql->prepareStatementForSqlObject($query);            
            $result = $satements->execute()->current();
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
        
    }
    
    function saveuserauthlink($params) {
        try {
            $query = $this->sql->insert('user_auth')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    function deleteUserAuth($where) {
        try {
            if(!empty($where)) {

                $query = $this->sql->delete('user_auth')
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }         
    }
    function checkauthkey($param) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('user_auth');
            if(!empty($param['auth_key'])) {
                $query = $query->where(array('user_auth.auth_key'=>$param['auth_key']));
            }
            if(!empty($param['key_for'])) {
                $query = $query->where(array('user_auth.key_for'=>$param['key_for']));
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->current();
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
        
    }
    
    function insertProductIntoOrderItem($itemData) {
        try {
            $query = $this->sql->insert('order_items')
                        ->values($itemData);    
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }    

    function updateOrderItem($itemData, $where) {
        try {
            $query = $this->sql->update('order_items')
                        ->set($itemData)
                        ->where($where);   
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->getAffectedRows();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }    
    
    function beginTransaction() {
        $this->adapter->getDriver()->getConnection()->beginTransaction();
    }
    function commit() {
        $this->adapter->getDriver()->getConnection()->commit();
    }    
    function rollback() {
        $this->adapter->getDriver()->getConnection()->rollback();
    } 

    function changepassword($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('user_master')
                            ->set($params)
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute()->getAffectedRows();
                return $result;
            }else{
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function enterDataIntoMailQueue($params, $optional=array()) {
        try {
            $table = 'email_queue';
            if(!empty($optional['queue_type'])) {
                $table = $optional['queue_type'];
            }
            $query = $this->sql->insert($table)
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            file_get_contents("http://172.104.239.54/rabbitmq/amqp_publisher.php");
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    function getTemplate($where, $optional = array()) {
        try {
                $table = 'email_template';
                if(!empty($optional['template_type'])) {
                    $table = $optional['template_type'];
                }
                $query = $this->sql->select($table); 
                $query = $query->where($where);
                if(!empty($optional['pagination'])) {
                    $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                    $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
                }   

                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                if(count($where)) {
                    $result = $result->current();
                }
                return $result;
        } catch (\Exception $ex) {
            return false;
        }         
    }
    
    function getNotification($where, $optional = array()) {
        try {
            $query = $this->sql->select('notification_queue');
            if(!empty($optional['count'])) {
                $query->columns(array('count'=>new Expression("count(*)")));
            }
            $query = $query->where($where);
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }            
            $query->order(array('id DESC'));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    function getSms($where) {
        try {
            $query = $this->sql->select('sms_queue');
            $query = $query->where($where);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    function updateSms($params, $where){
        try {
            if(!empty($where)) {
                $query = $this->sql->update('sms_queue')
                            ->set($params)
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
    
    function updateNotification($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('notification_queue')
                            ->set($params)
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
    
    function savePaymentDetails($params) {
        try {
            $query = $this->sql->insert('payment_details')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }         
    }
    
    function updatePaymentDetails($params, $where) {
        try {
            if(!empty($where) && !empty($params)) {
                $query = $this->sql->update('payment_details')
                            ->set($params)
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
    function getPaymentDetails($where) {  
        try {
            $query = $this->sql->select('payment_details');
            $query = $query->where($where);        
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            
            return $result;        
        }  catch (\Exception $ex) {
            return FALSE;
        }
    }
    function updateOrderPayment($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('order_master')
                            ->set($params)
                            ->where("order_id='".$where['order_id']."' OR parent_order_id='".$where['parent_order_id']."'");
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

    
    function getTotalRevenu($param) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('ledger_summary');
            if(!empty($param['merchant_id'])) {
                $query = $query->where(array('ledger_summary.merchant_id'=>$param['merchant_id']));
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->current();
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
        
    }
    
    function getOrderWiseLedger($param) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('ledger_master');
            if(!empty($param['start_date'])) {
                $query = $query->where($where->greaterThanOrEqualTo('ledger_master.created_date', $param['start_date']));
            }
            if(!empty($param['end_date'])) {
                $query = $query->where($where->lessThanOrEqualTo('ledger_master.created_date', $param['end_date']));
            }
            if(!empty($param['merchant_id'])) {
                $query = $query->where(array('ledger_master.merchant_id'=>$param['merchant_id']));
            }
            $query->order(array('id DESC'));
//            echo$query->getSqlString();die;
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        } 
    }
    
    public function insertIntoLedgerSummary ($params) {
        try {
            $query = $this->sql->insert('ledger_summary')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }         
    }
    
    public function insertIntoLedger($params){
        try {
            $query = $this->sql->insert('ledger_master')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }    
    
    public function updateLedgerSummary($params, $where) {
        $data = array(
            'total_revenue' => new \Zend\Db\Sql\Expression("total_revenue+".$params['total_revenue']),
            'total_commission' => new \Zend\Db\Sql\Expression("total_commission+".$params['total_commission']),
            'total_discount' => new \Zend\Db\Sql\Expression("total_discount+".$params['total_discount']),
            'total_merchant_amount' => new \Zend\Db\Sql\Expression("total_merchant_amount+".$params['total_merchant_amount'])
        );
        try {
            if(!empty($where)) {
                $query = $this->sql->update('ledger_summary')
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
    
    public function getCustomerCount($whereParams, $optional = array()) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('user_master');
            if(empty($optional)){
                $optional['date_formate'] = "%Y-%m-%d";
            }
            if(!empty($whereParams['start_date']) && !empty($whereParams['end_date'])) {
                $query->columns(array('count'=>new Expression("count(*)"),'created_date'=>new Expression("DATE_FORMAT(created_date, '$optional[date_formate]')")));
                $query->group(new Expression("DATE_FORMAT(created_date, '$optional[date_formate]')"));
            }else{
                $query->columns(array('count'=>new Expression("count(*)")));
            }
            
            if(!empty($whereParams['start_date'])) {
                $query = $query->where($where->greaterThanOrEqualTo('user_master.created_date', $whereParams['start_date']));                
            }
            if(!empty($whereParams['end_date'])) {
                $query = $query->where($where->lessThanOrEqualTo('user_master.created_date', $whereParams['end_date']));                
            }   
            //echo $query->getSqlString();die;
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    public function getOrderCount($whereParams, $optional = array()) {
        try {
            if(empty($optional)){
                $optional['date_formate'] = "%Y-%m-%d";
            }            
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('order_master');
            $query->columns(array('count'=>new Expression("count(*)"),'created_date'=>new Expression("DATE_FORMAT(created_date, '$optional[date_formate]')")));
            
            if(!empty($whereParams['merchant_id'])) {
                $query = $query->where($where->equalTo('order_master.merchant_id', $whereParams['merchant_id']));                
            }
            if(!empty($whereParams['order_status'])) {
                $query = $query->where($where->equalTo('order_master.order_status', $whereParams['order_status']));                
            }            
            if(!empty($whereParams['start_date'])) {
                $query = $query->where($where->greaterThanOrEqualTo('order_master.created_date', $whereParams['start_date']));                
            }
            if(!empty($whereParams['end_date'])) {
                $query = $query->where($where->lessThanOrEqualTo('order_master.created_date', $whereParams['end_date']));                
            }   
            $query->group(array(new Expression("DATE_FORMAT(created_date, '$optional[date_formate]')")));
            //if($whereParams['order_status'] =='cancelled'){
                //echo $query->getSqlString();die;
            //}
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function addRestrictedLocation($params) {
        try {
            $query = $this->sql->insert('restricted_location_master')
                        ->values($params);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->getAffectedRows();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function updateRestrictedLocation($params, $where) {
        try {            
            $query = $this->sql->update('restricted_location_master')
                        ->set($params)
                        ->where(array('id'=>$where['id']));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute()->getAffectedRows();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    public function restrictedLocationList($optional = array()) {
        try {
            $where = new \Zend\Db\Sql\Where();
            
            $query = $this->sql->select('restricted_location_master');
            if(!empty($optional['columns'])){
                $query->columns($optional['columns']); 
            }             
            if (!empty($optional['id'])) {
                $query = $query->where(array('id' => $optional['id']));
            }
            if(!empty($optional['address'])) {
                $query = $query->where($where->like('address', "%".$optional['address']."%"));
            } 
            if(!empty($optional['city_id'])) {
                $query = $query->where(array('city_id'=>$optional['city_id']));
            }            
            if(isset($optional['active'])) {
                $query = $query->where(array('active'=>$optional['active']));
            } 
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
    
    function deleteRestrictedLocation($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->delete('restricted_location_master')
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    function deleteShippingAddress($where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->delete('delivery_address')
                            ->where($where);
                $satements = $this->sql->prepareStatementForSqlObject($query);
                $result = $satements->execute();
                return true;
            }else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function getEmailQueque($where) {
        try {
            $query = $this->sql->select('email_queue');
            $query = $query->where($where);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    } 
    function updatemail($params, $where) {
        try {
            if(!empty($where)) {
                $query = $this->sql->update('email_queue')
                            ->set($params)
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
  function getCoupon($whereParams, $optional) {
        try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('coupon_master');
            if(!empty($optional['columns'])) {
                $query->columns($optional['columns']);
            }            
            if(!empty($whereParams['start_date'])) {
                $query = $query->where($where->lessThanOrEqualTo('coupon_master.start_date', $whereParams['start_date']));
            }
            if(!empty($whereParams['end_date'])) {
                $query = $query->where($where->greaterThanOrEqualTo('coupon_master.end_date', $whereParams['end_date']));
            }
            if(!empty($whereParams['name'])) {

                $query = $query->where(array('coupon_name'=>$whereParams['name']));
            }
            if(!empty($whereParams['user_id'])) {
                $query = $query->where(array('user_id'=>$whereParams['user_id']));
            } 
            if(isset($whereParams['show_coupon'])) {
            	$query = $query->where(array('show_coupon'=>$whereParams['show_coupon']));                     
            }
            if(!empty($optional['pagination'])) {
                $startLimit = ($optional['page']-1)*PER_PAGE_LIMIT;
                $query->limit(PER_PAGE_LIMIT)->offset($startLimit);
            }               
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($optional['count_row'])) {
                $result = $result->current();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function savecoupon($data) {
        try {
            $query = $this->sql->insert('coupon_master')
                        ->values($data  );
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function insetIntoappliedCoupon($parameters) {
        try {
            $query = $this->sql->insert('applied_user_coupon_mapping')
                        ->values($parameters);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return $this->adapter->getDriver()->getLastGeneratedValue();
        } catch (\Exception $ex) {
            return false;
        }        
    }
    
    function deleteAppliedCoupon($userId, $couponId=0) {
          try {
            $query = $this->sql->delete('applied_user_coupon_mapping');
                   if(!empty($userId)) {
                     $query->where(array('user_id'=>$userId));
                   }
                   if(!empty($couponId)) {
                    $query->where(array('coupon_id'=>$couponId));
                    }
                    $query->where(array('status'=>'applied'));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
        } catch (\Exception $ex) {
            return false;
        }      
    }
    
    function getAppliedCoupon($userId, $couponType='applied') {
        try {
            $query = $this->sql->select('coupon_master');
            $query = $query->join('applied_user_coupon_mapping', 'applied_user_coupon_mapping.coupon_id = coupon_master.id',array('status'));   
            $query->where(array('applied_user_coupon_mapping.user_id'=>$userId))
                    ->where(array('applied_user_coupon_mapping.status'=>$couponType));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            $result = $result->current();
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }                
    }
    
    function checkCouponIsUsed($where) {
        try {
            $query = $this->sql->select('applied_user_coupon_mapping');
              
            $query->where($where);
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            $result = $result->current();
            
            return $result;
        } catch (\Exception $ex) {
            return false;
        }     
    }    
    
    function updateAppliedCoupon($userId, $couponId, $status) { 
        try {
            $query = $this->sql->update('applied_user_coupon_mapping')
                        ->set(array('status'=>$status))
                        ->where(array('user_id'=>$userId, 'coupon_id'=>$couponId));
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            return true;
        } catch (\Exception $ex) {
            return false;
        }        
    }    
    
    public function getBallance($parameters) {
        try {
            $query = $this->sql->select('wallet_master');
            $query->columns(array('amount'));
            if(!empty($parameters['user_id'])){
                $query = $query->where(array('user_id'=>$parameters['user_id']));
            }
            if(!empty($parameters['wallet_key'])){
                $query = $query->where(array('wallet_key'=>$parameters['wallet_key']));
            }
            $satements = $this->sql->prepareStatementForSqlObject($query);
            $result = $satements->execute();
            if(!empty($parameters['user_id'])) {
                $result = $result->current();
            }
            return $result;
        } catch (\Exception $ex) {
            return false;
        }         
    }  
    
    
    public function getSpecialUserOffer($parameters) {
       try {
            $where = new \Zend\Db\Sql\Where();
            $query = $this->sql->select('special_user_offer_master');   
 
            
           
           $query = $query->where($where->lessThanOrEqualTo('special_user_offer_master.start_datetime', date('Y-m-d H:i:s')));
           $query = $query->where($where->greaterThanOrEqualTo('special_user_offer_master.end_datetime', date('Y-m-d H:i:s')));          
            if(!empty($parameters['mobile_number']) || !empty($parameters['email'])) {
                $query = $query->where(array('special_user_offer_master.username' => array($parameters['mobile_number'],$parameters['email'])));
            }      
            $query = $query->where(array('special_user_offer_master.status' => '1'));
           $satements = $this->sql->prepareStatementForSqlObject($query);
           $result = $satements->execute();
           return $result->current();
        } catch (\Exception $ex) {
            return false;
        }       	
    }       
}
