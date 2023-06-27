<?php

/**
 *  split array into "n" group
 */

function get_alphabet( $term, $abjad = "a" )
{
  sort($term);
  $alphabetic = array();

  foreach($term as $value)
  {
      $firstLetter = substr($value, 0, 1);
      if($abjad === $firstLetter)
      {
          $alphabetic[] = $value;
      }
  }

  return $alphabetic;

}
/**
 *  split array into "n" group
 */
function array_group( $array, $pergroup = 4 )
{
    if( !isset($array) || empty($array) ) return;

    $redfunc = function ($partial, $elem) use ($pergroup) {
        $groupCount = count($partial);
        if ($groupCount == 0 || count(end($partial)) == $pergroup)
            $partial[] = array($elem);
        else
            $partial[$groupCount-1][] = $elem;

        return $partial;
    };
    return array_reduce( $array, $redfunc, array() );
}

/**
 *  check key exist in multidimensional array
 */
function exist_key_multi( $key, Array $array ) {
    if (array_key_exists($key, $array)) {
        return true;
    }
    foreach ($array as $k=>$v) {
        if (!is_array($v)) {
            continue;
        }
        if (array_key_exists($key, $v)) {
            return true;
        }
    }
    return false;
}

/**
 *  get one (array or string)
 */
function get_one( $string )
{
	if( is_array($string) )
	{
		shuffle($string);
		return $string[0];
	}
	return $string;
}

/**
 *  Generate array random number $form - $to, without duplicate
 */
function rand_numb($from = 0, $to = 1000, $count = 10 )
{
	$numbers = range( $from, $to );
	if( $count > $to )
	{
		$count = $to;
		$numbers = range( $from, $count);
	}
	shuffle($numbers);
	for($i=0; $i<$count;$i++)
	   $digits[] = $numbers[$i];
	return $digits;
}
