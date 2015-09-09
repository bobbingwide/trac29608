<?php // (C) Copyright Bobbing Wide 2013-2015
/**
 * Return a hexadecimal dump of the given string
 *
 * @param string $string
 * @return string the hexadecimally formatted output
 */
function hexdump( $string ) {
  $count = strlen( $string );
  $dumpo = null;
  $dumpo = $count;
  $dumpo .= "<br />";
  $lineo = "";
  $hexo = "";
  for ( $i = 1; $i <= $count; $i++ ) {
    $ch = $string[$i-1];
    if ( ctype_cntrl ( $ch ) ) {
      $lineo .= ".";
    } else { 
      $lineo .= $ch; 
    }
    $hexo .= bin2hex( $ch );
    $hexo .=  " ";
    if (  0 == $i % 20 ) {
      $lineo = htmlentities( $lineo );
      $dumpo .= $lineo . " " . $hexo . "<br />";  
      $lineo = "";
      $hexo = "";
    }
  }
  
  $dumpo .= htmlentities( substr( $lineo. str_repeat(".", 20 ), 0, 20 ) );
  $dumpo .= " "; 
  $dumpo .= $hexo;
  $dumpo .= "<br />";
  return( $dumpo );
}


