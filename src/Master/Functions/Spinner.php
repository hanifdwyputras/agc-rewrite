<?php

class Spinner {
    public function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }

    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}

if( !function_exists('spin') ) {
  function spin($string){
      $spintax = new Spinner();
      return $spintax->process($string);
  }
}
