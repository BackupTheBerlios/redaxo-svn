<?php

/**
 * Layout Fu� des Backends
 * @package redaxo4
 * @version $Id: bottom.php,v 1.5 2008/03/19 13:03:13 kills Exp $
 */

?>

	</div>
<!-- *** OUTPUT OF CONTENT - END *** -->

</div>
</div><!-- END #rex-wrapper -->

	<div id="rex-footer">
		<div id="rex-navi-footer">
		<ul>
			<li><a href="#rex-header"<?php echo rex_tabindex() ?>>&#94;</a> | </li>
			<li><a href="http://www.yakamara.de" onclick="window.open(this.href); return false;"<?php echo rex_tabindex() ?>>yakamara.de</a> | </li>
      		<li><a href="http://www.redaxo.de" onclick="window.open(this.href); return false;"<?php echo rex_tabindex() ?>>redaxo.de</a> | </li>
			<?php if(isset($REX['USER'])) echo '<li><a href="index.php?page=credits">'.$I18N->msg('credits').'</a> | </li>'; ?>
      		<li><a href="http://forum.redaxo.de" onclick="window.open(this.href); return false;"<?php echo rex_tabindex() ?>>?</a></li>
		</ul>
		<p id="rex-scripttime"><!--DYN--><?php echo rex_showScriptTime() ?><!--/DYN--> sec | <?php echo rex_formatter :: format(time(), 'strftime', 'date'); ?></p>
		</div>
	</div>
	
	<div id="rex-extra"></div>
	
	</div><!-- END #rex-website -->
   </body>
</html>