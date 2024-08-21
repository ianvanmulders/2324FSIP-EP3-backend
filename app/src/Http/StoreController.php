<?php

namespace Http;

use Services\DatabaseConnector;

class StoreController extends ApiBaseController
{
    private \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = DatabaseConnector::getConnection();
    }

    public function dashboard(){

    }
    public function getPopups(){
        $currentDate = date('Y-m-d');
        $popups = $this->db->prepare('SELECT * FROM popups WHERE popups.end_date >= ? ')->executeQuery([$currentDate])->fetchAllAssociative();
        echo json_encode(['popups' => $popups]);
    }

    public function login(){
        $email = $this->httpBody['email'];
        $password = $this->httpBody['password'];

        $allOk = true;
        if(trim($email)== ''){
            $allOk = false;
        }
        else{
            $stmt = $this->db->prepare('SELECT password FROM users WHERE email = ?');
            $result = $stmt->executeQuery([$email]);
            $pass = $result->fetchOne();
        }

        if(trim($password) == ''){
            $allOk = false;
        }
        else if($pass == 0){
            $allOk = false;
        }
        else if(!password_verify($password,$pass)){
            $allOk = false;
        }

        if($allOk){
            $stmt = $this->db->prepare('SELECT first_name FROM users WHERE email = ?');
            $result = $stmt->executeQuery([$email]);
            $fname = $result->fetchOne();

            $_SESSION['user'] = true;
            $_SESSION['name'] = $fname;
            $this->message(201,'successfully logged in');
            exit();
        }
        else{
            $this->message(422,'email or password are incorrect');
            exit();
        }
    }

    public function logout(){
        session_destroy();
        $this->message(201,'successfully logged out');
        exit();
    }
}