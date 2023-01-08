<?php
defined('BASEPATH') OR exit('No direct script access allowed');





require str_replace('application','',APPPATH . '/vendor/autoload.php');


 require(APPPATH.'/libraries/REST_Controller.php');  

class Users extends REST_Controller{
    public function __construct() {
        parent::__construct();
         $this->load->database();
         $this->load->library('User_lib');
    }

    function signup_post()
    {
      $input_json = $this->input->raw_input_stream;
   

      $input_data = json_decode($input_json, true);

    
    $this->load->library('form_validation');

   $this->form_validation->set_data($input_data);


    $this->load->library('form_validation');
    $config =array(
        array(
            'field' => 'username',
            'label' => 'User Name',
            'rules' => 'trim|required|min_length[2]|max_length[200]'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'trim|required|valid_email|is_unique[users.email]'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'trim|required|min_length[8]|max_length[10]'
        )
    );

    $this->form_validation->set_rules($config);
    if (!$this->form_validation->run()) {
        $this->response([
            'status' => false,
            'message' => strip_tags(validation_errors())
        ], 404);
    }

else
{

    $dataarray=array(
        "username"=>$input_data['username'],
        "email"=>$input_data['email'],
        "password"=>password_hash($input_data['password'], PASSWORD_BCRYPT),
        );
        
 $indertid=$this->user_lib->insert($dataarray);
 if(!empty($indertid))
 {
    
    $this->response([
        'status' => true,
        'msg'=>'successfully signup'
    ], 200);

 }


}
    

  

    }
    public function login_post()
    {
       
        $input_json = $this->input->raw_input_stream;
   

        $input_data = json_decode($input_json, true);
  
      
      $this->load->library('form_validation');
  
     $this->form_validation->set_data($input_data);
  
  
      $this->load->library('form_validation');
      $config =array(
         array(
              'field' => 'email',
              'label' => 'Email',
              'rules' => 'trim|required|valid_email'
          ),
          array(
              'field' => 'password',
              'label' => 'password',
              'rules' => 'trim|required|min_length[8]|max_length[10]'
          )
      );
  
      $this->form_validation->set_rules($config);
      if (!$this->form_validation->run()) {
          $this->response([
              'status' => false,
              'message' => strip_tags(validation_errors())
          ], 404);
      }
  
  else
  {
  
      $dataarray=array(
          "email"=>$input_data['email'],
          "password"=>$input_data['password'],
          );
        $token=$this->user_lib->login_check($dataarray);
        if($token=='false' || empty($token))
        {
          
          return  $this->response([
                'status' => true,
                'msg'=>'unauthorized login'
                
            ], 404);

        }
        else
        {

            $this->response([
                'status' => true,
                'msg'=>'successfully login',
                'data' => $token
            ], 200);
        }
        
 
  
  
  }
      
  
    
        
    }

    function alldata_get()
    {
        
        $input_json = $this->input->get_request_header('Authorization');
        $verify=$this->user_lib->verify_token($input_json);
        
        if($verify=='' || $verify=='false')
         exit('Token is not valid');

        print_r($verify);die;
    }
}