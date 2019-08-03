<?php
$this->load->view('admin/templates/header.php');
?>

<style>
#map 
{ 
height: 800px; 
}
</style>

<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php echo $heading; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'addeditlocation_form');
						echo form_open('admin/location/updateLocationBoundary',$attributes) 
					?> 		
	 						<ul>
                                <li>
									<div class="form_grid_12">
										<div id="map"></div>
									</div>
								</li>
								<input type="hidden" name="boundayVal" id="boundayVal" value=""/>
								<input type="hidden" name="location_id" value="<?php if($form_mode){ echo $locationdetails->row()->_id; } ?>"/>
								<li>
									<div class="form_grid_12">
										<div class="form_input">
											<button type="submit" class="btn_small btn_blue" tabindex="4"><span><?php if ($this->lang->line('admin_common_update') != '') echo stripslashes($this->lang->line('admin_common_update')); else echo 'Update'; ?></span></button>
										</div>
									</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
			</div>
		</div>
		<span class="clear"></span>
</div>
<?php

$oldcoordinatesArr = array();
if(isset($locationdetails->row()->loc)){
	$oldcoordinatesArr = $locationdetails->row()->loc['coordinates'][0];
	
	$noco = array();
	foreach($oldcoordinatesArr as $key=>$val){
		$noco[] = array_reverse($val);
	}
	$oldcoordinatesArr = $noco;
	#print_r($oldcoordinatesArr); die;
	unset($oldcoordinatesArr[count($oldcoordinatesArr)-1]);
}
$map_radius = 10;
if(isset($locationdetails->row()->map_searching_radius)){
	$map_radius = intval($locationdetails->row()->map_searching_radius/1000);
}
$map_language = "";
if($langCode!=''){
	$map_language = '&language='.$langCode;
}

?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=drawing&key=<?php echo $this->config->item('google_maps_api_key');?><?php echo $map_language; ?>"></script>
<script type="text/javascript">
var Lat = <?php echo $locationdetails->row()->location['lat']; ?>;
var Long = <?php echo $locationdetails->row()->location['lng']; ?>;

var lat_longs = new Array();
var markers = new Array();
var drawingManager;

function initialize() {
	var myLatlng = new google.maps.LatLng(Lat, Long);
	var map_options = {
            zoom: 10,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
            };
    map = new google.maps.Map( document.getElementById('map'), map_options );
	
}

function overlayClickListener(overlay) {
    google.maps.event.addListener(overlay, "mouseup", function(event){
        $('#boundayVal').val(overlay.getPath().getArray());
    });
}
function getPoints() {
    $('#boundayVal').val(google.maps.getPath().getArray());
}

function create_polygon(coordinates) {
    var icon = {
        path: google.maps.SymbolPath.CIRCLE,
        //path: "M -1 -1 L 1 -1 L 1 1 L -1 1 z",
        strokeColor: "#3399FF",
        strokeOpacity: 0,
        fillColor: "#FFFFFF",
        fillOpacity: 1,
        scale: 3
    };

     var polygon = new google.maps.Polygon({
        map: map,
        paths: coordinates,
        strokeColor: "#3399FF",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#3399FF",
        fillOpacity: 0.5,
        zIndex: -1
    });

    var marker_options = {
        map: map,
        icon: icon,
        flat: true,
        draggable: true,
        raiseOnDrag: false
    };
    
    for (var i=0; i<coordinates.length; i++){
        marker_options.position = coordinates[i];
        var point = new google.maps.Marker(marker_options);
        
        google.maps.event.addListener(point, "drag", update_polygon_closure(polygon, i));
		
		google.maps.event.addListener(point, "mouseup", function(event){
			$('#boundayVal').val(polygon.getPath().getArray());
		});
    }
    
    function update_polygon_closure(polygon, i){
		$('#boundayVal').val(polygon.getPath().getArray());
        return function(event){
           polygon.getPath().setAt(i, event.latLng); 
        }
    }
	
};

initialize();

var corners = <?php make_coordinates($locationdetails->row()->location['lat'],$locationdetails->row()->location['lng'],'',$oldcoordinatesArr,$map_radius); ?>;


//var corners = [[13.077639,80.249119],[13.095864,80.24929],[13.108069,80.267143],[13.096199,80.282593],[13.07697,80.282421],[13.06861,80.266972]];
var coordinates = [];

for (var i=0; i<corners.length; i++){
	var position = new google.maps.LatLng(corners[i][0], corners[i][1]);
	coordinates.push(position);
}

create_polygon(coordinates);
</script>
<?php
$coordinatesArr = array();
function make_coordinates($latpoint,$lngpoint,$t='',$oldcoordinatesArr,$map_radius=10){
	for($i=1;$i<=16;$i++){
		$coordinatesArr[] = get_gps_distance($latpoint,$lngpoint,$map_radius,22.5*$i,$t);
	}
	if(!empty($oldcoordinatesArr)){
		$coordinatesArr = $oldcoordinatesArr;
	}
	echo json_encode($coordinatesArr);
}

function get_gps_distance($lat1,$long1,$d,$angle,$type=''){
    # Earth Radious in KM
    $R = 6378.14;

    # Degree to Radian
    $latitude1 = $lat1 * (M_PI/180);
    $longitude1 = $long1 * (M_PI/180);
    $brng = $angle * (M_PI/180);

    $latitude2 = asin(sin($latitude1)*cos($d/$R) + cos($latitude1)*sin($d/$R)*cos($brng));
    $longitude2 = $longitude1 + atan2(sin($brng)*sin($d/$R)*cos($latitude1),cos($d/$R)-sin($latitude1)*sin($latitude2));

    # back to degrees
    $latitude2 = $latitude2 * (180/M_PI);
    $longitude2 = $longitude2 * (180/M_PI);

    # 6 decimal for Leaflet and other system compatibility
   $lat2 = round ($latitude2,6);
   $long2 = round ($longitude2,6);

   // Push in array and get back
   $tab[0] = $lat2;
   $tab[1] = $long2;
   if($type==''){
	return $tab;
   }else{
	return @implode('|',$tab);
   }
}
?>

<?php 
$this->load->view('admin/templates/footer.php');
?>
