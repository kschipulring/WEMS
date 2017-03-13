<?php 

require '../wemsDatabase.php';

$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

//$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;

$output = "";

$tforman = 0;
$tEmp = 0;
$tTotal = 0;

//$eventID = 86;

$qry = oci_parse($c, "select sum(g.EMP_ASSIGNED) as EMPTOTAL, e.DEPTCODE as DEPTCD from WEMS_GANG g, WEMS_EMPLOYEE e where g.FORMANID = e.EMPLOYEENUMBER and g.EVENTID = :EVENTID
group by e.DEPTCODE")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $empTotal = $row['EMPTOTAL'];
    $deptCd = $row['DEPTCD'];
    $dept = "";
    $formanTotal = 0;
    
    
    
    $qry2 = oci_parse($c, "select count(e.DEPTCODE) as FORMAN_CNT from WEMS_GANG g, WEMS_EMPLOYEE e where g.FORMANID = e.EMPLOYEENUMBER and 
        g.EVENTID = :EVENTID and e.DEPTCODE = :DEPARTMENTCD")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    oci_bind_by_name($qry2, ":EVENTID", $eventID, -1);
    oci_bind_by_name($qry2, ":DEPARTMENTCD", $deptCd, -1);
    
    oci_execute($qry2);
    
    
    
    while($row = oci_fetch_array($qry2))
    {
        $formanTotal = $row['FORMAN_CNT'];
    }
    
    $total = $empTotal + $formanTotal;
    
    
    $qry3 = oci_parse($c, "select DEPTNAME from DEPT where DEPTCODE = :DEPTCODE")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
   
            oci_bind_by_name($qry3, ":DEPTCODE", $deptCd, -1);
    
            oci_execute($qry3);
            
            while($row = oci_fetch_array($qry3))
            {
                $dept = $row['DEPTNAME'];
            }
    
    
    
   // echo $dept . " Total Forman = " . $formanTotal . " Total Employees = " . $empTotal . " Total = ". $total . "<br>";

           //$output .= $dept . "    Forman = " . $formanTotal . "     Employees = " . $empTotal . "   Total = ". $total . "<br><br>";
          $output.= "<tr><td>" . $deptCd . ":  </td>  <td> Forman = " . $formanTotal . "</td><td> Employees = " . $empTotal . "</td> <td> Total: " . $total . "</td></tr>";
         
          $tforman = $tforman + $formanTotal;
          $tEmp = $tEmp + $empTotal;
          $tTotal = $tTotal + $total;
}

 $output.= "<tr><td>______</td></tr><tr><td></td>  <td> Total Forman = " . $tforman . "</td><td> Total Employees = " . $tEmp . "</td> <td> Total: " . $tTotal . "</td></tr>";

?>