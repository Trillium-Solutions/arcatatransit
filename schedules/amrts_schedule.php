<?php

// Include the configuration file for error management and such.
require_once ('../includes/config.inc.php'); 

// Connect to the database
require_once ('../postgres_connect.php');

if ($_GET['service'] == 'weekday') {$display_trips=array(200,212); $service_id=5; $trips_array_length=2;}
elseif ($_GET['service'] == 'weekend') {$display_trips=array(134); $service_id=6; $trips_array_length=1;}

$service_query = "select service_label from calendar where calendar_id=$service_id";
$service_result = db_query($service_query);

while ($row=db_fetch_array($service_result, MYSQL_ASSOC))
{$service_label=$row['service_label'];}

// Set the page title and include the HTML header.
$page_title = "AMRTS ".$service_label;

$css="td,th {font-size:10px;
	border-bottom: black;
	border-width: 1px 0px 0 0;
	border-style: solid none none  none;
	padding:0px
}
.colorback {background-color:#d3f4ff;}


";


include ('../includes/header.html');


echo "<p>Asterisks (*) indicate bus stops on demand at street intersections for identified route segment. Buses serving L.K. Wood Blvd. stop only on east side of street.</p>";


// query for trips

$trips_array_current_row=0;


while ($trips_array_current_row < $trips_array_length) {

$current_trip=$display_trips[$trips_array_current_row];

echo'<div style="float:left; padding-right:20px;">';
$trip_query = "select route_id,service_id from trips where trip_id=$current_trip";
$trip_result = db_query($trip_query);

while ($row=db_fetch_array($trip_result, MYSQL_ASSOC))
{$route_id=$row['route_id'];
$service_id=$row['service_id'];}

$route_query = "select route_short_name,route_long_name,route_color,route_desc from routes where route_id=$route_id";
$route_result = db_query($route_query);

while ($row=db_fetch_array($route_result, MYSQL_ASSOC))
{$route_short_name=$row['route_short_name'];
$route_long_name=$row['route_long_name'];
$route_color=$row['route_color'];
$route_desc=$row['route_desc'];}

// query for stops
$stop_times = "select time_format(arrival_time,':%i') AS arrival_time, time_format(departure_time,':%i') AS departure_time, time_format(arrival_time,'%p') AS am_pm, stops.stop_id,stops.stop_name, stops.zone_id,stops.stop_list_order, stops.stop_lat, stops.stop_lon,stops.map_key from stop_times inner join stops on stops.stop_id=stop_times.stop_id where stop_times.trip_id =$current_trip order by stop_times.stop_sequence asc";
$stops_result = db_query($stop_times);


echo "<table cellspacing=\"0\" style=\"border:4px solid #$route_color; padding:5px;\" width=\"150px\">
<tr><th colspan=\"3\">";
if ($route_short_name != "") {echo "$route_short_name-";}
if ($route_long_name != "") {echo "$route_long_name";}
echo "</th></tr>
<tr>";

echo "<td style=\"border:0px\"><i>(click name to show Google Maps)</i></td></tr>";


while ($row = db_fetch_array($stops_result, MYSQL_ASSOC)) {
echo '<tr>';

echo '<th><nobr><a href="http://maps.google.com/maps?f=q&hl=en&q='.$row['stop_lat'].'+,'.$row['stop_lon'].'&z=17">'.$row['stop_name'].'</a>&nbsp;&nbsp;&nbsp;</nobr></th>';


echo '<td>&nbsp;&nbsp;'.$row['arrival_time'].'</td>';

if ($row['arrival_time'] != $row['departure_time']) {echo '<tr><td colspan="3" style="text-align:right;border:0px;"><i>Departs</i> '.$row['departure_time'].'</td></tr>';}

echo '</td></tr>';
}

echo '<tr><td colspan="3">' . $route_desc . '</td></tr>';


echo '</table>
</div>';

$trips_array_current_row++;

}

?>


</body>

</html>