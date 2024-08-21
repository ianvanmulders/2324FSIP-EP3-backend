<?php

namespace Http;

use DateTime;
use Services\DatabaseConnector;
use Services\MailService;

class ContactController extends ApiBaseController
{

    private \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = DatabaseConnector::getConnection();
    }

    public function contact(){

    }
    public function contactSend(){
        $fname =  $this->httpBody['fname'];
        $lname = $this->httpBody['lname'];
        $email = $this->httpBody['email'];
        $address = $this->httpBody['address'];
        $message = $this->httpBody['message'];

        $allOk = true;
        if(trim($fname) == ''){
            $allOk = false;
        }
        else if(strlen(trim($fname)) <= 2){
            $allOk = false;
        }
        if(trim($lname) == ''){
            $allOk = false;
        }
        if(trim($email) == ''){
            $allOk = false;
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $allOk = false;
        }
        if(trim($message) == ''){
            $allOk = false;
        }

        if($allOk){
            MailService::send('gelatoGallery@hotmail.com', $email, 'I have a question', 'contact', $message, $fname, $lname);
            echo json_encode(['fname' => $fname, 'lname' => $lname, 'email' => $email, 'address' => $address, 'message' => $message]);
            $this->message(201,'email successfully sended');
            exit();
        }
        else{
            $this->message(422,'Some input fields are invalid');
            exit();
        }
    }

    public function orderPost(){
        $fname =  $this->httpBody['fname'];
        $lname = $this->httpBody['lname'];
        $fullName = $fname .' '. $lname;
        $email = $this->httpBody['email'];
        $pickupdate = $this->httpBody['pickupdate'];
        $note = $this->httpBody['note'];
        $products = $this->httpBody['products'];
        $errors = [];

        $allOk = true;
        if(trim($fname) == ''){
            $allOk = false;
            $errors[] = "give your first name";
        }

        if(trim($lname) == ''){
            $allOk = false;
            $errors[] = "give your last name";
        }
        else if(strlen(trim($lname)) <= 2){
            $allOk = false;
            $errors[] = "last name has to be more than two characters";
        }
        if(trim($email) == ''){
            $allOk = false;
            $errors[] = "give your email";
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $allOk = false;
            $errors[] = "give a correct email address";
        }
        if(trim($pickupdate)== ''){
            $allOk = false;
            $errors[] = "give a pickup date";
        }
        if(trim($note) == ''){
            $note= "No further information needed";
        }

        foreach ($products as $product){
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];

            if($product_id <= 0){
                $allOk = false;
                $errors[] = "This product does not exist";
            }
            else{
                $stmt = $this->db->prepare('SELECT id FROM products WHERE id = ?');
                $result = $stmt->executeQuery([$product_id]);
                $id = $result->fetchOne();
                if($id == 0){
                    $allOk = false;
                    $errors[] = "This product does not exist";
                }
                if($quantity <= 0){
                    $allOk = false;
                    $errors[] = "you have to order at least one item";
                }
                else{
                    $stmt = $this->db->prepare('SELECT stock FROM products WHERE id = ?');
                    $result = $stmt->executeQuery([$product_id]);
                    $stock = $result->fetchOne();
                    if($stock < $quantity){
                        $allOk = false;
                        $errors[] = "Not enough in stock";
                        //$this->message(422,'Not enough in stock');
                    }
                }
            }
            echo json_encode(['product_id' => $product_id, 'quantity'=> $quantity]);
        }
        if($allOk){
            $order_date = (new DateTime()) -> format('y-m-d h:i:s');
            $stmt = $this->db->prepare('INSERT INTO orders (order_date, pickup_date, note, name_client, email_client) VALUES (?, ?, ?, ?, ?)');
            $result = $stmt->executeQuery([$order_date, $pickupdate, $note, $fullName, $email]);
            $lastId = $this->db->lastInsertId();

            foreach($products as $product){
                $stmt = $this->db->prepare('SELECT stock FROM products WHERE id = ?');
                $result = $stmt->executeQuery([$product['product_id']]);
                $stock = $result->fetchOne();
                $newStock = $stock - $product['quantity'];

                $stmt1 = $this->db->prepare('INSERT INTO order_product (order_id, product_id, quantity) VALUES (?, ?, ?)');
                $result1 = $stmt1->executeQuery([$lastId, $product['product_id'], $product['quantity']]);

                $stmt = $this->db->prepare('UPDATE products SET stock = ? WHERE id = ?');
                $result = $stmt->executeQuery([$newStock, $product['product_id']]);
                $note.= '</br> I ordered '. $product['quantity'] . ' '. $product['name'] . '</br>';
            }

            MailService::send('gelatoGallery@hotmail.com', $email, 'I placed an order', 'order', $note, $fname, $lname);

            $this->message(201,'order is successfully placed');
            exit();
        }
        else{
            $this->message(422,'Some input fields are invalid:');
            foreach($errors as $error){
                $this->message(422, $error);
            }
            exit();
        }
    }

    public function orderTruckPost(){
        $fname =  $this->httpBody['fname'];
        $lname = $this->httpBody['lname'];
        $fullName = $fname .' '. $lname;
        $email = $this->httpBody['email'];
        //$address = $this->httpBody['address'];
        $note = $this->httpBody['note'];
        $pickupdate = $this->httpBody['pickupdate'];

        $allOk = true;
        if(trim($fname) == ''){
            $allOk = false;
        }
        else if(strlen(trim($fname)) <= 2){
            $allOk = false;
        }
        if(trim($lname) == ''){
            $allOk = false;
        }
        if(trim($email) == ''){
            $allOk = false;
        }
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $allOk = false;
        }
        if(trim($pickupdate)== ''){
            $allOk = false;
        }
        if(trim($note) == ''){
            $allOk = false;
        }

        if($allOk){
            $reservation_date = (new DateTime()) -> format('y-m-d h:i:s');
            $stmt = $this->db->prepare('INSERT INTO ice_truck_reservations (reservation_date, pickup_date, name_client, email_client, note) VALUES (?, ?, ?, ?, ?)');
            $result = $stmt->executeQuery([$reservation_date, $pickupdate, $fullName, $email, $note]);
            $note .= '</br> I ordered the icetruck.';
            MailService::send('gelatoGallery@hotmail.com', $email, 'I ordered the icetruck', 'order icetruck', $note, $fname, $lname);

            $this->message(201,'order is successfully placed');
            exit();
        }
        else{
            $this->message(422,'Some input fields are invalid');
            exit();
        }
    }
}