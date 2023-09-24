<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Playground extends Component
{
    public $query; // Store the user's SQL query
    public $queryResult; // Store the result of the query
    public $selectedTable;
    public $selectedColumns;
    public $queryTime;
    public $querySize;
    public $tableNames;
    public $joins = [];

    public $joinType = 'INNER JOIN';
    public $joinTable;
    public $joinLeft;
    public $joinOperator;
    public $joinRight;
    public $whereClauses = [];
    public $whereConditions;
    public $gameName;
    public $orderByColumns;
    public $orderByDirection = 'ASC';

    public function addWhere()
    {
        if ($this->whereConditions) {
            $this->whereClauses[] = [
                'condition' => $this->whereConditions,
            ];

            // Clear the input field after adding the condition
            $this->whereConditions = '';
        }
    }

    public function removeWhere($index)
    {
        if (isset($this->whereClauses[$index])) {
            unset($this->whereClauses[$index]);
            $this->whereClauses = array_values($this->whereClauses); // Reindex the array
        }
    }

    public function addJoin()
    {
        if ($this->joinType && $this->joinTable && $this->joinLeft && $this->joinOperator && $this->joinRight) {
            $this->joins[] = [
                'type' => $this->joinType,
                'table' => $this->joinTable,
                'left' => $this->joinLeft,
                'operator' => $this->joinOperator,
                'right' => $this->joinRight,
            ];

            // Clear input fields after adding the join
            $this->joinType = '';
            $this->joinTable = '';
            $this->joinLeft = '';
            $this->joinOperator = '';
            $this->joinRight = '';
        }
    }

    public function render()
    {
        return view('livewire.playground');
    }

    public function mount()
    {
        $this->tableNames = $this->getTableNames();
    }
//    public function updated(){
//        $this->generateQuery();
//    }
    public function executeQuery()
    {
        try {
            $startTime = microtime(true); // Record the start time
            // Execute the user's SQL query and store the result
            $results = $this->queryResult = DB::select($this->query);
            $endTime = microtime(true); // Record the end time
            $executionTime = round($endTime - $startTime, 4); // Calculate execution time in seconds
            $resultSizeKB = round(strlen(json_encode($results)) / 1024, 2);

            $this->queryTime = $executionTime;
            $this->querySize = $resultSizeKB;

        } catch (\Exception $e) {
            // Handle any exceptions or errors here
            $this->queryResult = 'Error: ' . $e->getMessage();
        }
    }

    public function generateQuery()
    {
        if ($this->selectedTable) {
            $columns = empty($this->selectedColumns) ? '*' : $this->selectedColumns;
            $this->query = "SELECT {$columns} FROM {$this->selectedTable}";
            foreach ($this->joins as $join) {
                $this->query .= " {$join['type']} {$join['table']} ON {$join['left']} {$join['operator']} {$join['right']}";
            }
            if (!empty($this->whereClauses)) {
                $whereConditions = [];
                foreach ($this->whereClauses as $whereClause) {
                    $whereConditions[] = $whereClause['condition'];
                }
                $this->query .= " WHERE " . implode(" AND ", $whereConditions);
            }
            if ($this->orderByColumns) {
                $this->query .= " ORDER BY {$this->orderByColumns} {$this->orderByDirection}";
            }
            $this->executeQuery();
        } else {
            $this->query = null;
        }
    }

    public function getTableNames()
    {
        $tables = DB::select('SHOW TABLES');
        return array_column($tables, 'Tables_in_' . env('DB_DATABASE'));
    }

    public function removeJoin($index)
    {
        if (isset($this->joins[$index])) {
            unset($this->joins[$index]);
            $this->joins = array_values($this->joins); // Reindex the array
        }
    }

    public function saveSession()
    {
        if (! empty($this->gameName)){
            // Save the session data
            Session::put("game.sessions.{$this->gameName}", [
                'selectedTable' => $this->selectedTable,
                'selectedColumns' => $this->selectedColumns,
                'joins' => $this->joins,
                'whereClauses' => $this->whereClauses,
                'whereConditions' => $this->whereConditions,
                'orderByColumns' => $this->orderByColumns,
                'orderByDirection' => $this->orderByDirection,
            ]);

            // Display a success message or perform any other action as needed
            session()->flash('success', 'Session data saved successfully.');
        }else{
            session()->flash('error', 'Please enter name for the game :D');
        }
    }

    public function loadSession()
    {
        $sessionData = Session::get("game.sessions.{$this->gameName}");

        if ($sessionData) {
            $this->selectedTable = $sessionData['selectedTable'];
            $this->selectedColumns = $sessionData['selectedColumns'];
            $this->joins = $sessionData['joins'];
            $this->whereClauses = $sessionData['whereClauses'];
            $this->whereConditions = $sessionData['whereConditions'];
            $this->orderByColumns = $sessionData['orderByColumns'];
            $this->orderByDirection = $sessionData['orderByDirection'];
            // GO
            $this->generateQuery();
            // Display a success message or perform any other action as needed
            session()->flash('success', 'Session data loaded successfully.');
        } else {
            // Handle the case when the specified game session does not exist
            session()->flash('error', 'Game session not found.');
        }
    }
}
