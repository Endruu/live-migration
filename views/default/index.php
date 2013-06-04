<h1>LiveMigration</h1>
<h2>Actual: <?php ?>></h2>

<div id="livemigration-container">
	
	<div id="livemigration-summary">
	
		<?php echo $this->renderPartial("_summary", array('mlist' => $mlist, 'latest' => $latest), true); ?>
	
	</div>

</div>


<pre id="livemigration-result">
	<!-- outpu of migrate and mark commands -->
</pre>