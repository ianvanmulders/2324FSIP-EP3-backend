<?php

namespace Http;

use Services\DatabaseConnector;

class PopupController extends ApiBaseController
{

    private \Doctrine\DBAL\Connection $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = DatabaseConnector::getConnection();
    }
    public function getPopups(){
        $currentDate = date('Y-m-d');
        $popups = $this->db->prepare('SELECT * FROM popups WHERE popups.end_date >= ? ')->executeQuery([$currentDate])->fetchAllAssociative();
        echo json_encode(['popups' => $popups]);
    }

    public function createPopup() {
        $startDate = $this->httpBody['start_date'];
        $endDate = $this->httpBody['end_date'];
        $frequency = $this->httpBody['frequency_in_days'];
        $userId = $this->httpBody['user_id'];
        $statement = $this->db->prepare('INSERT INTO popups (start_date, end_date, frequency_in_days, user_id) VALUES (?,?,?,?)');
        $result = $statement->executeStatement([$startDate, $endDate, $frequency, $userId]);
        if ($result === 0) {
            $this->message(405, 'not allowed');
            exit();
        }
        $this->message(200, 'succes');
    }
}