<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
	James Rose <james.o.rose@gmail.com>
*/
include "root.php";
require_once "resources/require.php";
require_once "resources/check_auth.php";
if (permission_exists('script_editor_view')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

//add multi-lingual support
	$language = new text;
	$text = $language->get();

//include
	require_once "header.php";

//define function recure_dir
	function recur_dir($dir) {
		clearstatcache();
		$htmldirlist = '';
		$htmlfilelist = '';
		$dirlist = opendir($dir);
		$dir_array = array();
		while (false !== ($file = readdir($dirlist))) {
			if ($file != "." AND $file != ".."){
				$newpath = $dir.'/'.$file;
				$level = explode('/',$newpath);
				if (substr($newpath, -4) == ".svn" ||
					substr($newpath, -4) == ".git") {
					//ignore .svn and .git dir and subdir
				}
				elseif (substr($newpath, -3) == ".db") {
					//ignore .db files
				}
				else {
					$dir_array[] = $newpath;
				}
				if ($x > 1000) { break; };
				$x++;
			}
		}

		asort($dir_array);
		foreach ($dir_array as $newpath){
			$level = explode('/',$newpath);

			if (is_dir($newpath)) {
				$dirname = end($level);
				$newpath = str_replace ('//', '/', $newpath);
				$htmldirlist .= "
					<table border=0 cellpadding='0' cellspacing='0'>
						<tr>
							<td nowrap style='padding-left: 16px;'>
								<a onclick=\"Toggle(this, '".$newpath."');\" style='cursor: pointer;'><img src='resources/images/icon_folder.png' border='0' align='absmiddle' style='margin: 1px 2px 3px 0px;'>".$dirname."</a><div style='display:none'>".recur_dir($newpath)."</div>
							</td>
						</tr>
					</table>\n";
			}
			else {
				$filename = end($level);
				$filesize = round(filesize($newpath)/1024, 2);
				$newpath = str_replace ('//', '/', $newpath);
				$newpath = str_replace ("\\", "/", $newpath);
				$newpath = str_replace ($filename, '', $newpath);
				$htmlfilelist .= "
					<table border=0 cellpadding='0' cellspacing='0'>
						<tr>
							<td nowrap align='bottom' style='padding-left: 16px;'>
								<a href='javascript:void(0);' onclick=\"parent.document.getElementById('filename').value='".$filename."'; parent.document.getElementById('folder').value='".$newpath."';\" title='".$newpath." &#10; ".$filesize." KB'><img src='resources/images/icon_file.png' border='0' align='absmiddle' style='margin: 1px 2px 3px -1px;'>".$filename."</a>
							</td>
						</tr>
					</table>\n";
			}
		}

		closedir($dirlist);
		return $htmldirlist ."\n". $htmlfilelist;
	}

echo "<script type=\"text/javascript\" language=\"javascript\">\n";
echo "    function makeRequest(url, strpost) {\n";
echo "        var http_request = false;\n";
echo "\n";
echo "        if (window.XMLHttpRequest) { // Mozilla, Safari, ...\n";
echo "            http_request = new XMLHttpRequest();\n";
echo "            if (http_request.overrideMimeType) {\n";
echo "                http_request.overrideMimeType('text/xml');\n";
echo "                // See note below about this line\n";
echo "            }\n";
echo "        } else if (window.ActiveXObject) { // IE\n";
echo "            try {\n";
echo "                http_request = new ActiveXObject(\"Msxml2.XMLHTTP\");\n";
echo "            } catch (e) {\n";
echo "                try {\n";
echo "                    http_request = new ActiveXObject(\"Microsoft.XMLHTTP\");\n";
echo "                } catch (e) {}\n";
echo "            }\n";
echo "        }\n";
echo "\n";
echo "        if (!http_request) {\n";
echo "            alert('".$text['message-give-up']."');\n";
echo "            return false;\n";
echo "        }\n";
echo "        http_request.onreadystatechange = function() { returnContent(http_request); };\n";
echo "        http_request.overrideMimeType('text/html');\n";
echo "        http_request.open('POST', url, true);\n";
echo "\n";
echo "\n";
echo "        if (strpost.length == 0) {\n";
echo "            //http_request.send(null);\n";
echo "            http_request.send('name=value&foo=bar');\n";
echo "        }\n";
echo "        else {\n";
echo "            http_request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');\n";
echo "            http_request.send(strpost);\n";
echo "        }\n";
echo "\n";
echo "    }\n";
echo "\n";
echo "    function returnContent(http_request) {\n";
echo "\n";
echo "        if (http_request.readyState == 4) {\n";
echo "            if (http_request.status == 200) {\n";

echo "                  parent.editAreaLoader.setValue('edit1', http_request.responseText); \n";
echo "\n";
echo "            }\n";
echo "            else {\n";
echo "                alert('".$text['message-problem']."');\n";
echo "            }\n";
echo "        }\n";
echo "\n";
echo "    }\n";
echo "</script>\n";

echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";
//echo "// ---------------------------------------------\n";
//echo "// --- http://www.codeproject.com/jscript/dhtml_treeview.asp\n";
//echo "// --- Name:    Easy DHTML Treeview           --\n";
//echo "// --- Author:  D.D. de Kerf                  --\n";
//echo "// --- Version: 0.2          Date: 13-6-2001  --\n";
//echo "// ---------------------------------------------\n";
echo "function Toggle(node, path) {\n";
echo "	parent.document.getElementById('folder').value=path; \n";
echo "	parent.document.getElementById('filename').value='';\n";
echo "	parent.document.getElementById('folder').focus();\n";
echo "	// Unfold the branch if it isn't visible\n";
echo "	if (node.nextSibling.style.display == 'none') {\n";
echo "  	node.nextSibling.style.display = 'block';\n";
echo "	}\n";
echo "	// Collapse the branch if it IS visible\n";
echo "	else {\n";
echo "  	node.nextSibling.style.display = 'none';\n";
echo "	}\n";
echo "\n";
echo "}\n";
echo "</SCRIPT>\n";

echo "<table  width='100%' height='100%' border='0' cellpadding='0' cellspacing='2'>\n";
echo "	<tr>\n";
echo "		<td align=\"left\" valign='top' nowrap>\n";
echo "      	<table border=0 cellpadding='0' cellspacing='0'><tr><td style='cursor: default;'><img src='resources/images/icon_folder.png' border='0' align='absmiddle' style='margin: 0px 2px 4px 0px;'>".$text['label-files']."<div>\n";

ini_set("session.cookie_httponly", True);
session_start();
if ($_SESSION["app"]["edit"]["dir"] == "scripts") {
	echo recur_dir($_SESSION['switch']['scripts']['dir']);
}
if ($_SESSION["app"]["edit"]["dir"] == "php") {
	echo recur_dir($_SERVER["DOCUMENT_ROOT"].'/'.PROJECT_PATH);
}
if ($_SESSION["app"]["edit"]["dir"] == "grammar") {
	echo recur_dir($_SESSION['switch']['grammar']['dir']);
}
if ($_SESSION["app"]["edit"]["dir"] == "provision") {
	echo recur_dir($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH."/resources/templates/provision/");
}
if ($_SESSION["app"]["edit"]["dir"] == "xml") {
	echo recur_dir($_SESSION['switch']['conf']['dir']);
}

echo "			</div></td></tr></table>\n";

echo "		</td>\n";
echo "	</tr>\n";
echo "</table>\n";

require_once "footer.php";

unset ($result_count);
unset ($result);
unset ($key);
unset ($val);
unset ($c);
?>