<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commonmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    // insert data
    public function insert($table='',$data){
        $this->db->insert($table,$data);
        return $this->db->insert_id();
    }
    
    // check email exist
    public function check_unique_mail($mail){
        $resultU=$this->db->where('email',$mail)->get('users')->row();
        $resultV=$this->db->where('email',$mail)->get('vendors')->row();
        if(!count($resultU) && !count($resultV)){
            // If no data exist in database
            return true;
        }else{
            return false;
        }
    }
    
    // check login details
    public function check_user($data){
        $mail=$data['email'];
        $password=$data['password'];
        $resultU=$this->db->where('email',$mail)->where('password',$password)->get('users')->row();
        $resultV=$this->db->where('email',$mail)->where('password',$password)->get('vendors')->row();
        if(!count($resultU) && !count($resultV)){
            // If no data exist in database
            return [];
        }elseif(count($resultU)){
            $resultU->user_type='user';
            $resultU->image=($resultU->image?PROFILE_IMAGE_URL.$resultU->image:DEFAULT_PROFILE_IMAGE_URL);
            return $resultU;
        }else{
            $resultV->user_type='vendor';
            $resultV->image=($resultV->image?PROFILE_IMAGE_URL.$resultV->image:DEFAULT_PROFILE_IMAGE_URL);
            return $resultV;
        }
    }
    
    // activate user
    public function activateuser($mail){
        $result=$this->db->where('email',$mail)->get('users')->row();
        $resultV=$this->db->where('email',$mail)->get('vendors')->row();
        if(!count($result) && !count($resultV)){
            // User does not exist
            return 'notexist' ;
        }else{
            if((count($result) && $result->status==1)  || (count($resultV) && $resultV->status==1)){
                // already activated
                return 'alreadyactivated';
            }
            if((count($result) && $result->status==0)  || (count($resultV) && $resultV->status==0)){
                // activate the user
                $this->db->where('email',$mail)->update('users',array('status'=>1));
                $this->db->where('email',$mail)->update('vendors',array('status'=>1));
                return 'activatedsuccessfully';
            }
        }

    }
    
    
    // check Email exist
    public function checkEmail($email){
        $resultU=$this->db->where('email',$email)->get('users')->row();
        $resultV=$this->db->where('email',$email)->get('vendors')->row();
        if(!count($resultU) && !count($resultV)){
            // User does not exist
            return false ;
        }
        return true;
    }
    
    
    // reset password
    public function resetPassword($email,$password){
        $table="";
        $resultU=$this->db->where('email',$email)->get('users')->row();
        $resultV=$this->db->where('email',$email)->get('vendors')->row();
        if(count($resultU)){
            // user
            $table="users";
        }
        if(count($resultV)){
            // vendor
            $table="vendors";
        }
        
        $this->db->where('email',$email)->update($table,array('password'=>sha1($password), 'status'=>1)); 
        return $this->db->affected_rows();
    }
    
    // update profile
    public function updateProfile($table,$email,$data){
        $this->db->where('email',$email)->update($table,$data); 
        return $this->db->affected_rows();
    }
    

    // get  offer list
    public function getlist($table,$vendor_id=NULL){
        if($vendor_id==NULL){
          return $this->db->get($table)->result();    
        }else{
          return $this->db->where('vendor_id',$vendor_id)->get($table)->result();
        } 
    }
    
    // get user fav
    public function getfavlist($user_id){
       $resutl=$this->db->where('user_id',$user_id)->get('favourite')->result();
       $offer_id=[];
       foreach($resutl as $res){
           $offer_id[]=$res->offer_id;
       }
       if(empty($offer_id)){
           return [];    
       }
       return $this->db->where_in('id',$offer_id)->get('offers')->result();
    }
    
    
    // delete offer
    public function delete($table='offers',$offer_id){
        $offer=$this->getOfferById($offer_id);
        if(isset($offer->images)){
        $imagesArray=($offer->images!=''?json_decode($offer->images):[]);
            foreach($imagesArray as $images){
                @unlink('uploads/offer/'.$images);
            }
        }
        $this->db->where('id',$offer_id)->delete($table); 
        return $this->db->affected_rows();
    }
    
    // get offer
    public function getOfferById($offer_id){
        return $this->db->where('id',$offer_id)->get('offers')->row(); 
    }
    

    // edit offer
    public function updateoffer($table,$data,$offer_id){
        $this->db->where('id',$offer_id)->update($table,$data); 
        return $this->db->affected_rows();
    }
    
    
    public function fav($offer_id,$user_id){
        $resutl=$this->db->where('offer_id',$offer_id)->where('user_id',$user_id)->get('favourite')->result(); 
        if(count($resutl)){
            // remove from fav 
             $this->db->where('offer_id',$offer_id)->where('user_id',$user_id)->delete('favourite'); 
             return 0;
        }else{
            // add fav  
             $this->db->insert('favourite',array('offer_id'=>$offer_id,'user_id'=>$user_id));
             return 1;
        }
    }
    
    
   
}


