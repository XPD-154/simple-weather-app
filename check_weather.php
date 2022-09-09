<?php
session_start();

//function to process API call
function api_call($googleApiUrl){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);

	curl_close($ch);
	$data = json_decode($response, true);

	/*
	echo"<pre>";
	print_r($data);
	echo"</pre>";
	*/
	if($data == []){

		$_SESSION['error'] = "invalid location";

	}else{

		if(isset($_SESSION['latitude']) && isset($_SESSION['logitude'])){

			//echo "weather description = ".$data['weather'][0]['description'];
			$_SESSION['weather_des'] = $data['weather'][0]['description'];
			//echo "main temperature = ".$data['main']['temp'];
			$_SESSION['temp'] = $data['main']['temp'];
			//echo "wind speed = ".$data['wind']['speed'];
			$_SESSION['wind_spd'] = $data['wind']['speed'];

		}
		elseif(isset($data[0]['lat']) && isset($data[0]['lat'])){

			//echo "latitude = ".$data[0]['lat'];
			$_SESSION['latitude'] = $data[0]['lat'];
			//echo "logitude = ".$data[0]['lon'];
			$_SESSION['logitude'] = $data[0]['lon'];
		}
	}

}

//API key
$apiKey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$weather = "";

//check if city name has been submitted
if(isset($_GET['city'])){

	$cityId = $_GET['city'];

	//check if lantitude and logitude of city is available
	if(isset($_SESSION['latitude']) && isset($_SESSION['logitude'])){

		$googleApiUrl_2 = "https://api.openweathermap.org/data/2.5/weather?lat=" . $_SESSION['latitude'] . "&lon=" . $_SESSION['logitude'] . "&appid=". $apiKey;

		api_call($googleApiUrl_2);

		unset($_SESSION['latitude']);
		unset($_SESSION['logitude']);

		$weather = "The Weather in ".$_GET['city']." is currently '".$_SESSION['weather_des']."'.";

		$tempInCelcuis = intval($_SESSION['temp'] - 273);
		$weather .= " The Temperature is ".$tempInCelcuis."&deg;C and the Wind Speed is '".$_SESSION['wind_spd']."meter/sec'.";

	}
	//if logitude and latitude is not available get it
	else{

		$googleApiUrl_1 = "http://api.openweathermap.org/geo/1.0/direct?q=" . $cityId . "&limit=5&appid=". $apiKey;

		api_call($googleApiUrl_1);

	}

}

?>
<!DOCTYPE html>
<html>
<head>

	  <link rel="stylesheet" href="bootstrap-4.4.1/dist/css/bootstrap.min.css">

	  <link rel="stylesheet" href="js/jquery-ui/jquery-ui.css">

	  <title>Weather Scrapper</title>

	  <style type="text/css">

	  	html{
	  		background: url(img/739995-2016-06-13.jpg) center;
	  		-webkit-background-size: cover;
	  		-moz-background-size: cover;
	  		-o-background-size:cover;
	  		background-size: cover;

	  	}

	  	body{
	  		background: none;
	  	}

	  	.container{
	  		text-align: center;
	  		margin-top: 90px;
	  		width: 500px
	  	}
		input{
			margin: 20px 0;
		}
		#weather{
			margin-top: 15px;
		}
	  </style>

</head>

<body>

	<div class="container">

		<h1>Whats the weather</h1>
		<form>
			<div class="form-group">
				 <label for="city"><h3 class="text-white bg-dark">Enter the name of a city</h3></label>
				 <input type="text" class="form-control city" id="city" name="city" placeholder="Eg.London" value="<?php if(isset($_GET['city'])){echo $_GET['city'];}?>">
			</div>
				 <button type="submit" class="btn btn-primary submit">Submit</button>
		</form>
		<div id="weather"><?php
			if($weather!=""){
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'.$weather.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

			}elseif(isset($_SESSION['error'])){
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.$_SESSION['error'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

			}
		?></div>

	</div>

    <script src="js/jquery-3.4.1.js"></script>
    <script src="js/jquery-ui/jquery-ui.js"></script>
    <script src="bootstrap-4.4.1/dist/js/bootstrap.min.js"></script>

</body>
</html>

