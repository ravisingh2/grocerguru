<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Library;
use Application\Library\common;
use Application\Model\commonModel;
use Application\Model\productModel;
class product {
    protected $productModel;
    protected $redis;
    public function __construct() {
        $this->commonLib = new common();
        $this->commonModel = new commonModel();
        $this->productModel = new productModel();
        //$this->redis = new \Redis();
        //$this->redisObj = $this->redis->connect('127.0.0.1', 6379);        
    }
    function featurecategorylist($parameters){
    	$response = array('status' => 'fail', 'msg' => 'No record found ');
	if (!empty($parameters['feature_category_id'])){
	    $optional['feature_category_id'] = $parameters['feature_category_id'];         
	}
    	$result = $this->productModel->featurecategorylist($optional);
    	if(!empty($result)) {
    		$imageData = array();
    		$data = $this->commonLib->processResult($result, 'category_id', false, true);
		$whereParams = array();
                $whereParams['image_id'] = array_keys($data);
                $whereParams['type'] = 'category';
                $categoryImageData = $this->commonModel->fetchImage($whereParams);
                if(!empty($categoryImageData)) {
                    $imageData = $this->commonLib->processResult($categoryImageData, 'image_id', true);
                }    		
    		$response = array('status' => 'success', 'data' => $data, 'images'=>$imageData,'imageRootPath'=>HTTP_ROOT_PATH);
    	}
    	return $response;
    }
    function getProductList($parameters) {
        $keyStr = md5(json_encode($parameters));
        //$response = $this->redis->get($keyStr);
        if(!empty($response)) {
            $response = json_decode($response, true);
            return $response;
        }else {
            $response = array('status' => 'fail', 'msg' => 'No record found ');
            $optional = array();
            $totalNumberOfRecord = 0;
            if (!empty($parameters['id'])){
                $optional['id'] = $parameters['id'];
            }
            if (!empty($parameters['city_id'])){
                $storeParams = array();
                $optional['store_id'][]= 0;
                $storeParams['city_id'] = $parameters['city_id'];
                $storeList = $this->commonLib->getStoreByCity($storeParams);
                if(!empty($storeList['data'])) {
                    $optional['store_id'] = array_keys($storeList['data']);
                }   
            }   
            if(!empty($parameters['all_product'])) {
                $optional['all_product'] = 1;
            }
            if(!empty($parameters['product_name'])) {
                $optional['product_name'] = $parameters['product_name'];
            }
            if(!empty($parameters['product_id'])) {
                $optional['product_id'] = $parameters['product_id'];
            }     
            if(!empty($parameters['product_type'])) {
                if(is_array($parameters['product_type'])){
                    if(in_array('hotdeals',$parameters['product_type'])) {
                        $optional['hotdeals'] = 1;
                    }
                    if(in_array('offers', $parameters['product_type'])) {
                        $optional['offers'] = 1;
                    }
                    if(in_array('new_arrival', $parameters['product_type'])) {
                        $optional['new_arrival'] = 1;
                    }                
                }else{
                    if('hotdeals' == $parameters['product_type']) {
                        $optional['hotdeals'] = 1;
                    }
                    if('offers' == $parameters['product_type']) {
                        $optional['offers'] = 1;
                    }                
                    if('new_arrival' == $parameters['product_type']) {
                        $optional['new_arrival'] = 1;
                    }                
                }            
            }        
            if(!empty($optional['category_name'])) {
                $parameters['product_name'] = $parameters['category_name'];
            }        
            if (!empty($parameters['merchant_id'])){
                $optional['merchant_id'] = $parameters['merchant_id'];
            }     
            $categoryParams = array();
            if (!empty($parameters['category_name'])){
                $categoryParams['category_name'] = $parameters['category_name'];
            }  
            if (!empty($parameters['category_id'])){
                $categoryParams['parent_category_id'] = $parameters['category_id'];
            }     
            if(!empty($categoryParams)) {
                $categoryParams['columns'] = array(new \Zend\Db\Sql\Expression('category_master.id as id'));
                $categoryData = $this->commonLib->categoryList($categoryParams);
            }
            if(!empty($categoryData['data'])) {
                $optional['category_id'] = array_keys($categoryData['data']);
            }
            if (!empty($parameters['category_id'])){
                $optional['category_id'][] = $parameters['category_id'];         
            } 
            if (!empty($parameters['promotion_id'])){
                $optional['promotion_id'] = $parameters['promotion_id'];         
            }
            if (!empty($parameters['brand_name'])){
                $optional['brand_name'] = $parameters['brand_name'];         
            } 
            if (!empty($parameters['min_price'])){
                $optional['min_price'] = $parameters['min_price'];         
            }   
            if (!empty($parameters['max_price'])){
                $optional['max_price'] = $parameters['max_price'];         
            }   
            if (!empty($parameters['min_discount'])){
                $optional['min_discount'] = $parameters['min_discount'];         
            } 
            if (!empty($parameters['max_discount'])){
                $optional['max_discount'] = $parameters['max_discount'];         
            }             
            if (!empty($parameters['pagination'])) {
                $optional['pagination'] = $parameters['pagination'];
                $optional['page'] = !empty($parameters['page']) ? $parameters['page'] : 1;
            }
            if (!empty($parameters['order_by']) && !empty($parameters['short_by'])) {
                $optional['sort_by'] = $parameters['short_by'];
                $optional['order_by'] = $parameters['order_by'];
            }
            $result = $this->productModel->productList($optional);
            $attributeImageData = array();
            if (!empty($result)) {
                $productData = $this->commonLib->processResult($result, 'product_id', false, true);
                if (!empty($productData)) {
                    $optional['count'] = 1;
                    unset($optional['pagination']);
                    unset($optional['sort_by']);
                    $resultCount = $this->productModel->productList($optional);
                    $totalRecord = $resultCount->current();
                    $totalNumberOfRecord = $totalRecord['count'];
                    $getattribute = $this->commonModel->getAttributeList(array('product_id' => array_keys($productData)));
                    $attdata = $this->commonLib->processResult($getattribute, 'id');
                    if(!empty($attdata)) {
                        $attrImageWhere = array();
                        $attrImageWhere['image_id'] = array_keys($attdata);
                        $attrImageWhere['type'] = 'attribute';
                        $attributeImageData = $this->commonLib->fetchImage($attrImageWhere);                
                    }
                    $productImageWhere = array();
                    $productImageWhere['image_id'] = array_keys($productData);
                    $productImageWhere['type'] = 'product';
                    $commonModel = new commonModel();
                    $productImageData = $this->commonLib->fetchImage($productImageWhere);                                
                    $productImageWhere['type'] = 'nutrition_image';
                    $nutritionImageData = $this->commonLib->fetchImage($productImageWhere);                                
                    $minPriceParams = array();
                    $minPriceParams['attribute_id'] = array_keys($attdata);
                    if(!empty($optional['store_id'])) {
                        $minPriceParams['store_id'] = $optional['store_id'];
                    }
                    if (!empty($parameters['merchant_id'])){
                        $minPriceParams['merchant_id'] = $parameters['merchant_id'];
                    }                        
                    if (!empty($parameters['order_by']) && !empty($parameters['short_by'])) {
                        $minPriceParams['sort_by'] = $parameters['short_by'];
                        $minPriceParams['order_by'] = $parameters['order_by'];
                    }    
                    if(!empty($parameters['all_product'])) {
                         $minPriceParams['all_product'] = 1;
                    }
                    $prodcutAttribute = $this->getMerchantProductAttribute($minPriceParams, $attdata, $productData);
                    $productDetaList = $this->prepareProductWiseAttribute($productData, $prodcutAttribute);
                    $response = array('status' => 'success', 'data' => $productDetaList, 'attributeImageData'=>$attributeImageData, 'productImageData'=>$productImageData,'nutritionImageData'=>$nutritionImageData, 'imageRootPath'=>HTTP_ROOT_PATH, 'totalNumberOFRecord'=>$totalNumberOfRecord);               
                }
            }
        }
        //$this->redis->set($keyStr, json_encode($response));
        //$this->redis->expire($keyStr, 3600);         
        return $response;
    }
    
