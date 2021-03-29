<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Library\customer;
use Zend\Mail;

class CustomerController extends AbstractActionController {
    public $commonLib;
    public $customerLib;
    public function __construct() {
        $this->customerLib = new customer();
        $this->commonLib = new \Application\Library\common();
        //$this->checkRqid();
    }
    public function indexAction() {
        $response = array('status' => 'fail', 'msg' => 'Method not supplied ');
        $requestParams = $parameters = trim($_REQUEST['parameters'], "\"");
        $parameters = json_decode($parameters, true);
        $parameters['agent'] = !empty($_REQUEST['agent'])?$_REQUEST['agent']:'w';
        $userDetailMandatoryForMethod = array('placeorder', 'modifyOrder');
        if(in_array($parameters['method'], $userDetailMandatoryForMethod)) {
            if(empty($parameters['user_id'])) {
                $parameters['method'] = '';
                $response = array('status' => 'fail', 'msg' => 'User id not suplied.');                
            }else{
                $userDetails = $this->customerLib->getUserDetailsById($parameters['user_id']);
                if(empty($userDetails)) {
                    $parameters['method'] = '';
                    $response = array('status' => 'fail', 'msg' => 'User not found.');
                }                
            }
        }        
        if (!empty($parameters['method'])) {
            switch ($parameters['method']) {
                case 'addtocart':
                    $response = $this->customerLib->addtocart($parameters);
                    break;
                case 'updatecart':
                    $response = $this->customerLib->updateCart($parameters);
                    break;
                case "getitemintocart":
                    $response = $this->customerLib->getItemIntoCart($parameters);
                    break;  
                case 'addedituser':
                    $response = $this->customerLib->addEditUser($parameters);
                    break;
                case 'login':
                    $response = $this->customerLib->login($parameters);
                    break;                
                case 'addeditdeliveryaddress':
                    $response = $this->customerLib->addEditDeleveryAddress($parameters);
                    break;                
                case 'getaddresslist':
                    $response = $this->customerLib->getAddressList($parameters);
                    break;   
                case 'checkout':
                    $response = $this->customerLib->checkout($parameters);
                    break;                
                case 'placeorder':
                    $response = $this->customerLib->placeOrder($parameters, $userDetails);
		    //$response['status']='fail'; 
		    //$response['msg']='operational issue. Contact with admin.';
                    break;
                case 'orderlist':
                    $response = $this->customerLib->orderList($parameters);
                    break;
                case 'assignedordertorider':
                    $response = $this->customerLib->getAssignedOrderToRider($parameters);
                    break;
                case 'assignordertorider':
                    $response = $this->customerLib->assignOrderToRider($parameters);
                    break;                
                case 'generateotp':
                    /*if($parameters['otp_type'] == 'register'){
                        $response['status'] = 'success';
                        $response['msg'] = 'Otp Sent';
                    }else {*/
                        $response = $this->customerLib->generateotp($parameters);
                    //}
                    break;
                case 'verifyotp':
                    $response = $this->customerLib->verifyotp($parameters);
                    break;
                case 'forgetpassword':
                    $response = $this->customerLib->forgetpassword($parameters);
                    break;
                case 'validateauthkey':
                    $response = $this->customerLib->validateAuthKey($parameters);
                    break;                    
                case 'changepassword':
                    $response = $this->customerLib->changepassword($parameters);
                    break;
                case 'changepasswordbyauthkey':
                    $response = $this->customerLib->changepasswordByAuthKey($parameters);
                    break;                    
                case 'updateorderbyrider':
                    $parameters['role'] = 'rider';
                    $response = $this->customerLib->updateOrderByRider($parameters);
                    break;       
                case 'updateOrderstatus':
                    $response = $this->customerLib->updateOrderStatus($parameters);
                    break;
                case 'ledgersummery':
                    $response = $this->customerLib->ledgersummery($parameters);
                    break;
                case 'paytomerchant':
                    $response = $this->customerLib->PayToMerchant($parameters);
                    break;                
                case 'getcustomersaledetail':
                    $response = $this->customerLib->getCustomerSalesDetails($parameters);
                    break;                
                case 'gettotalcustomer':
                    $response = $this->customerLib->getCustomerCount($parameters);
                    break;      
                case 'getnotification':
                    $response = $this->customerLib->getNotification($parameters);
                    break;
                case 'updatenotification':
                    $response = $this->customerLib->updateNotification($parameters);                    
                    break;
                case 'sendmanualnotificationbyrider':
                    $response = $this->customerLib->sendManualNotificationByRider($parameters);
                    break;
                case 'addEditRestrictedLocation':
                    $response = $this->customerLib->addEditRestrictedLocation($parameters);
                    break;                
                case 'getRestrictedLocationList':
                    $response = $this->customerLib->getRestrictedLocationList($parameters);
                    break;
                case 'getCustomerList':
                    $response = $this->customerLib->getRestrictedLocationList($parameters);
                    break;         
                case 'deleteRestrictedLocation':
                    $response = $this->customerLib->deleteRestrictedLocation($parameters);
                    break;
                case 'deleteshippingaddress':
                    $response = $this->customerLib->deleteshippingaddress($parameters);
                    break;                
                case 'userlist':
                    $parameters['count'] = 1;
                    $response = $this->customerLib->getUserDetail($parameters);
                    break;
                case 'modifyOrder':
                    $response = $this->customerLib->modifyOrder($userDetails, $parameters);
                    break;
                case 'verifyemail':
                    $response = $this->customerLib->verifyEmail($parameters);
                    break;                
                case 'customercarenumber':
                    
                    $customerCareNumber = array('0'=>'+233553354848', '3'=>'+233553354848');
                    $response = array();
                    $response['status'] = 'success';
                    $response['customer_care_number'] = $customerCareNumber[$parameters['city_id']]?$customerCareNumber[$parameters['city_id']]:$customerCareNumber[0];
                    break; 
                case 'applycoupon':
                    $response = $this->customerLib->applyCoupon($parameters);
                    break;  
                case 'getcoupon':
                    if(!isset($parameters['pagination'])) {
                        $parameters['pagination'] = true;
                    }
                    $parameters['show_coupon'] = '1';
                    $response = $this->customerLib->getCoupon($parameters);
                    break;
                case 'customerfeedback':
                    $response = $this->customerLib->customerfeedback($parameters);
                    break;   
                case 'getPaymentLink':
                    $response = $this->customerLib->getPaymentLink($parameters);
                    break;                      
                    
            }
        }
        $responseStr = json_encode($response);
        echo $responseStr;
        $logText = $requestParams."\n Response :- \n".$responseStr;
        $this->commonLib->writeDebugLog($logText, 'customer', $parameters['method']);
        exit;
    }
    
    function checkRqid() {
        $rqid = hash('sha512', SECURE_KEY.$_REQUEST['parameters']);
        if($rqid != $_REQUEST['rqid']){
            echo json_encode(array('status'=>"fail", "msg"=>"rqid not match"));
            exit;
        }      
    }    

}
