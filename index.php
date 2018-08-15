<?php
include( 'hooks.php' );
$hooks = new Hooks;
$actions = $hooks->get_actions();
$filters = $hooks->get_filters();

if ( isset( $_GET['json'] ) ) {
	header( 'Content-Type: application/json' );
	$data = array(
	'actions' => $hooks->actions,
	'filters' => $hooks->filters,
	);
	echo json_encode( $data );
	die();
}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Beaver Hooks</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js" type="text/javascript"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <div class="menu">
	<div class="left">
	  <ul>
		<?php
		foreach ( $hooks->nav as $k => $nav_data ) {
			printf( '<li><a href="%s">%s</a></li>', $nav_data['link'], $nav_data['name'] );
		}
			?>
	  </ul>
		  </div>
	  </div>
  <div id="demo">
  <h1><?php echo $hooks->package['name']; ?> Hooks & Filters</h1>
  <div class="table-responsive-vertical shadow-z-1">
  <table id="table" class="table table-hover table-mc">
	  <thead>
		<tr>
		  <th>Name</th>
		  <th>Location</th>
					<th>Context</th>
		</tr>
	  </thead>
	  <tbody>
				<?php
				echo $actions;
				echo $filters;
				?>
	  </tbody>
	</table>
  </div>
</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="/js/index.js" type="text/javascript"></script>
</html>
