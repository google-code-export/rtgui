<?php
//
// rtGui - Copyright Simon Hall 20007
//
// http://rtgui.googlecode.com/
//
$execstart=$start=microtime(true);
include "functions.php";
include "config.php";
import_request_variables("gp","r_");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="favicon.ico" />
<title>rtGui</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php


echo "<table width=100% border=0 cellpadding=5 cellspacing=0>";
echo "<tr><td><a href='index.php'><h1>rtGui</h1></a>";
echo "<i>The rTorrent Graphical User Interface</i></td>";
echo "<td>&nbsp;</td>";      
echo "</td></table>\n<br>\n";

// Torrent info...  (get all downloads, then filter out just this one by the hash)
$alltorrents=get_full_list("main");
$thistorrent=array();
foreach($alltorrents as $torrent) {
   if ($r_hash==$torrent['hash']) $thistorrent=$torrent;
}
echo "<br>&laquo; <a href='index.php'>Back</a> | ";
echo "<a href='control.php?hash=".$thistorrent['hash']."&cmd=".($thistorrent['is_active']==1 ? "stop" : "start")."'>".($thistorrent['is_active']==1 ? "Stop" : "Start")."</a>";
echo " | <a href='control.php?hash=".$thistorrent['hash']."&cmd=delete' onClick='return confirm(\"Delete torrent - are you sure? (This will not delete data from disk)\");'>Delete</a>";
echo " | <a href='control.php?hash=".$thistorrent['hash']."&cmd=hashcheck'>Check</a><br><br>\n";

if ($thistorrent['complete']) { $statusflags="Complete "; } else { $statusflags="Incomplete ";}
if ($thistorrent['is_hash_checked']) $statusflags.="&middot; Hash Checked ";
if ($thistorrent['is_hash_checking']) $statusflags.="&middot; Hash Checking ";
if ($thistorrent['is_multi_file']) $statusflags.="&middot; Multi-file ";
if ($thistorrent['is_open']) $statusflags.="&middot; Open ";
if ($thistorrent['is_private']) $statusflags.="&middot; Private ";
echo "<h2>Torrent:</h2>";
echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable'>";
echo "<tr class='row1'><td align=right><b>Name:</b></td><td><span class='torrenttitle ".($thistorrent['is_active']==1 ? "active" : "inactive")."'>".$thistorrent['name']."</span></td></tr>\n";
echo "<tr class='row2'><td align=right><b>Status Flags:</b></td><td>".$statusflags."</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Message:</b></td><td>".$thistorrent['message']."</td>";
echo "<tr class='row2'><td align=right><b>Completed Bytes:</td><td>".format_bytes($thistorrent['completed_bytes'])."</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Size:</b></td><td>".format_bytes($thistorrent['size_bytes'])."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Complete:</b></td><td>".$thistorrent['percent_complete']." %&nbsp;&nbsp;";
echo "<table align=left border=0 cellspacing=0 cellpadding=1 bgcolor=#666666 width=50px><tr><td align=left>";
echo "<img src='percentbar.gif' height=4px width=".@round(($thistorrent['percent_complete']/2))."px>";
echo "</td></tr>\n</table>\n";
echo "</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Down Rate:</b></td><td>".format_bytes($thistorrent['down_rate'])."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Down Total:</b></td><td>".format_bytes($thistorrent['down_total'])."</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Up Rate:</b></td><td>".format_bytes($thistorrent['up_rate'])."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Up Total:</b></td><td>".format_bytes($thistorrent['up_total'])."</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Peers connected:</b></td><td>".$thistorrent['peers_connected']."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Peers not connected:</b></td><td>".$thistorrent['peers_not_connected']."</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Peers complete:</b></td><td>".$thistorrent['peers_complete']."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Ratio:</b></td><td>".@round(($thistorrent['ratio']/1000),2)." %</td></tr>\n";
echo "<tr class='row1'><td align=right><b>Status:</b></td><td>".$thistorrent['status_string']."</td></tr>\n";
echo "<tr class='row2'><td align=right><b>Priority:</b></td><td>".$thistorrent['priority_str']."</td></tr>\n";

echo "</table>\n<br>\n";

// tracker info...
echo "<h2>Trackers:</h2>\n";
$data=get_tracker_list($r_hash);
echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable'>";
echo "<tr class='tablehead'>";
echo "<td>URL</td>";
echo "<td align=center>Last</td>";
echo "<td align=center>Interval</td>";
echo "<td align=center>Scrapes</td>";
echo "<td align=center>Enabled</td>";
echo "</tr>\n";
$thisrow="row1";
foreach($data AS $item) {
   echo "<tr class='$thisrow'>";
   echo "<td>".$item['get_url']."</td>";
   echo "<td align=center>".($item['get_scrape_time_last']>0 ? date("Y-m-d H:i",@round($item['get_scrape_time_last']/1000000)) : "never")."</td>";
   echo "<td align=center>".@round($item['get_normal_interval']/60)." mins</td>";
   echo "<td align=center>".$item['get_scrape_complete']."</td>";
   echo "<td align=center>".($item['is_enabled']==1 ? "Yes" : "No")."</td>";
   if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
}
echo "</table>\n<br>\n";

// file info...
echo "<h2>File List:</h2>\n";
$data=get_file_list($r_hash);
echo "<form action='control.php' action=post>";
echo "<table border=0 cellspacing=0 cellpadding=5 class='maintable'>";
echo "<tr class='tablehead'>";
echo "<td>Filename</td>";
echo "<td align=center>Size</td>";
echo "<td align=center>Done</td>";
echo "<td align=center>Chunks</td>";
echo "<td align=center>Priority</td>";
echo "</tr>\n";
$thisrow="row1";
$index=0;
foreach($data AS $item) {
   echo "<tr class='$thisrow'>";
   echo "<td>".$item['get_path']."</td>";
   echo "<td align=center>".format_bytes($item['get_size_bytes'])."</td>";
   echo "<td align=center>".@round(($item['get_completed_chunks']/$item['get_size_chunks'])*100)." %<br>\n";
   echo "<table border=0 cellspacing=0 cellpadding=1 bgcolor=#666666 width=50px><tr><td align=left>\n";
   echo "<img src='percentbar.gif' height=4px width=".@round((($item['get_completed_chunks']/$item['get_size_chunks'])*100)/2)."px>";
   echo "</td></tr>\n</table>\n";
   echo "</td>\n";
   echo "<td align=center>".$item['get_completed_chunks']." / ".$item['get_size_chunks']."</td>\n";
   echo "<td>\n";
   echo "<select name='set_fpriority[$index]'>\n";
   echo "<option value='0' ".($item['get_priority']==0 ? "selected" : "").">Off</option>\n";
   echo "<option value='1' ".($item['get_priority']==1 ? "selected" : "").">Normal</option>\n";
   echo "<option value='2' ".($item['get_priority']==2 ? "selected" : "").">High</option>\n";
   echo "</select>\n";
   echo "<input type='hidden' name='hash' value='$r_hash'>\n";
   echo "</td>\n";
   echo "</tr>\n";
   if ($thisrow=="row1") {$thisrow="row2";} else {$thisrow="row1";}
   $index++;
}
echo "<tr><td>&nbsp</td><td>&nbsp;</td><td>&nbsp</td><td>&nbsp</td><td><input type='submit' value='Save'>";
echo "</table>\n";
echo "</form>";
?>
</body>
</html>