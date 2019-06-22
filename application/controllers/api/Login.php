<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Login extends REST_Controller  {

    public function __construct() {
        parent::__construct();
 
    }
	
	public function index()
	{
	   
	   echo $this->sendmail("prashantjaiswal2003@gmail.com", "sub", "message", $file = NULL, "pamphlet@jagsal.com");
	}
	
    public function login_post()
    {
        try{
            // Validate request data
            $this->validate_request('login');
            
            // Extract user data from POST request
            $email = $this->post('email');
            $password = $this->post('password');
            
            $user=$this->Commonmodel->check_user(['email'=>$email,'password'=>sha1($password)]);
            if(empty($user)){
                // if Invalid Credentials
                $this->response(['status'=>parent::HTTP_BAD_REQUEST, 'msg' => $this->lang->line('text_rest_invalid_credentials')], parent::HTTP_BAD_REQUEST);    
            }
            $status=$user->status;
            switch ($status) {
                case "0":
                    $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' =>  $this->lang->line('inactive_user')], parent::HTTP_BAD_REQUEST);
                    break;
                case "1":
                    // SUCCESS : Create a token from the user data and send it as reponse
                    $token = AUTHORIZATION::generateToken(['email' => $email, 'password'=>$password]);
                    $this->response(['status' => parent::HTTP_OK, 'msg'=>$this->lang->line('login_success'),'info'=>$user, 'token' => $token], parent::HTTP_OK);
                    break;
                case "2":
                    $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' =>  $this->lang->line('blocked_user')], parent::HTTP_BAD_REQUEST);
                    break;
                default:
                    $this->something_went_wrong();
            }
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    public function afterlogin_post()
    {
        // Call the verification method and store the return value in the variable
        $data = $this->authenticate_user();
        $user=$this->Commonmodel->check_user(['email'=>$data->email,'password'=>sha1($data->password)]);
        // Send the return data as reponse
        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'info' => $user];
        $this->response($response, $status);
    }
}
