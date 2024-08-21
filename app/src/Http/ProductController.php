<?php

namespace Http;

use Services\DatabaseConnector;

class ProductController extends ApiBaseController
{

    private \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = DatabaseConnector::getConnection();
    }

    function increaseStock()
    {
        // authenticatie toevoegen // admin only
        $id = $this->httpBody['id'];
        $amount = $this->httpBody['amount'];
        $affectedRows = $this->db->prepare('UPDATE products SET stock = stock + ? WHERE id = ?')->executeStatement([$amount, $id]);
        if ($affectedRows === 0) {
            $this->message(404, 'Product not found');
            exit();
        }
        $this->message(200, 'stock increased');
    }

    function productInfo($id)
    {
        $product = $this->db->prepare('SELECT * FROM products WHERE id = ?')->executeQuery([$id])->fetchAllAssociative();
        echo json_encode(['product' => $product]);
    }

    /* Werkt */
    function deleteProduct($id)
    {
//        $user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
//
//        if(!$user){
//            echo json_encode(['message' => 'You have no authority for this']);
//            exit();
//        }
        $affectedRows = $this->db->prepare('DELETE FROM products WHERE id = ?')->executeStatement([$id]);
        if ($affectedRows == 0) {
            http_response_code(404);
            echo json_encode(['message' => 'product doesn\'t exist']);
        } else {
            echo json_encode(['message' => 'success']);
        }
    }

    /* Werkt */
    function getAllProducts()
    {
        $products = $this->db->fetchAllAssociative('SELECT * FROM products');
        echo json_encode(['products' => $products]);
    }

    function getIceCreams()
    {
        $category = isset($_GET['category']) ? (string)$_GET['category'] : '';
        $tag = isset($_GET['tag']) ? (string)$_GET['tag'] : '';
        if (trim($category != '')) {
            $stmt = $this->db->prepare('SELECT * FROM products join categories on products.category_id = categories.id WHERE categories.name = ?');
            $result = $stmt->executeQuery([$category]);
            $icecreams = $result->fetchAllAssociative();
            if (trim($tag) != '') {
                $stmt = $this->db->prepare('SELECT * FROM products join categories on products.category_id = categories.id join product_tag on products.id = product_tag.product_id join tags on product_tag.tag_id = tags.id WHERE categories.name = ? AND tags.name = ?');
                $result = $stmt->executeQuery([$category, $tag]);
                $ice = $result->fetchAllAssociative();
                echo json_encode(['products' => $ice]);
            } else {
                echo json_encode(['products' => $icecreams]);
            }
        } else if (trim($tag) != '') {
            $stmt = $this->db->prepare('SELECT * FROM products join product_tag on products.id = product_tag.product_id join tags on product_tag.tag_id = tags.id WHERE tags.name = ?');
            $result = $stmt->executeQuery([$tag]);
            $icecreams = $result->fetchAllAssociative();
            echo json_encode(['products' => $icecreams]);
        } else {
            $products = $this->db->fetchAllAssociative('SELECT * FROM products WHERE is_cake = 0');
            echo json_encode(['products' => $products]);
        }

    }

    function getIceCakes()
    {
        $tag = isset($_GET['tag']) ? (string)$_GET['tag'] : '';
        if (trim($tag) != '') {
            $stmt = $this->db->prepare('SELECT * FROM products join product_tag on products.id = product_tag.product_id join tags on product_tag.tag_id = tags.id WHERE tags.name = ?');
            $result = $stmt->executeQuery([$tag]);
            $cake = $result->fetchAllAssociative();
            echo json_encode(['products' => $cake]);
        } else {
            $products = $this->db->fetchAllAssociative('SELECT * FROM products WHERE is_cake = 1');
            echo json_encode(['products' => $products]);
        }

    }

    /* Werkt */
    function addProduct()
    {
//        $user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
//
//        if(!$user){
//            echo json_encode(['message' => 'You have no authority for this']);
//            exit();
//        }
        $this->db->prepare("INSERT INTO products (name, flavour, description, price, stock, category_id, is_cake) VALUES (?,?,?,?,?,?,?)")
            ->executeStatement([
                $this->httpBody['name'],
                $this->httpBody['flavour'],
                $this->httpBody['description'],
                $this->httpBody['price'],
                $this->httpBody['stock'],
                $this->httpBody['category_id'],
                $this->httpBody['is_cake']
            ]);
        if (isset($this->httpBody['tags'])) {
            $productId = $this->db->lastInsertId();
            foreach ($this->httpBody['tags'] as $tag) {
                $this->db
                    ->prepare('INSERT INTO product_tag (product_id, tag_id) VALUES(?,?)')
                    ->executeStatement([$productId, $tag]);

            }
        }
    }

    public function editProduct($id)
    {
//        $user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
//
//        if(!$user){
//            echo json_encode(['message' => 'You have no authority for this']);
//            exit();
//        }

//        $stmt = $this->db->prepare('SELECT id FROM products WHERE id = ?');
//        $result = $stmt->executeQuery([$id]);
//        $exist = $result->fetchAssociative();
//        if (!$exist) {
//            $this->message(404, 'product not found.' ); // 404 Not Found.
//            exit();
//        }
//        else {
//            $allOk = true;

//            if(trim($this->httpBody['name']) === ''){ $allOk = false; }
//            if(trim($this->httpBody['flavour']) === ''){ $allOk = false; }
//            if(trim($this->httpBody['price']) === ''){ $allOk = false; }
//            if(trim($this->httpBody['stock']) === ''){ $allOk = false; }
//            if(trim($this->httpBody['category_id']) === ''){ $allOk = false; }
//            if(trim($this->httpBody['is_cake']) === ''){ $allOk = false; }

//            if($allOk){
        $stmt = $this->db->prepare("UPDATE products SET name = ?, flavour = ?, description = ?, price = ?, stock = ?, category_id = ?, is_cake = ? WHERE id = ?");
//        $stmt->executeStatement();
        //voorbeeld: update products set price = "16.00" where id = 14
        $stmt ->executeStatement([
                        $this->httpBody['name'],
                        $this->httpBody['flavour'],
                        $this->httpBody['description'],
                        $this->httpBody['price'],
                        $this->httpBody['stock'],
                        $this->httpBody['category_id'],
                        $this->httpBody['is_cake'],
                        $this->httpBody['id']
                    ]);
//                if (isset($this->httpBody['tags'])) {
//                    $stmt = $this->db->prepare('SELECT tag_id FROM product_tag WHERE product_id = ?');
//                    $result = $stmt->executeQuery([$id]);
//                    $dbTagIds = $result->fetchAllAssociative();
//                    foreach ($this->httpBody['tags'] as $tag) {
//                        if(!in_array($tag, $dbTagIds)) {
//                            $this->db
//                                ->prepare('INSERT INTO product_tag (product_id, tag_id) VALUES(?,?)')
//                                ->executeStatement([$id, $tag]);
//                        }
//                    }
//                }
//                else -> delete tags?

        $this->message(204, 'edit was successful');
        exit();
//            }
//            else{
//                $this->message(422,'Some input fields are invalid');
//                exit();
//            }
//        }
    }
}