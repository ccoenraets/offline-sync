<?php

/*
 * RESTFul API for an employee directory application. Sandbox for offline-sync experimentation. Maintain a session-based
 * and updatable employee list that mimics a real-life database-powered backend while enabling multiple users to
 * experiment with CRUD operations on their isolated data set without compromising the integrity of a central database.
 */

require 'Slim/Slim.php';

session_start();

if (!isset($_SESSION['employees'])) {
    $_SESSION['employees'] = array(
        (object) array("id" => 1, "firstName" => "John", "lastName" => "Smith", "title" => "CEO", "officePhone" => "617-321-4567", "lastModified" => "2008-01-01 00:00:00", "deleted" => false),
        (object) array("id" => 2, "firstName" => "Lisa", "lastName" => "Taylor", "title" => "VP of Marketing", "officePhone" => "617-522-5588", "lastModified" => "2011-06-01 01:00:00", "deleted" => false),
        (object) array("id" => 3, "firstName" => "James", "lastName" => "Jones", "title" => "VP of Sales", "officePhone" => "617-589-9977", "lastModified" => "2009-08-01 16:30:24", "deleted" => false),
        (object) array("id" => 4, "firstName" => "Paul", "lastName" => "Wong", "title" => "VP of Engineering", "officePhone" => "617-245-9785", "lastModified" => "2012-05-01 08:22:10", "deleted" => false),
        (object) array("id" => 5, "firstName" => "Alice", "lastName" => "King", "title" => "Architect", "officePhone" => "617-744-1177", "lastModified" => "2012-02-10 22:58:37", "deleted" => false),
        (object) array("id" => 6, "firstName" => "Jen", "lastName" => "Brown", "title" => "Software Engineer", "officePhone" => "617-568-9863", "lastModified" => "2010-01-15 11:17:45", "deleted" => false),
        (object) array("id" => 7, "firstName" => "Amy", "lastName" => "Garcia", "title" => "Software Engineer", "officePhone" => "617-327-9966", "lastModified" => "2011-07-03 14:24:50", "deleted" => false),
        (object) array("id" => 8, "firstName" => "Jack", "lastName" => "Green", "title" => "Software Engineer", "officePhone" => "617-565-9966", "lastModified" => "2012-04-28 10:25:56", "deleted" => false)
    );
}

$app = new Slim(array(
    'debug' => false
));

$app->error(function ( Exception $e ) use ($app) {
    echo $e->getMessage();
});

$app->get('/employees',         'getEmployees');
$app->post('/employees',        'addEmployee');
$app->put('/employees/:id',     'updateEmployee');
$app->delete('/employees/:id',  'deleteEmployee');

$app->run();

function getEmployees() {
    if (isset($_GET['modifiedSince'])) {
        getModifiedEmployees($_GET['modifiedSince']);
        return;
    }
    $employees = $_SESSION['employees'];
    $result = array();
    foreach ($employees as $employee) {
        if (!$employee->deleted) {
            $result[] = $employee;
        }
    }
    echo json_encode($result);
}

// Get the employees that have been modified since the specified timestamp
// This is the cornerstone of this data sync solution
function getModifiedEmployees($modifiedSince) {
    if ($modifiedSince == 'null') {
        $modifiedSince = "1000-01-01";
    }
    $employees = $_SESSION['employees'];
    $result = array();
    foreach ($employees as $employee) {
        if ($employee->lastModified > $modifiedSince) {
            $result[] = $employee;
        }
    }
    echo json_encode($result);
}

// Add an employee to the session's employee list
function addEmployee() {
    $employees = $_SESSION['employees'];
    $l = sizeof($employees);
    // We don't allow more than 20 employees in this sandbox
    if ($l>19) {
        throw new Exception("You can only have 20 employees in this sandbox");
        return;
    }
    $request = Slim::getInstance()->request();
   	$body = $request->getBody();
   	$employee = json_decode($body);
    $employee->lastModified = date('Y-m-d H:i:s');
    $employee->deleted = false;
    $employee->id = sizeof($employees) + 1;
    $employees[] = $employee;
    $_SESSION['employees'] = $employees;
    echo json_encode($employee);
}

// Update an employee in the session's employee list
function updateEmployee($id) {
    $request = Slim::getInstance()->request();
   	$body = $request->getBody();
   	$employee = json_decode($body);
    $employee->lastModified = date('Y-m-d H:i:s');
    $employee->deleted = false;

    $employees = $_SESSION['employees'];
    $l = sizeof($employees);

    for($i = 0; $i < $l; ++$i)
    {
        if ($employees[$i]->id == $id) {
            array_splice($employees, $i, 1, array($employee));
            $_SESSION['employees'] = $employees;
            echo json_encode($employee);
            return;
        }
    }
}

// Delete the specified employee from the session's employee list
function deleteEmployee($id) {
    $employees = $_SESSION['employees'];
    $l = sizeof($employees);
    for($i = 0; $i < $l; ++$i)
    {
        if ($employees[$i]->id == $id) {
            $employees[$i]->lastModified = date('Y-m-d H:i:s');
            $employees[$i]->deleted = true;
            $_SESSION['employees'] = $employees;
            echo json_encode($employees[$i]);
            return;
        }
    }
}

?>