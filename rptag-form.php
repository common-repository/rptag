<p><label for="rpcat-title">Title:</label>
<input class="rpcat_css" id="widget_rptag-<?php echo $number; ?>-title" name="widget_rptag[<?php echo $number; ?>][title]" type="text" value="<?php echo htmlspecialchars($values['title'], ENT_QUOTES); ?>" /></p>

<p><label for="rpcat-count">Display:</label>
<input class="rpcat_css" id="widget_rptag-<?php echo $number; ?>-count" size="5" name="widget_rptag[<?php echo $number; ?>][count]" type="text" value="<?php echo htmlspecialchars($values['count'], ENT_QUOTES); ?>" />entries<br/><small>Maximun number allowed 15</small></p>

<p><label for="rpcat-tag">Select Tag:</label>
<select class="rpcat_css" id="widget_rptag-<?php echo $number; ?>-tag" name="widget_rptag[<?php echo $number; ?>][tag]">
	<?php 
		$tags =  get_tags();
		print_r($tags);
		foreach ($tags as $tag) {
			$option = '<option value="'.$tag->slug.'"';
			if($values['tag'] == $tag->slug) 
				$option .= ' selected="selected"';
			$option .= '>';
			$option .= $tag->name;
			$option .= '('.$tag->count.')';
			$option .= '</option>';
			echo $option;
		}
	?>
</select></p>