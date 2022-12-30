<?php

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
class User_lib{

    function __construct()
    {
        $this->CI=& get_instance();
        $this->CI->load->model('User_model');
        $this->key=base64_decode($this->CI->config->item('jwt_key'));
    }

    public function __call($method, $arguments)
    {
        if (!method_exists($this->CI->User_model, $method)) {
            throw new Exception('Undefined method User_model::' . $method . '() called');
        }

        return call_user_func_array([$this->CI->User_model, $method], $arguments);
    }

    function login_check($dataarray)
    {
        if(empty($dataarray))
        return 'wrong credentials';

        $dataarray=array(
            "email"=>$dataarray['email'],
            "password"=>$dataarray['password'],
            );
          $userdata=$this->logincheck($dataarray);
          if($userdata=='false')
          return false;

          $jwt_data = array(
            'user_id' =>$userdata->id,
            'email'=>$userdata->email,
            'name'=>$userdata->username
        );
        
        $tokenId = base64_encode(random_bytes(32));
        $issuedAt = time();
        $notBefore = $issuedAt;
        $time=60*60;
        $expire = $notBefore + $time;   
        $data = [
            'iat' => $issuedAt, // Issued at: time when the token was generated
            'jti' => $tokenId, // Json Token Id: an unique identifier for the token
            'nbf' => $notBefore, // Not before
            'exp' => $expire, // Expire
            'data' => $jwt_data
        ];

        $token=JWT::encode($data,$this->key,'HS256');
        if(!empty($token))
        return $token;
        
        

    }

    function verify_token($token)
    {
    try{

    $decode=JWT::decode($token, new Key($this->key, 'HS256'));
    return $decode->data;
}
catch(Exception $e)
{

    return false;
}
      
    }
   
}