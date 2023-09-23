<?php

namespace App\Livewire;

use App\Models\GameSession;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Playground extends Component
{
    public $query; // Store the user's SQL query
    public $queryResult; // Store the result of the query
    public $selectedTable;
    public $selectedColumns;
    public $generatedQuery;
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
            // Execute the user's SQL query and store the result
            $this->queryResult = DB::select($this->query);
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
            // Save the session data to the database
            GameSession::query()->updateOrCreate([
                'game_name' => $this->gameName,
            ], [
                'session_data' => json_encode([
                    'selectedTable' => $this->selectedTable,
                    'selectedColumns' => $this->selectedColumns,
                    'joins' => $this->joins,
                    'whereClauses' => $this->whereClauses,
                    'whereConditions' => $this->whereConditions,
                ]),
            ]);

            // Display a success message or perform any other action as needed
            session()->flash('success', 'Session data saved successfully.');
        }else{
            session()->flash('error', 'Please enter name for the game :D');
        }
    }

    public function loadSession()
    {
        // Load the session data from the database based on the specified game name
        $session = GameSession::query()->where('game_name', $this->gameName)->first();

        if ($session) {
            $sessionData = json_decode($session->session_data, true);

            // Restore the session data
            $this->selectedTable = $sessionData['selectedTable'];
            $this->selectedColumns = $sessionData['selectedColumns'];
            $this->joins = $sessionData['joins'];
            $this->whereClauses = $sessionData['whereClauses'];
            $this->whereConditions = $sessionData['whereConditions'];

            // Display a success message or perform any other action as needed
            session()->flash('success', 'Session data loaded successfully.');
        } else {
            // Handle the case when the specified game session does not exist
            session()->flash('error', 'Game session not found.');
        }
    }
}
