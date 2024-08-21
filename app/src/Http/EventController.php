<?php

namespace Http;

use Services\DatabaseConnector;

class EventController extends ApiBaseController
{
    private \Doctrine\DBAL\Connection $db;
    public function __construct()
    {
        parent::__construct();
        $this->db = DatabaseConnector::getConnection();
    }

    function addEvent() {
        $statement = $this->db->prepare('INSERT INTO events (name, date, url, is_closed) VALUES(?,?,?,?)');
        $statement->executeStatement([
            $this->httpBody['name'],
            $this->httpBody['date'],
            $this->httpBody['url'],
            $this->httpBody['is_closed']]);
    }

    function getEvents() {
        $events = $this->db->executeQuery('SELECT * FROM events ORDER BY date ASC')->fetchAllAssociative();
        //print_r($events);
        echo json_encode(['events' => $events]);
    }
}