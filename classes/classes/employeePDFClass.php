<?php
require_once("PDFClass.php");
class employeePDF extends PDF {
    private $pdfTitle = "Long Island Rail Road - Department Wise Employee Report"; 
    private $eventName;
    
    public function __construct() {
        parent::__construct();
    }
    
    
    
    // Page footer
    public function Footer() {
        //Position at 15 mm from bottom
        $this->SetY(-15);
        //Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(50, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(100, 10, $this->eventName, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(100, 10, " Printed on " .date("m/d/Y H:i:s"), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    
    public function getEmployeeData($eventId){
        $eventData = array();
        $eventObj = new event();        
        $result = $eventObj->getEventById($eventId);        
        if($result){
            while($row = oci_fetch_array($result[0])){                
                $eventData["event"] = $row;                
            }
        }        

        $gangObj = new gang();    
        $departmentResult = $gangObj->getGangTotalByDepartment($eventId);
        if($departmentResult){
            $i=0;
            while($row = oci_fetch_array($departmentResult[0])){
                $eventData['department'][$i] =  $row;
                $i++;
            }
        }    
            
        return $eventData;   
    }
    
    
    public function createEmployeePDF($formData){
        $eventData = array();
        ob_clean();        
        try {  
            $page1Data = array();
            $page2Data = array();
            $pageSeparator = false;
            $counter = 0;
            $pageTitle = $this->pdfTitle;
            $your_width = "216"; //mm
            $your_height = "279"; //mm
            $outputFileName = "/tmp/wems_".uniqid().".pdf";
            $tableString = '';
            
            $eventData = $this->getEmployeeData($formData["eventId"]);         
            $this->eventName =  $eventData["event"]["EVENTDESC"] ." - ". $eventData["event"]["EXTERNALID"];
            if (isset($eventData['department'])) {               
                if (is_array($eventData["department"])) {                   
                    $tableString = "<table width=\"100%\"><thead><tr><th style=\"width:55%; text-align:center;background-color: #f0eec2;\">Department</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">No Of Forman</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">No Of Employee</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">Total Crew Size</th>";                    
                    $tableString .= "</tr></thead>";
                    $tableString .= "<tbody>";
                    $grandTotalForman = 0;
                    $grandTotalEmployee = 0;
                    $grandTotalCrewSize = 0;
                    foreach($eventData['department'] as $key => $departmentDetails){  
                            $grandTotalForman = $grandTotalForman +  $departmentDetails['NOOFFORMAN'];
                            $grandTotalEmployee = $grandTotalEmployee +  $departmentDetails['NOOFEMPLOYEE'];
                            $grandTotalCrewSize = $grandTotalCrewSize +  $departmentDetails['TOTALCREWSIZE'];
                            $tableString .= "<tr><td style=\"width:55%; text-align:center;font-size: 8pt;\">".$departmentDetails['DEPTNAME']."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$departmentDetails['NOOFFORMAN']."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$departmentDetails['NOOFEMPLOYEE']."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$departmentDetails['TOTALCREWSIZE']."</td>";                            
                            $tableString .= "</tr>";
                    }
                    $tableString .= "</tbody>";
                    $tableString .= "<tfoot><tr><td style=\"width:55%; text-align:center;background-color: #f0eec2;\"></td>";
                    $tableString .= "<td style=\"width:15%; text-align:center;background-color: #f0eec2;\">".$grandTotalForman."</td>";
                    $tableString .= "<td style=\"width:15%; text-align:center;background-color: #f0eec2;\">".$grandTotalEmployee."</td>";
                    $tableString .= "<td style=\"width:15%; text-align:center;background-color: #f0eec2;\">".$grandTotalCrewSize."</td>";
                    $tableString .= "</tr></tfoot></table>";
                }
            }  
                 
            $reportLogo = "itLogo.png";
            $logoWidth = 30;
            $reportTitle = $pageTitle;           
            
            // set default header data
            $this->SetHeaderData($reportLogo, $logoWidth, $reportTitle, '');
            
            // set header and footer fonts
            $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));
            $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            
            // set default monospaced font
            $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            
            //set margins
            $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $this->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->SetFooterMargin(PDF_MARGIN_FOOTER);

            $orientation = "L";
            $custom_layout = array($your_width, $your_height);
            $this->AddPage($orientation, $custom_layout);
                        
            //set auto page breaks
            $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            //Column titles
            
            $this->writeHTML($tableString, true, false, false, false, '');
                        
            $this->Output($outputFileName, "I");
            ob_end_flush();
            
        }catch (Exception $Error) {
            print "Exception: " . $Error->getMessage() . "<br/>";
        }
    }
}
?>