<?php
include_once('../classes/PDFClass.php');
include_once('tcpdf/tcpdf.php');
try {

    if (isset($_POST["jsonData"])) {
        $jsonDetails = json_decode($_POST["jsonData"], true);        
        $tableString = <<<_TABLE_
<style>
    table.grid_header th{
        text-align: center;
        padding: 2px 0;
        background-color: #D0DFEA;
        font-size:10;
        font-weight: normal;
    } 

    .grid_column {
        padding: 2px 0;
        text-align: left;
        font-size:9;
    }
    div.ss_mashup_element {
        text-align: left;
        font-weight: bold;
        text-decoration:underline;
    }

    div.ss_mashup_element_no_ul {
        text-align: left;
        font-weight: bold;
    }

    div.borderedDiv {
        background-color: #FFFFFF;
        border: .5px solid #000000;
        height: 10px;
        margin: 10px 0;
        width: 640px;
    }
    div.ss_mashup_entry_content{
        font-weight: normal;
        text-decoration:none;
    }
</style>
_TABLE_;
        $page1Data = array();
        $page2Data = array();
        $pageSeparator = false;
        $counter = 0;
        $pageTitle = "WEMS Report";
        $your_width = "216"; //mm
        $your_height = "279"; //mm
        if (is_array($jsonDetails)) {
            $jsonFormData = json_decode($_POST["formData"], true);
            foreach($jsonFormData as $pdfDefinition) {                
                if (is_array($pdfDefinition)) {
                    if (isset($pdfDefinition["pageSeparator"])) {
                        $pageSeparator = true;
                    }
                    if (isset($pdfDefinition["table"]) ) {
                        $singleTableString = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"0\">\n";
                        foreach($pdfDefinition["table"] as $table ) {                            
                            if (is_array($table)) {
                                if (isset($table["displayName"]) ) {
                                    $singleTableString .= "<tr>";
                                    $labelStyle = "width:100px; text-align:right;";
                                    $parameterStyle = "width:200px; text-align:right;";
                                    $cellParameters = "";
                                     
                                    if (isset($table["labelStyle"])) {
                                        $labelStyle = $table["labelStyle"];
                                    }
                                    if (isset($table["parameterStyle"])) {
                                        $parameterStyle = $table["parameterStyle"];
                                    }
                                    if (isset($table["cellParameters"])) {
                                        $cellParameters = $table["cellParameters"];
                                    }
                                     
                                    if (isset($table["displayName"])) {
                                        $singleTableString .= "<td style=\"$labelStyle\" $cellParameters> $table[displayName] </td>";
                                    }
                                    if (isset($table["parameter"])) {
                                        $value = $jsonDetails[$table["parameter"]];
                                        if (isset($table["preProcessFunction"])) {
                                            $value = $table["preProcessFunction"]($value);
                                        }
                                        $singleTableString .= "<td style=\"$parameterStyle\"> ".$value."</td>";
                                    }
                                    $singleTableString .=  "</tr>";
                                } else {
                                    $singleTableString .= "<tr>";
                                    foreach($table as $row) {
                                        $labelStyle = "width:100px; text-align:right;";
                                        $parameterStyle = "width:200px; text-align:right;";
                                        $cellParameters = "";

                                        if (isset($row["labelStyle"])) {
                                            $labelStyle = $row["labelStyle"];
                                        }
                                        if (isset($row["parameterStyle"])) {
                                            $parameterStyle = $row["parameterStyle"];
                                        }
                                        if (isset($row["cellParameters"])) {
                                            $cellParameters = $row["cellParameters"];
                                        }

                                        if (isset($row["displayName"])) {
                                            $singleTableString .= "<td style=\"$labelStyle\" $cellParameters> $row[displayName] </td>";
                                        }
                                        if (isset($row["parameter"])) {
                                            $value = $jsonDetails[$row["parameter"]];
                                            if (isset($row["preProcessFunction"])) {
                                                $value = $row["preProcessFunction"]($value);
                                            }
                                            $singleTableString .= "<td style=\"$parameterStyle\"> ".$value."</td>";
                                        }
                                    }
                                    $singleTableString .=  "</tr>";
                                }
                            }                            
                        }
                        $singleTableString .= "</table>";
                        $singleTableString .= "<br/><br/>";
                        if ($pageSeparator) {
                            $page2Data[] = $singleTableString;
                        } else {
                            $page1Data[] = $singleTableString;
                        }
                    }
                    if (isset($pdfDefinition["body"])) {
                        $bodyString = "";
                        foreach($pdfDefinition["body"] as $body ) {
                            $style = "";
                            if (isset($body["divStyle"])) {
                                $style = "style=\"". $body["divStyle"]."\"";
                            }
                            if (isset($body["displayName"])) {                                
                                $bodyString .= "<div $style>" . $body["displayName"] . "</div>";
                            }
                            if(isset($body["parameter"])) {
                                $bodyString .= $jsonDetails[$body["parameter"]];
                            }
                        }
                        if ($pageSeparator) {
                            $page2Data[] = $bodyString;
                        } else {
                            $page1Data[] = $bodyString;
                        }
                    }
                    if (isset($pdfDefinition["function"])){
                        $functionName = $pdfDefinition["function"]["functionName"];
                        if (function_exists($functionName)) {
                            if (isset($pdfDefinition["function"]["functionParams"])) {
                                $newTable = $functionName($pdfDefinition["function"]["functionParams"], $jsonDetails);
                                if ($pageSeparator) {
                                    $page2Data[] = $bodyString;
                                } else {
                                    $page1Data[] = $newTable;
                                }
                            } else {
                                $functionName();
                            }
                        }
                    }
                    if (isset($pdfDefinition["hidden"])) {
                        if ($pdfDefinition["hidden"]["pdfVariable"] == "pageTitle") {
                            if(isset($jsonDetails["pageTitle"])) {
                                $pageTitle = $jsonDetails["pageTitle"];

                                $your_width = "216"; //mm
                                $your_height = "356"; //mm
                            } else {
                                $pageTitle = $pdfDefinition["hidden"]["paramterName"];
                            }
                        }
                    }
                    ++$counter;
                } 
            }
        }        
        $pdf = new PDF();
        $outputFileName = "/tmp/wems_".uniqid().".pdf";
        $reportLogo = "itLogo.png";
        $logoWidth = 30;
        $reportTitle = $pageTitle;
        // set default header data
        $pdf->SetHeaderData($reportLogo, $logoWidth, $reportTitle, '');

        // set document information
        $pdf->SetTitle($reportTitle);
        $pdf->SetAuthor('Heta Desai');

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 12));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $orientation = "L";
        if ($pageSeparator) {
            //Make the first page Portrait with standard margins
            $your_width = "216"; //mm
            $your_height = "279"; //mm
            $orientation = "P";
        }
        $custom_layout = array($your_width, $your_height);
        $pdf->AddPage($orientation, $custom_layout);
        $pdf->SetFont('times', '', 10);
        $displayLocation_X = 15;
        $displayLocation_Y = 30;
        $pdf->SetXY($displayLocation_X, $displayLocation_Y);
        $pageInformation = $tableString . join("", $page1Data);
        $pdf->writeHTML($pageInformation, true, false, false, false, '');

        if ($pageSeparator) {
            $orientation = "L";
            $your_width = "216"; //mm
            $your_height = "356"; //mm

            $custom_layout = array($your_width, $your_height);
            $pdf->AddPage($orientation, $custom_layout);
            $pdf->SetFont('times', '', 10);
            $displayLocation_X = 15;
            $displayLocation_Y = 30;
            $pdf->SetXY($displayLocation_X, $displayLocation_Y);
            $pageInformation = $tableString . join("", $page2Data);
            $pdf->writeHTML($pageInformation, true, false, false, false, '');
        }

        $pdf->Output($outputFileName, "I");
    } else {
        throw new Exception("Missing data", 599);
    }
}
catch (Exception $Error) {
    print "Exception: " . $Error->getMessage() . "<br/>";
}