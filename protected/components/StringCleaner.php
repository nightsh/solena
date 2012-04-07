<?php
# This script removes accents from a string.
# Copyright 2008 Omat Holding B.V. <info@omat.nl>
# Some functions are copied from php.net website.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License as
# published by the Free Software Foundation; either version 2 of
# the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

function ordUTF8($c, $index = 0, &$bytes = null)
 {
   $len = strlen($c);
   $bytes = 0;
 
   if ($index >= $len)
     return false;
 
   $h = ord($c{$index});
 
   if ($h <= 0x7F) {
     $bytes = 1;
     return $h;
   }
   else if ($h < 0xC2)
     return false;
   else if ($h <= 0xDF && $index < $len - 1) {
     $bytes = 2;
     return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
   }
   else if ($h <= 0xEF && $index < $len - 2) {
     $bytes = 3;
     return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
                              | (ord($c{$index + 2}) & 0x3F);
   }           
   else if ($h <= 0xF4 && $index < $len - 3) {
     $bytes = 4;
     return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
                              | (ord($c{$index + 2}) & 0x3F) << 6
                              | (ord($c{$index + 3}) & 0x3F);
   }
   else
     return false;
 }

function convToUtf8($str) 
{ 
    setlocale(LC_ALL, 'en_US.utf8');
    if( mb_detect_encoding($str."a" /*php bug*/,"UTF-8, ISO-8859-1")!="UTF-8" ) 
    {
        return  iconv(mb_detect_encoding($str."a" /*php bug*/,"UTF-8, ISO-8859-1") ,"utf-8", $str); 
    } else {
        return $str; 
    }
}

 function clearUTF($s)
 {
     $r = '';
     $s1 = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
     for ($i = 0; $i < strlen($s1); $i++)
     {
         $ch1 = $s1[$i];
         $ch2 = mb_substr($s, $i, 1);
 
         $r .= $ch1=='?'?$ch2:$ch1;
     }
     return $r;
 }

function ords_to_unistr($ord, $encoding = 'UTF-8'){
     // Turns an array of ordinal values into a string of unicode characters
     $str = '';
     // Pack this number into a 4-byte string
     // (Or multiple one-byte strings, depending on context.)                
     $v = $ord;
     $str = pack("N",$v);
     $str = mb_convert_encoding($str,$encoding,"UCS-4BE");
     return($str);            
 }

function replace_accents($string) 
{
    #$string = "Côte d'Ivoir";

    // iconv needs that.
    setlocale(LC_ALL, 'en_US.utf8');

    // make it utf8 as good as we can.
    $string = convToUtf8($string);

    // debugging in case something is not working.
    #echo "First char is \x".ordUTF8( $string, 0 ). " - ";

    // exceptions there will always be...
    $string = str_replace( ords_to_unistr( "216" ), "O", $string);
    $string = str_replace( ords_to_unistr( "248" ), "o", $string);
    $string = str_replace( ords_to_unistr( "272" ), "d", $string);
    $string = str_replace( ords_to_unistr( "322" ), "l", $string);

    // delete anything not utf8
    $string = iconv("utf-8", "utf-8//IGNORE", $string);

    // convert it.
    $string = clearUTF($string);
    return( $string);
}

function cleanString( $string )
{
	$string = replace_accents( trim($string) );
	$string = stripslashes($string);
	$string = preg_replace("/[^a-zA-Z]/", "", $string);
	return strtolower($string);
}

?>
