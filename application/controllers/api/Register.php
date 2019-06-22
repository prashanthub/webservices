<?php
defined('BASEPATH') OR exit('No direct script access allowed');
  
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Register extends REST_Controller  {

    public function __construct() {
        parent::__construct();
    }
	
	// Register a user
    public function registeruser_post()
    {   
        try{
            // Validate request data
            $this->validate_request('registeruser');
            
            // Extract user data from POST request
            $name = $this->post('name');
            $email = $this->post('email');
            $phone = $this->post('phone');
            $password = $this->post('password');
            $device_type = $this->post('device_type');
            $device_token = $this->post('device_token');
            
            // Upload Image
            $image_name='';
            if (isset($_FILES['image']['name'])) {
            $image_name=$this->uploadImage($name='image',$path='profile');
            }
            
            $data=[
             'name'=>$name,  
             'email'=>$email,
             'phone'=>$phone,
             'password'=>sha1($password),
             'status'=>0, // inactive
             'device_type'=>$device_type,
             'device_token'=>$device_token,
             'image'=>$image_name
            ];
            
            // Insert User data
            if($this->Commonmodel->insert('users',$data)){
                 // send mail
                 $details['verify'] = base_url() . "activateuser/?a=".base64_encode($email);
            	 $details['name']=$name;
            	 $data['title']="Email verification";
            	 $data['message']=$this->load->view('email/register', $details, TRUE);;
            	 $message= $this->load->view('email/mail_common_template', $data, TRUE);
                 $this->sendmail($email, $subject='Pamphlet - Email verification', $message);
                 
                 $user=$this->Commonmodel->check_user(['email'=>$email,'password'=>sha1($password)]);
                 $token = AUTHORIZATION::generateToken(['email' => $email, 'password'=>$password]);
            
                 $response = ['status'=>parent::HTTP_OK, 'msg' => $this->lang->line('register_success'), 'info'=>$user, 'token'=>$token];
                 $this->response($response, parent::HTTP_OK);   
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // Register a vendor
    public function registervendor_post()
    {   
        try{
            // Validate request data
            $this->validate_request('registervendor');
            
            // Extract user data from POST request
            $name = $this->post('name');
            $email = $this->post('email');
            $phone = $this->post('phone');
            $password = $this->post('password');
            $device_type = $this->post('device_type');
            $device_token = $this->post('device_token');
            $address = $this->post('address');
            $latitude = $this->post('latitude');
            $longitude = $this->post('longitude');
            
            // Upload Image
            $image_name='';
            if (isset($_FILES['image']['name'])) {
            $image_name=$this->uploadImage($name='image',$path='profile');
            }
            
            $data=[
             'name'=>$name,  
             'email'=>$email,
             'phone'=>$phone,
             'password'=>sha1($password),
             'status'=>0, // inactive
             'device_type'=>$device_type,
             'device_token'=>$device_token,
             'image'=>$image_name,
             'address'=>$address,
             'latitude'=>$latitude,
             'longitude'=>$longitude,
            ];
            
            // Insert Vendor data
            if($this->Commonmodel->insert('vendors',$data)){
                 // send mail
                 $details['verify'] = base_url() . "activateuser/?a=".base64_encode($email);
            	 $details['name']=$name;
            	 $data['title']="Email verification";
            	 $data['message']=$this->load->view('email/register', $details, TRUE);;
            	 $message= $this->load->view('email/mail_common_template', $data, TRUE);
                 $this->sendmail($email, $subject='Pamphlet - Email verification for Vendor', $message);
                 
                 $user=$this->Commonmodel->check_user(['email'=>$email,'password'=>sha1($password)]);
                 $token = AUTHORIZATION::generateToken(['email' => $email, 'password'=>$password]);
                 
                 $response = ['status'=>parent::HTTP_OK, 'msg' => $this->lang->line('register_success'), 'info'=>$user, 'token'=>$token];
                 $this->response($response, parent::HTTP_OK);   
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // forgot pass
    public function forgotpass_post()
    {   
        try{
            // Validate request data
            $this->validate_request('forgotpass');
            
            // Extract user data from POST request
            $email = $this->post('email');
            if(!$this->Commonmodel->checkEmail($email)){
                 $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'This Email does not exist'], self::HTTP_BAD_REQUEST);    
            }

            // send forgot password mail
            $details['reset'] = base_url() . "resetpassword/?a=".base64_encode($email);
            $details['name']='';
            $data['title']="Forgot Password";
            $data['message']=$this->load->view('email/forgotpass', $details, TRUE);;
            $message= $this->load->view('email/mail_common_template', $data, TRUE);
            $this->sendmail($email, $subject='Pamphlet - Forgot Password', $message);
            
            $response = ['status'=>parent::HTTP_OK, 'msg' => 'A Reset Password link has been sent to your mail, please check.'];
            $this->response($response, parent::HTTP_OK);   
           
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // reset password view
    public function resetpassword_get()
    {   
        try{
            $mail = $this->get('a');
            $data=[];
            $data['mail']=$mail;
            $this->load->view('api/header');
            $this->load->view('api/resetpass',$data);
            $this->load->view('api/footer');
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // resetuserpass
    public function resetuserpass_post()
    {   
        try{
            // Validate request data
            //$this->validate_request('resetuserpass');
            
            // Extract user data from POST request
            $email = base64_decode($this->post('email'));
            $password = $this->post('password');
            $data=[];
            if($this->Commonmodel->resetPassword($email,$password)){
                $this->load->view('api/header');
                $this->load->view('api/resetuserpass',$data);
                $this->load->view('api/footer');
            }else{
                 $this->something_went_wrong();
            }
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // Activate User
    public function activateuser_get()
    {   
        try{
            $mail = base64_decode($this->get('a')); 
            $result=$this->Commonmodel->activateuser($mail);
            $data=[];
            $data['result']=$result;
            $this->load->view('api/header');
            $this->load->view('api/activateuser',$data);
            $this->load->view('api/footer');
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // validate device type (Android / IOS )
    public function validate_devicetype($str)
    {   
        try{
            $field_value = $str; //this is redundant, but it's to show you how
            //the content of the fields gets automatically passed to the method
            $this->form_validation->set_message('validate_devicetype','Device Type can be Android OR IOS');
            return ((in_array($field_value,array('Android','IOS'))) ? TRUE : FALSE );
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    

    // check Unique mail
    public function unique_mail($str)
    {   
        try{
            $field_value = $str; //this is redundant, but it's to show you how
            //the content of the fields gets automatically passed to the method
            $this->form_validation->set_message('unique_mail','Email field must contain a unique value');
            return ($this->Commonmodel->check_unique_mail($field_value) ? TRUE : FALSE );
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
}
