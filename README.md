# MySQL Playground

MySQL Playground is a web-based SQL query builder and visualization tool built with Laravel and Livewire. It allows you to create and execute SQL queries, visualize query results in real-time, and explore database tables effortlessly.

## Features

- Build SQL queries with a user-friendly interface.
- Visualize query results in real-time, including table data and query statistics.
- Save and load multiple sessions with custom game names.
- Restrict access to cloning, editing, or distributing code with a custom "All Rights Reserved" license.

## Getting Started

To get started with MySQL Playground, follow these steps:

1. Clone the repository to your local machine:

   ```bash
   git clone https://github.com/ahmedtofaha/mysql-playground-laravel.git
   ```
2. install project dependencies:
    ```bash
   cd mysql-playground
   composer install
   npm install && npm run dev
   ```
3. Set up your database configuration in the .env file.
4. Run the Laravel development server:
    ```bash
    php artisan serve
    ```
## Usage

1. **Select a Table**: Choose a table from the dropdown menu or specify custom columns by entering them (comma-separated) in the input field.

2. **Configure JOINs**:
    - Select the join type (INNER JOIN, LEFT JOIN, RIGHT JOIN) from the dropdown.
    - Choose the table to join with.
    - Specify the join condition by entering the left part, operator, and right part in the respective input fields.

3. **Define WHERE Conditions**:
    - In the "WHERE" section, enter conditions using the provided input field. You can add multiple conditions.

4. **Generate and Execute Query**: Click the "GO" button to generate and execute the SQL query based on your selections.

5. **View Query Results**: The query results, including table data and statistics, will be displayed in a table format below.

## Saving and Loading Sessions

- **Save Session**:
    - Enter a custom game name in the input field.
    - Click the "SAVE" button to save the current session for future use.

- **Load Session**:
    - Enter the previously saved game name in the input field.
    - Click the "LOAD" button to load a saved session.

**Note**: Make sure to abide by the licensing terms outlined in the [License](#LICENSE) section. This software is protected by an "All Rights Reserved" license, and unauthorized use is not permitted.
