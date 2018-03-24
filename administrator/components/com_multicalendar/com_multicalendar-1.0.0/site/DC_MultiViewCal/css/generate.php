<?php 
$arr = array("ui-lightness",
"ui-darkness",
"smoothness",
"start",
"redmond",
"sunny",
"overcast",
"le-frog",
"flick",
"pepper-grinder",
"eggplant",
"dark-hive",
"cupertino",
"south-street",
"blitzer",
"humanity",
"hot-sneaks",
"excite-bike",
"vader",
"mint-choc",
"black-tie",
"trontastic",
"swanky-purse");

$path = "./";
for ($i=0;$i<count($arr);$i++)
{
    @mkdir($path .$arr[$i]);
}
$filename = "calendarMain.css";
$fd = fopen ($filename, "r");
$contents = fread ($fd, filesize ($filename));
fclose ($fd);
	 			//header_months and color //buttons and color 			//activebutton and color  //active and color 				//border         //icons buttons, months
$c = array (
array(	"F7B64A","FFFFFF",				"F6F6F6","1C94C4",				"E78F08","ffffff",				"E78F08","ffffff",				"EC8E0C",    "icons_blue.png" , "icons_white.png"	), // 1
array(	"333333","FFFFFF",				"CCCECC","000000",				"3E3E3E","FFFFFF",				"3E3E3E","FFFFFF",				"999999",    "icons_black.png" , "icons_white.png"	), // 2
array(	"DEDEDE","000000",				"CCCECC","000000",				"F5F5F5","000000",			  "CCCECC","FFFFFF",				"F4F4F4",    "icons_black.png" , "icons_black.png"	), // 3
array(	"9DCEE3","000000",				"46A5D2","000000",				"0088CC","FFFFFF",				"0088CC","FFFFFF",				"0088CC",    "icons_black.png" , "icons_black.png"	), // 4
array(	"70A8D2","000000",				"DDECF7","000000",				"428CC4","FFFFFF",				"DDECF7","000000",				"9DC7E6",    "icons_black.png" , "icons_black.png"	), // 5
array(	"8D8574","000000",				"FEDC6B","000000",				"FEEEBD","000000",				"ffffBD","ff0000",				"8D8574",    "icons_black.png" , "icons_black.png"	), // 6
array(	"DDDDDD","000000",				"F2F2F2","000000",				"B7B7B7","FFFFFF",				"B7B7B7","FFFFFF",				"D4D4D4",    "icons_black.png" , "icons_black.png"	), // 7
array(	"5A952C","FFFFFF",				"7CBB4D","000000",				"3E6C1A","FFFFFF",				"D6E9C7","000000",				"497D1E",    "icons_black.png" , "icons_white.png"	), // 8
array(	"E4E4E4","000000",				"FAFAFA","000000",				"FEFEFE","FF0084",				"FEFEFE","FF0084",				"D8D8D8",    "icons_black.png" , "icons_black.png"	), // 9
array(	"E7E5DA","4F3A1C",				"F7F6F5","000000",				"C2BDA9","FFFFFF",				"F7F6F5","A32F03",				"C2BDA9",    "icons_black.png" , "icons_black.png"	), // 10
array(	"463E4F","FFFFFF",				"DDDADF","000000",				"645E69","FFFFFF",				"645E69","FFFFFF",				"31283B",    "icons_black.png" , "icons_white.png"	), // 11
array(	"323232","FFFFFF",				"737373","000000",				"0972A5","FFFFFF",				"EEEEEE","0972A5",				"333333",    "icons_black.png" , "icons_white.png"	), // 12
array(	"E2EFF8","000000",				"E2EFF8","000000",				"62BBE8","FFFFFF",				"DEEDF7","000000",				"AED0EA",    "icons_black.png" , "icons_black.png"	), // 13
array(	"EDEADC","000000",				"BAD698","000000",				"55A616","FFFFFF",				"DBE9C9","3C8A09",				"CDC4A3",    "icons_black.png" , "icons_black.png"	), // 14
array(	"CC0202","FFFFFF",				"dddddd","000000",				"eeeeee","920A0A",				"FFF4F4","920A0A",				"CC0202",    "icons_black.png" , "icons_white.png"	), // 15
array(	"D09042","FFFFFF",				"EDE4D5","000000",				"F4F0EC","B85736",				"F4F0EC","B85736",				"D09042",    "icons_black.png" , "icons_white.png"	), // 16
array(	"2C4359","E4E664",				"93C3CD","000000",				"DB4865","FFFFFF",				"93C3CD","FFFFFF",				"2C4359",    "icons_black.png" , "icons_yellow.png"	), // 17
array(	"FBFBFB","E69700",				"A2C9EB","000000",				"E69700","FFFFFF",				"DEEDF7","000000",				"DDDDDD",    "icons_black.png" , "icons_orange.png"	), // 18
array(	"4D4D4D","FFFFFF",				"CCCECC","000000",				"121212","FFFFFF",				"929292","FFFFFF",				"4D4D4D",    "icons_black.png" , "icons_white.png"	), // 19
array(	"44352B","9BCC60",				"695649","9BCC60",				"262019","9BCC60",				"EDE9DC","6A9931",				"3E3128",    "icons_green.png" , "icons_green.png"	), // 20
array(	"333333","FFFFFF",				"606060","FFFFFF",				"EFEFEF","000000",				"707070","FFFFFF",				"2C2C2C",    "icons_white.png" , "icons_white.png"	), // 21
array(	"C5E89B","000000",				"414141","B8EC79",				"303030","FFFFFF",				"EFEFEF","6BA525",				"A9DD68",    "icons_green.png" , "icons_black.png"	), // 22
array(	"2F220E","EACD86",				"584C2D","FFFFFF",				"443113","F9F2BD",				"ECE2B4","000000",				"756931",    "icons_white.png" , "icons_beige.png"	)  // 23
);
for ($i=0;$i<count($c);$i++)
{     
    $newcontent = $contents;
    $newcontent = str_replace("nnnnnn4", $c[$i][4], $newcontent);
    $newcontent = str_replace("nnnnnn2", $c[$i][1], $newcontent);
    $newcontent = str_replace("nnnnnn6", $c[$i][2], $newcontent);
    $newcontent = str_replace("nnnnnn7", $c[$i][3], $newcontent);
    $newcontent = str_replace("nnnnnn3", $c[$i][0], $newcontent);
    $newcontent = str_replace("nnnnnn1", $c[$i][8], $newcontent); 
    $newcontent = str_replace("nnnnnn5", $c[$i][5], $newcontent);
    $newcontent = str_replace("nnnnnn8", $c[$i][6], $newcontent);
    $newcontent = str_replace("nnnnnn9", $c[$i][7], $newcontent);
    $newcontent = str_replace("nnnnnnimg10", $c[$i][9], $newcontent);
    $newcontent = str_replace("nnnnnnimg11", $c[$i][10], $newcontent);
     
    $filename = $path .$arr[$i]."/calendar.css";
    $fd = fopen ($filename, "w");
    fwrite ($fd, $newcontent);
    fclose ($fd);    
}
?>