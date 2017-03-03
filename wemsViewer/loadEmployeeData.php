<?php 

    require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
    
    $qry = oci_parse($c, "delete from WEMS_EMPLOYEE")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
 
    oci_execute($qry);
    
    $qry2 = oci_parse($c, "insert into WEMS.WEMS_EMPLOYEE (EMPLOYEENUMBER, DEPTCODE, FST_NME, LST_NME, DIV_CD)
                            select  EMP_NUM, DEPT_CD, FST_NME, LST_NME, DIV_CD FROM VW_MDS_GEN_ALL where DIV_CD is not null")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
    
    oci_execute($qry2);
    
   
    
    
    ?>