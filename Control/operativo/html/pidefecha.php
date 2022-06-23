<?php #seccion de inclusión de captura de una fecha
IF(!$YADEFINIDO)
{
$YADEFINIDO=1;
?>
<script type="text/javascript" src="html/jscalendar/calendar.js"></script>
<script type="text/javascript" src="html/jscalendar/lang/calendar-esp.js"></script>
<script type="text/javascript" src="html/jscalendar/calendar-setup.js"></script>
<style type="text/css"> @import url(html/jscalendar/default.css);</style>
<?PHP } ?>
<input type="hidden" name="<?=$CAMPOFECHA?>" id="<?=$CAMPOFECHA?>" value="<?=$DATOFECHA?>" />
<span class="datefield" id="eventdate_span<?=$CAMPOFECHA?>"><?=$DATOFECHA?></span>
<img align="texttop" src="html/jscalendar/img.gif" id="eventdate_trigger<?=$CAMPOFECHA?>" style="cursor: pointer;" title="Calendario" alt="Calendario" onclick="return true;" onmouseover="this.style.background='red';" onmouseout="this.style.background=''" />
<script type="text/javascript">
	Calendar.setup(
	{
		inputField     :    "<?=$CAMPOFECHA?>",
		ifFormat       :    "%Y%m%d",
		displayArea    :    "eventdate_span<?=$CAMPOFECHA?>",
		daFormat       :    "%A, %B %d, %Y",
		singleClick    :    true,
		timeFormat     :    "12",
		button         :    "eventdate_trigger<?=$CAMPOFECHA?>",
		align          :    "Tl",
		date           :    new Date(<?=$DATOFECHA?>).setTime(0000000000000),
		step           :    1
	});
</script>
<?PHP  # FIN DE LA INCLUSION
#ifFormat       :    "%m/%d/%Y %H:%M",
# daFormat       :    "%Y, %B %d, %Y",
?>