    function prepareProductWiseAttribute($productData, $productAttribute) {
        $productDetaList= array();
        foreach ($productData as $key=>$productDetails) {
            if(!empty($productAttribute[$key])) {
                $productDetaList[$key] = $productDetails;
                $productDetaList[$key]['attribute'] = $productAttribute[$key];
            }
        }
        return $productDetaList;
    }    
    function getMerchantProductAttribute($parameters, $attributeDetail) {
        $this->productModel = new productModel();
        $data = $this->productModel->getMerchantProductAttribute($parameters);
        $attributeByProduct = array();
        if(!empty($data)) {
            foreach($data as $row) {
                if(empty($attributeByProduct[$row['product_id']][$row['attribute_id']])) {
                    $attributeByProduct[$row['product_id']][$row['attribute_id']] = $row;
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['attribute_name'] = $attributeDetail[$row['attribute_id']]['name'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['discount_type'] = $attributeDetail[$row['attribute_id']]['discount_type'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['discount_value'] = $attributeDetail[$row['attribute_id']]['discount_value'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['unit'] = $attributeDetail[$row['attribute_id']]['unit'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['quantity'] = $attributeDetail[$row['attribute_id']]['quantity'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] = number_format($attributeByProduct[$row['product_id']][$row['attribute_id']]['price'], 2);
                    if($attributeDetail[$row['attribute_id']]['discount_type']=='percent') {
                        $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] = number_format($attributeByProduct[$row['product_id']][$row['attribute_id']]['price']-$attributeByProduct[$row['product_id']][$row['attribute_id']]['price']*$attributeDetail[$row['attribute_id']]['discount_value']/100, 2);
                    }else if($attributeDetail[$row['attribute_id']]['discount_type']=='flat'){
                        $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] = round($attributeByProduct[$row['product_id']][$row['attribute_id']]['price']-$attributeDetail[$row['attribute_id']]['discount_value'], 2);
                    }                    
                }else if($attributeByProduct[$row['product_id']][$row['attribute_id']]['price']>$row['price']) {
                    $attributeByProduct[$row['product_id']][$row['attribute_id']] = $row;
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['attribute_name'] = $attributeDetail[$row['attribute_id']]['name'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['discount_type'] = $attributeDetail[$row['attribute_id']]['discount_type'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['discount_value'] = $attributeDetail[$row['attribute_id']]['discount_value'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['unit'] = $attributeDetail[$row['attribute_id']]['unit'];                    
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['quantity'] = $attributeDetail[$row['attribute_id']]['quantity'];
                    $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] = number_format($attributeByProduct[$row['product_id']][$row['attribute_id']]['price'], 2);
                    if($attributeDetail[$row['attribute_id']]['discount_type']=='percent') {
                        $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] =round($attributeByProduct[$row['product_id']][$row['attribute_id']]['price']-$attributeByProduct[$row['product_id']][$row['attribute_id']]['price']*$attributeDetail[$row['attribute_id']]['discount_value']/100, 2);
                    }else if($attributeDetail[$row['attribute_id']]['discount_type']=='flat'){
                        $attributeByProduct[$row['product_id']][$row['attribute_id']]['actual_price'] = number_format($attributeByProduct[$row['product_id']][$row['attribute_id']]['price']-$attributeDetail[$row['attribute_id']]['discount_value'], 2);
                    }
                }
            }
        }
        return $attributeByProduct;
    }
    
    function getProductByMerchantAttributeId($parameters) {
        $response = array('status' => 'fail', 'msg' => 'No record found ');
        $optional = array();
        if(!empty($parameters['merchant_inventry_id'])) {
            $optional['merchant_inventry_id'] = $parameters['merchant_inventry_id'];
            $data = $this->productModel->productList($optional);
            $productData = $this->commonLib->processResult($data, 'id');
            if(!empty($productData)){
                $dataByProductId = $this->commonLib->processResult($productData, 'product_id');
                $productImageWhere = array();
                $productImageWhere['image_id'] = array_keys($dataByProductId);
                $productImageWhere['type'] = 'product';
                $commonModel = new commonModel();
                $productImageData = $this->commonLib->fetchImage($productImageWhere);                
                $response = array('status'=>'success', 'data'=>$productData, 'productImageData'=>$productImageData);
            }
        }
        
        return $response;
    }
    
    function cashCollected($parameters) {   
        $response = array('status' => 'fail', 'msg' => 'update Fail');
        if(empty($parameters['order_id'])) {
            $response['msg'] = 'order id not supplied';
            
            return $response;
        }
        $customerModel = new \Application\Model\customerModel();
        $updateWhereParams = array('order_id'=>$parameters['order_id']);
        $updateOrderData = array('payment_status'=>$parameters['payment_status']);
     
        $result = $customerModel->updateOrder($updateOrderData, $updateWhereParams);
        if(!empty($result)) {
            $response = array('status' => 'success', 'msg' => 'order Updated successfully');
        }
        
        return $response;
    }  
    
    function brandlist($parameters) {
        $response = array('status' => 'fail', 'msg' => 'No Record Found');
        $brandData = $this->productModel->brandList($parameters);
        $brandList = $dataByProductId = $this->commonLib->processResult($brandData, '');
        if(!empty($brandList)) {
            $response = array('status' => 'success', 'data' => $brandList);
        }
        
        return $response;
    }
    
    function notifyproduct($parameters) {
        $status = true;
        $response = array('status'=>'fail'); 
        $notifyData = array();
        if(!empty($parameters['user_id'])){
            $notifyData['user_id'] = $parameters['user_id'];
        }else{
            $response['msg'] = "User not supplied";  
             $status = false;
        }
        if(!empty($parameters['product_attribute_id'])){
            $notifyData['product_attribute_id'] = $parameters['product_attribute_id'];
        }else{
            $response['msg'] = "Attribute Id not supplied.";  
             $status = false;
        } 
        if($status) {
            $isProductOutOfStock = false;
            $productList = $this->commonModel->checkAttributeExist(array('attribute_id'=>$parameters['product_attrubute_id']));
            foreach ($productList as $product) {
                if($product['stock']<=0) {
                    $isProductOutOfStock = true;
                }else {
                    $isProductOutOfStock = false;
                }
            }
            if($isProductOutOfStock) {
                $notifyData['created_date'] = date('Y-m-d H:i:s');
                $productModel = new productModel();
                $notifiedAlready = $productModel->getNotifiedProduct($notifyData);
                if(!empty($notifiedAlready)) {
                    $response = array('status'=>'success', "msg"=>"You will be notified when product come in stock.");
                }else {
                    $this->productModel->insertIntoNotify($notifyData);
                    $response = array('status'=>'success', "msg"=>"You will be notified when product come in stock.");
                }
            }       
        }
        return $response;
    }
    
    function getNotifiedProductList($parameters) {
        $response = array('status'=>'fail', "msg"=>"No record found");
        if(!empty($parameters['user_id'])){
            $notifyData['user_id'] = $parameters['user_id'];
        }else{
            $status = true;
            $response['msg'] = "User not supplied";  
        }
        $productModel = new productModel();
        $notifiedProductList = $productModel->getNotifiedProduct($notifyData);        
        $data = $this->commonLib->processResult($notifiedProductList, 'product_attribute_id');
       
        if(!empty($data)) {
            $response = array('status'=>'success', "data"=>$data);
        }
        
        return $response;
        
    }

}
