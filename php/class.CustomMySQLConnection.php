<?php
	class CustomMySQLConnection {
		// MySQL server's information
		private $host = 'localhost';
		private $user = 'root';
		private $pass = '';
		private $db   = 'comp1640';

		private $sql_connection = ''; // Initialize an empty string variable before assign it to be the type of sql connection

		// Function to open a connection to MySQL server
		function openConnection() {
			$this->sql_connection = mysqli_connect($this->host,$this->user,$this->pass,$this->db);
		}

		// Function to close SQL connection
		function closeConnection() {
			mysqli_close($this->sql_connection);
		}

		// Function to execute SELECT and all kind of SQL statement. Then return the result
		public function executeSELECT($query_statement) {
			// Firstly, open a new connection
			$this->openConnection();

			// Then execute SQL statement
			$result_set = mysqli_query($this->sql_connection,$query_statement);

			// Finally close connection and return result
			$this->closeConnection();

			// Return mysqli_result object for successful QUERY statement
			// - TRUE for successful DML statement
			// - FALSE for failed DML statement
			return $result_set;
		}

		// Function to fix escape string
		public function fixEscapeString($string) {
			$this->openConnection();
			$fixed_string = mysqli_real_escape_string($this->sql_connection, $string);
			$this->closeConnection();
			return $fixed_string;
		}

		// Function to get the most recent error description as string
		public function getDbConnectionError() {
			return mysqli_error($this->sql_connection);
		}
	}

	// Create an instance for above class
	$connection = new CustomMySQLConnection();
?>