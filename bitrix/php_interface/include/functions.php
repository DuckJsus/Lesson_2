<?
define("IBLOCK_CAT_ID", 1);
?>

<?
function dump($var, $die = false, $all = false)
{
	global $USER;
	if( ($USER->isAdmin()) || ($all == true))
	{
		?>
		<font style="text-align: left; font-size: 10px"><pre><?var_dump($var)?></pre></font><br>
		<?
	}
	if($die)
	{
		die;
	}
}
?>