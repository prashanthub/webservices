<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class User extends REST_Controller  {

    public function __construct() {
        parent::__construct();
 
    }

    // Edit Profile
    public function editprofile_post()
    {   
        try{
            // Validate request data
            $this->validate_request('editprofile');
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            
            // Extract user data from POST request
            $name = $this->post('name');
            $phone = $this->post('phone');
            $address = $this->post('address');
            $user_type = $this->post('user_type');// user OR vendor
            
            // Upload Image
            $image_name='';
            if (isset($_FILES['image']['name'])) {
            $image_name=$this->uploadImage($name='image',$path='profile');
            }
            
            $data=[
             'name'=>$name,  
             'phone'=>$phone,
             'image'=>$image_name
            ];
            
            if($image_name==''){
                unset($data['image']);
            }
            $table='users';
            if($user_type=="vendor"){
                $data['address']=$address;
                $table='vendors';
            }
            
            // Update profile
            if($this->Commonmodel->updateProfile($table,$auth_data->email,$data)){
                $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
                // Send the return data as reponse
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Profile Updated Successfully', 'info' => $user];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // add/remove fav offer
    public function addremovefav_post()
    {   
        try{
            // Validate request data
            $this->validate_request('fav');
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            
            // Extract user data from POST request
            $offer_id = $this->post('offer_id');
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_id=$user->id;
            $user_type=$user->user_type;
            if($user_type!='user'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a user to add/remove fav offer'], self::HTTP_BAD_REQUEST); 
            }
            
            $offer=$this->Commonmodel->getOfferById($offer_id);
            if(!$offer){$this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'No offer Found'], self::HTTP_BAD_REQUEST);}
            
            // FAV
            $returndata=$this->Commonmodel->fav($offer_id,$user_id);
                // Send the return data as reponse
                $fav=new stdClass;
                $fav->fav=$returndata;
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Fav '.(($returndata)?'added':'removed').' Successfully', 'info' => $fav];
                $this->response($response, $status);  
            
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // list all fav
    public function listfav_get()
    {   
        try{
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_id=$user->id;
            $user_type=$user->user_type;
            if($user_type!='user'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a user to list fav offers'], self::HTTP_BAD_REQUEST); 
            }
            
            // get fav list
            $list=$this->Commonmodel->getfavlist($user_id);
                // Send the return data as reponse
                foreach($list as $l){
                    $images_array=json_decode($l->images);
                    if(!empty($images_array)){
                    array_walk($images_array, function(&$value, $key) { $value = OFFER_IMAGE_URL.$value; } );
                    $l->images=$images_array;
                    }else{
                    $l->images=[DEFAULT_OFFER_IMAGE_URL];    
                    }
                }
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Fav Offers Shown Successfully','info'=>$list];
                $this->response($response, $status);  
            
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // validate device type (Android / IOS )
    public function validate_usertype($str)
    {   
        try{
            $field_value = $str; //this is redundant, but it's to show you how
            //the content of the fields gets automatically passed to the method
            $this->form_validation->set_message('validate_usertype','User Type can be user OR vendor');
            return ((in_array($field_value,array('user','vendor'))) ? TRUE : FALSE );
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
}
