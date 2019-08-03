<?php
	$isoDate = "2017-02-16T18:30:00.000+0000";

	//echo $date =  date_format(date_create($element->bill_from), 'Y-m-d, h-i a');
    //echo date(DATE_ISO8601, strtotime($isoDate));
    //echo $element->bill_date." , ";
    //echo date('c', strtotime($element->bill_from));
    
    //echo $date = date_create($element->bill_from, DATE_ISO8601);
    //echo date_format($date,"Y/m/d H:i:s");



	//echo date('Y-m-d', strtotime('-2 week +2'));
	//echo date('Y-m-d', strtotime('first day of -3 month'));
	//
	echo $newDate = (string) date("Y-m-d h:i", $isoDate);

?>