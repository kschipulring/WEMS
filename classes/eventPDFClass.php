<?php
require_once("PDFClass.php");
class eventPDF extends PDF {
    private $pdfTitle = "Long Island Rail Road - Platform Assignment Report"; 
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

    
    public function getEventData($eventId, $locationIds){
        $eventData = array();
        $eventObj = new event();        
        $result = $eventObj->getEventById($eventId);        
        if($result){
            while($row = oci_fetch_array($result[0])){                
                $eventData["event"] = $row;                
            }
        }        
        $cleanableTargetObj = new cleanableTarget();
        $locationObj = new location();            
        foreach($locationIds as $locationId){
            $locationResult = $locationObj->getLocationById($locationId);
            if($locationResult){
                while($row = oci_fetch_array($locationResult[0])){
                    $locationDetail = $row;
                }
            }                   
            $result = $cleanableTargetObj->getCleanableTargetByLocation($eventId, $locationId);
            if($result){
                $i = 1;
                while($row = oci_fetch_array($result[0])){                             
                    $eventData["location"][$locationDetail['MARKERNAME']][$i] =  $row;
                    $i++;    
                }
            }
         }        
        return $eventData;   
    }
    
    
    public function createEventPDF($formData){
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
            
            $eventData = $this->getEventData($formData["eventId"], $formData["locationId"]);         
            $this->eventName =  $eventData["event"]["EVENTDESC"] ." - ". $eventData["event"]["EXTERNALID"];
            if (isset($eventData['location'])) {               
                if (is_array($eventData["location"])) {                   
                    $tableString = "<table width=\"100%\"><thead><tr><th style=\"width:15%; text-align:right;background-color: #f0eec2;\">Location</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">Station</th>";
                    $tableString .= "<th style=\"width:14%; text-align:center;background-color: #f0eec2;\">Dept</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">Gang Forman</th>";
                    $tableString .= "<th style=\"width:3%; text-align:center;background-color: #f0eec2;\">Crew</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">Start Time</th>";
                    $tableString .= "<th style=\"width:15%; text-align:center;background-color: #f0eec2;\">End Time</th>";
                    $tableString .= "<th style=\"width:10%; text-align:center;background-color: #f0eec2;\">Assign By</th>";
                    $tableString .= "<th style=\"width:3%; text-align:center;background-color: #f0eec2;\">Qty Used</th></tr></thead>";
                    $tableString .= "<tbody>";
                    foreach($eventData["location"] as $key => $componentDetails){
                        
                        $location = "";
                        $station = "";
                        foreach($componentDetails as $component){   
                            if(empty($location)){
                                $name = $key;
                            }else {
                                $name = '';
                            }                   
                            if(empty($station) || $station !=  $component["FULLNAME"]){
                                $stationName =  $component["FULLNAME"];
                                $station =  $component["FULLNAME"];
                            } else {
                                
                                
                                $stationName = '';
                            }
                            $tableString .= "<tr><td style=\"width:15%; text-align:center;font-size: 8pt;\">".$name."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$stationName."</td>";
                            $tableString .= "<td style=\"width:14%; text-align:center;font-size: 8pt;\">".$component["DEPTABBR"]."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$component["EMPNAME"]."</td>";
                            $tableString .= "<td style=\"width:3%; text-align:center;font-size: 8pt;\">".$component["CREWSIZE"]."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$component["CTSTARTTIME"]."</td>";
                            $tableString .= "<td style=\"width:15%; text-align:center;font-size: 8pt;\">".$component["CTENDTIME"]."</td>";
                            $tableString .= "<td style=\"width:10%; text-align:center;font-size: 8pt;\">".$component["CTNOTEUSER"]."</td>";
                            $tableString .= "<td style=\"width:3%; text-align:center;font-size: 8pt;\">".$component["CTBAGS"]."</td></tr>"; 
                            $location = $key;
                        }
                    }
                    $tableString .= "</tbody></table>";
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