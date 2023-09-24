<div class="mt-4 pr-4 pl-4">
    <div class="row">

        <div class="col-md-7">
            @if($query)
                <div class="row mb-1">
                    <div class="col-md-12">
                        <p> QUERY : <span style="background-color: wheat">{{$query}}</span></p>
                    </div>
                </div>
            @endif
            @if($queryResult)
                <div class="row mt-1">
                    <div class="col-md-12">
                        <p>Query Execution Time: {{ $queryTime }} seconds</p>
                        <p>Result Size: {{ $querySize }} KB</p>
                    </div>
                </div>
                @if(is_array($queryResult))
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        @foreach($queryResult[0] as $key => $value)
                                            <th>{{ $key }}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($queryResult as $row)
                                        <tr>
                                            @foreach($row as $value)
                                                <td>{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div>
                        <b>{{$queryResult}}</b>
                    </div>
                @endif
            @endif
        </div>
        <div class="col-md-5">
            <b>| Columns</b>
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter columns (comma-separated)"
                               wire:model="selectedColumns">
                    </div>
                </div>
            </div>
            <b>| Table</b>
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <select class="form-control" wire:model="selectedTable">
                            <option value="">From a table</option>
                            @foreach($tableNames as $tableName)
                                <option value="{{ $tableName }}">{{ $tableName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Order by columns (comma-separated)"
                               wire:model="orderByColumns">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <select class="form-control" wire:model="orderByDirection">
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                </div>
            </div>

            <b>| JOINs</b><br>
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <select class="form-control" wire:model="joinType">
                            <option value="INNER JOIN">INNER JOIN</option>
                            <option value="LEFT JOIN">LEFT JOIN</option>
                            <option value="RIGHT JOIN">RIGHT JOIN</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <select class="form-control" wire:model="joinTable">
                            <option value="">Select a table to join</option>
                            @foreach($tableNames as $tableName)
                                <option value="{{ $tableName }}">{{ $tableName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter left part" wire:model="joinLeft">
                        <input type="text" class="form-control" placeholder="Enter operator" wire:model="joinOperator">
                        <input type="text" class="form-control" placeholder="Enter right part" wire:model="joinRight">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" wire:click="addJoin">Add Join</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display the added joins -->
            @foreach($joins as $index => $join)
                <p><b>Join {{ $join['table'] }}</b></p>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <div class="input-group mb-2">
                            <select class="form-control" wire:model="joins.{{ $index }}.type">
                                <option value="INNER JOIN">INNER JOIN</option>
                                <option value="LEFT JOIN">LEFT JOIN</option>
                                <option value="RIGHT JOIN">RIGHT JOIN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" placeholder="Enter left part"
                                   wire:model="joins.{{ $index }}.left">
                            <input type="text" class="form-control" placeholder="Enter operator"
                                   wire:model="joins.{{ $index }}.operator">
                            <input type="text" class="form-control" placeholder="Enter right part"
                                   wire:model="joins.{{ $index }}.right">
                            <div class="input-group-append">
                                <button class="btn btn-danger" wire:click="removeJoin({{ $index }})">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <b>| WHERE</b><br>
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Enter conditions" wire:model="whereConditions">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" wire:click="addWhere">Add Condition</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display the added WHERE conditions with edit and delete options -->
            @foreach($whereClauses as $index => $where)
                <div class="row mt-2">
                    <div class="col-md-12">
                        <p><b>Condition {{ $index + 1 }}</b></p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" placeholder="Enter condition"
                                   wire:model="whereClauses.{{ $index }}.condition">
                            <div class="input-group-append">
                                <button class="btn btn-danger" wire:click="removeWhere({{ $index }})">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <div class="input-group-append justify-content-center" dir="rtl">
                    <button class="btn btn-success m-1" wire:click="generateQuery">GO</button>
                    <button class="btn btn-primary m-1" wire:click="saveSession">SAVE</button>
                    <button class="btn btn-info m-1" wire:click="loadSession">LOAD</button>
                </div>
            </div>
            <div>
                <input type="text" class="form-control" placeholder="Enter game name" wire:model="gameName">
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

        </div>
    </div>

</div>
