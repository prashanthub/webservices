<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Offer extends REST_Controller  {

    public function __construct() {
        parent::__construct();
 
    }

    // Create Offer
    public function createoffer_post()
    {   
        try{
            // Validate request data
            $this->validate_request('createoffer');
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_type=$user->user_type;
            if($user_type!='vendor'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a vendor to create an offer'], self::HTTP_BAD_REQUEST); 
            }
            // get vendor ID
            $vendor_id=$user->id;
            
            // Extract user data from POST request
            $oname = $this->post('name');
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');
            $description = $this->post('description');
            
 
            // Upload Images
            $images_names=[];
            if(!empty($_FILES['files']['name'])){
                $filesCount = count($_FILES['files']['name']);
                for($i = 0; $i < $filesCount; $i++){
                    $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                    $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['file']['error']     = $_FILES['files']['error'][$i];
                    $_FILES['file']['size']     = $_FILES['files']['size'][$i];
                    
                    $images_names[]=$this->uploadImage($name='file',$path='offer');
                    
                }
            }
            
            if(strtotime($start_date) >= strtotime($end_date)){
                // if start date is greater
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'End Date must be greater than Start Date'], self::HTTP_BAD_REQUEST); 
            }
            
            $data=[
             'name'=>$oname,  
             'vendor_id'=>$vendor_id,
             'start_date'=>$start_date,
             'end_date'=>$end_date,
             'description'=>$description,
             'images'=>json_encode($images_names)
            ];
            
            // Create Offer
            if($this->Commonmodel->insert($table='offers',$data)){
                // Send the return data as reponse
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Offer Created Successfully'];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // Edit Offer
    public function editoffer_post()
    {   
        try{
            // Validate request data
            $this->validate_request('editoffer');
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_type=$user->user_type;
            if($user_type!='vendor'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a vendor to create an offer'], self::HTTP_BAD_REQUEST); 
            }
            // get vendor ID
            $vendor_id=$user->id;
            
            // Extract user data from POST request
            $oname = $this->post('name');
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');
            $description = $this->post('description');
            $offer_id = $this->post('offer_id');
            
            $offer=$this->Commonmodel->getOfferById($offer_id);
            if(!$offer){$this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'No offer Found'], self::HTTP_BAD_REQUEST);}
            $oldImagesArray=json_decode($offer->images);
            $deleted_images_array=$this->post('delete_image');
            
             // Upload Images
            $images_names=[];
            if(!empty($_FILES['files']['name'])){
                $filesCount = count($_FILES['files']['name']);
                for($i = 0; $i < $filesCount; $i++){
                    $_FILES['file']['name']     = $_FILES['files']['name'][$i];
                    $_FILES['file']['type']     = $_FILES['files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                    $_FILES['file']['error']     = $_FILES['files']['error'][$i];
                    $_FILES['file']['size']     = $_FILES['files']['size'][$i];
                    
                    $images_names[]=$this->uploadImage($name='file',$path='offer');
                    
                }
            }

            $removed=false;
            if(empty($oldImagesArray)){
                $all_images=$images_names;
            }else{
                $all_images=array_merge($oldImagesArray,$images_names);
            }
            foreach($deleted_images_array as $single_image){
                if(in_array($single_image,$all_images)){
                    if (($key = array_search($single_image, $all_images)) !== false) {
                      $removed=true;
                      unset($all_images[$key]);
                    }
                    @unlink('uploads/offer/'.$single_image);
                }    
            }
            if(strtotime($start_date) >= strtotime($end_date)){
                // if start date is greater
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'End Date must be greater than Start Date'], self::HTTP_BAD_REQUEST); 
            }
            
            // Reindex array
            if($removed)
            $all_images = array_values($all_images);
            
            $data=[
             'name'=>$oname,  
             'vendor_id'=>$vendor_id,
             'start_date'=>$start_date,
             'end_date'=>$end_date,
             'description'=>$description,
             'images'=>json_encode($all_images)
            ];
            
            // Create Offer
            if($this->Commonmodel->updateoffer($table='offers',$data,$offer_id)){
                // Send the return data as reponse
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Offer Updated Successfully'];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // list my Offers
    public function myoffers_get()
    {   
        try{
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_type=$user->user_type;
            if($user_type!='vendor'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a vendor to list offers'], self::HTTP_BAD_REQUEST); 
            }
            // get vendor ID
            $vendor_id=$user->id;
            
            // get Offer
            if($list=$this->Commonmodel->getlist($table='offers',$vendor_id)){
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
                $response = ['status' => $status,'msg'=>'My Offers Shown Successfully','info'=>$list];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // all Offers
    public function alloffers_get()
    {   
        try{
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            
            // get Offer
            if($list=$this->Commonmodel->getlist($table='offers')){
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
                $response = ['status' => $status,'msg'=>'All Offers Shown Successfully','info'=>$list];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    // search
    public function search_get()
    {   
        try{
            $search_string = $this->get('q');
            
            $status = parent::HTTP_OK;
            $response = ['status' => $status,'msg'=>'Search data shown successfully','info'=>'This API is in development, will be avaliable soon'];
            $this->response($response, $status);  
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    //  delete Offer
    public function deleteoffer_delete()
    {   
        try{
            // Validate request data
            //$this->validate_request('deleteoffer');
            
            // Call the verification method and store the return value in the variable
            $auth_data = $this->authenticate_user();
            $user=$this->Commonmodel->check_user(['email'=>$auth_data->email,'password'=>sha1($auth_data->password)]);
            $user_type=$user->user_type;
            if($user_type!='vendor'){
                // if usertype is not vendor then throw error
                $this->response(['status'=>self::HTTP_BAD_REQUEST, 'msg' => 'You must be a vendor to delete offers'], self::HTTP_BAD_REQUEST); 
            }
            // get offer ID to delete
            $offer_id = $this->delete('offer_id');
            
            // Create Offer
            if($this->Commonmodel->delete($table='offers',$offer_id)){
                // Send the return data as reponse
                $status = parent::HTTP_OK;
                $response = ['status' => $status,'msg'=>'Offer deleted Successfully'];
                $this->response($response, $status);  
            }else{
                 $this->something_went_wrong();
            }
            
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
    
    
    // validate date time
    public function validate_datetime($str)
    {   
        try{
            $field_value = $str; //this is redundant, but it's to show you how
            //the content of the fields gets automatically passed to the method
            $this->form_validation->set_message('validate_datetime','Datetime format is not correct, it must be (Y-m-d H:i:s)');
            $d = DateTime::createFromFormat($format='Y-m-d H:i:s', $date=$field_value);
            // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
            $result=$d && $d->format($format) === $date;
            return ($result ? TRUE : FALSE );
            
        }catch (Exception $e) {
             $this->throw_exception($e);
        }
    }
}
