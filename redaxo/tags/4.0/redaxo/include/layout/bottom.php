<?php

/**
 * Layout Fu� des Backends
 * @package redaxo4
 * @version $Id: bottom.php,v 1.12 2007/10/13 13:52:01 kills Exp $
 */

?>

	</div>
<!-- *** OUTPUT OF CONTENT - END *** -->

</div><!-- END #rex-wrapper -->

	<div id="rex-ftr">
		<ul>
			<li><a href="http://www.yakamara.de" target="_blank" class="black"<?php echo rex_tabindex() ?>>yakamara.de</a> | </li>
      <li><a href="http://www.redaxo.de" target="_blank" class="black"<?php echo rex_tabindex() ?>>redaxo.de</a> | </li>
			<?php if(isset($REX_USER)) echo '<li><a href="index.php?page=credits" class="black">'.$I18N->msg('credits').'</a> | </li>'; ?>
      <li><a href="http://forum.redaxo.de"<?php echo rex_tabindex() ?>>?</a></li>
		</ul>
		<p><?php echo showScripttime() ?> sec | <?php echo rex_formatter :: format(time(), 'strftime', 'date'); ?></p>
	</div>
   </body>
</html>