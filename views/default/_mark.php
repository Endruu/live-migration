<h2>Marking as latest: <?php echo $latest; ?></h2>

<div id="livemigration-summary">

	<?php echo $this->renderPartial("_summary", array('mlist' => $mlist, 'latest' => $latest), true); ?>

</div>

<br /><hr />

<pre id="livemigration-result">
	<?php echo $response; ?>
</pre>