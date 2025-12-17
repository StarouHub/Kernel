<?php
// Simple database simulation for school project
class SimpleDB {
    private $dataFile = 'data.json';
    
    public function __construct() {
        // Create data file if it doesn't exist
        if (!file_exists($this->dataFile)) {
            $initialData = [
                'investments' => [],
                'tenders' => [],
                'transactions' => []
            ];
            file_put_contents($this->dataFile, json_encode($initialData));
        }
    }
    
    public function getData() {
        return json_decode(file_get_contents($this->dataFile), true);
    }
    
    public function saveData($data) {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
        return true;
    }
    
    public function addInvestment($investment) {
        $data = $this->getData();
        $data['investments'][] = $investment;
        return $this->saveData($data);
    }
    
    public function addTender($tender) {
        $data = $this->getData();
        $data['tenders'][] = $tender;
        return $this->saveData($data);
    }
    
    public function addTransaction($transaction) {
        $data = $this->getData();
        $data['transactions'][] = $transaction;
        return $this->saveData($data);
    }
}

// Simple usage example:
// $db = new SimpleDB();
// $db->addInvestment(['id' => 1, 'project' => 'Test']);
?>
