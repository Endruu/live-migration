<h2><?php echo $title; ?></h2>

<div id="livemigration-summary">

	<?php echo $this->renderPartial("_summary", array('mlist' => $mlist, 'latest' => $latest), true); ?>

</div>

<br /><hr />

<h3>
	<?php
		if( $error ) {
			echo "<span style='color: red;'>Error during migration!</span>";
		} else {
			echo "<span style='color: green;'>Migration done!</span>";
		}
	?>
</h3>

<pre id="livemigration-result">
	<?php echo $response; ?>
</pre>
