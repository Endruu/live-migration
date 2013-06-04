<?php echo CHtml::beginForm(); ?>

<table>
	<thead>
		<tr>
			<th>Select</th>
			<th>Migration</th>
			<th>Created</th>
			<th>Applied</th>
			<th>Status</th>
		</tr>
	</thead>
	
	<tbody id="livemigration-summary">

<?php

foreach( $mlist as $migration => $m ) {
	$checked = $migration == $latest ? true : false;
	$tr = "<tr>";
	
	$tr .= "<td>" . CHtml::radioButton( 'selected', $checked, $htmlOptions = array ( 'value' => $migration ) ) . "</td>";
	if( $checked ) {
		$tr .= "<td><b>" . $m["name"] . "</b></td>";
	} else {
		$tr .= "<td>" . $m["name"] . "</td>";
	}
	$tr .= "<td>" . $m["created"] . "</td>";
	$tr .= "<td>" . $m["applied"] . "</td>";
	$tr .= "<td>" . $m["status"] . "</td>";
	
	$tr .= "</tr>\n";
	echo $tr;
}

?>

	</tbody>
</table>

<?php

echo $this->renderPartial("_buttons", null, true);
echo CHtml::endForm();

?>