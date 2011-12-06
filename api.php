<?php
# Connect Database
try {
	$db = new PDO("sqlite:todo.db");
} catch(PDOException $e) {
	echo $e->getMessage();
	exit(1);
}

# Get Requests
if(isset($_GET) and $_GET) {
	$get = $_GET['get'];

	switch($get) {
	case "all":
		echo get_all();
	break;
	case "one":
		$id = $_GET['id'];
		echo get_one($id);
	break;
	default:
		echo "Invalid request!";
	}
}

# Update (state change) Requests
if(isset($_POST) and $_POST) {
	$type = $_POST['type'];

	switch($type) {
	case "add":
		$subject = $_POST['subject'];
		echo add($subject);
	break;
	case "delete":
		$id = $_POST['id'];
		echo delete($id);
	break;
	case "done":
		$id = $_POST['id'];
		echo done($id);
	break;
	case "undo":
		$id = $_POST['id'];
		echo undo($id);
	break;
	default:
		echo "Invalid request!";
	}
}

# Functions
function get_all() {
	global $db;

	try {
		$sql = "SELECT * FROM tasks LIMIT 20";
		$result = $db->query($sql);

		$result->setFetchMode(PDO::FETCH_ASSOC);

		$tasks = array();
		while($row = $result->fetch()) {
			$tasks[] = $row;
		}

		return json_encode($tasks);

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

function get_one($id) {
	global $db;

	if(!$id) return false;

	try {
		$sql = "SELECT * FROM tasks WHERE id = $id";
		$result = $db->prepare($sql);
		$result->execute();

		$result->setFetchMode(PDO::FETCH_ASSOC);
		return json_encode($result->fetch());

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

function add($subject) {
	global $db;

	if(!$subject) return false;

	try {
		$sql = "INSERT INTO tasks (done, subject) VALUES (0, '$subject')";
		$result = $db->prepare($sql);
		$result->execute();

		return $db->lastInsertId();

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

function done($id) {
	global $db;

	if(!$id) return false;

	try {
		$sql = "UPDATE tasks SET done=1 WHERE id=$id";
		$result = $db->prepare($sql);
		$result->execute();

		return $result->rowCount();

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

function undo($id) {
	global $db;

	if(!$id) return false;

	try {
		$sql = "UPDATE tasks SET done=0 WHERE id=$id";
		$result = $db->prepare($sql);
		$result->execute();

		return $result->rowCount();

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

function delete($id) {
	global $db;

	if(!$id) return false;

	try {
		$sql = "DELETE FROM tasks WHERE id=$id";
		$result = $db->prepare($sql);
		$result->execute();

		return $result->rowCount();

	} catch(PDOException $e) {
		return $e->getMessage();
	}
}

