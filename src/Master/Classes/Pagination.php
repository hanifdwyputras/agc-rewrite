<?php

namespace Masterzain\Classes;

class Pagination
{

  protected $total_data   = "";
  protected $per_page     = "";
  protected $output       = "";
  protected $max_pages    = "";
  protected $base_style   = "<li><a href='{%link%}?page={%page%}'>{%page%}</a></li>";
  protected $active_style = "<li class='active'><a href='#'>{%page%}</a></li>";
  protected $prev_class   = "prev";
  protected $next_class   = "next";
  protected $display   	  = "5";
  
  public function setLimitPages( $value = "" )
  {
      $this->max_pages = $value;
      return $this;
  }

  public function setPrevClass( $value = "" )
  {
      $this->prev_class = $value;
      return $this;
  }

  public function setNextClass( $value = "" )
  {
      $this->next_class = $value;
      return $this;
  }

  public function setTotal( $value = "" )
  {
      $this->total_data = $value;
      return $this;
  }

  public function setPerPage( $value = "" )
  {
      $this->per_page = $value;
      return $this;
  }

  public function setBaseStyle( $value = "" )
  {
      $this->base_style = $value;
      return $this;
  }

  public function setActiveStyle( $value = "" )
  {
      $this->active_style = $value;
      return $this;
  }

  protected function getTotalPages()
  {
      $this->total_page = round( $this->total_data / $this->per_page );

      if( $this->total_page == 1 ) {
          return;
      }
      if( $this->total_page < $this->getMaxPage() )
            return $this->total_page;

      return $this->getMaxPage();
  }

  protected function getPrevPages()
  {
      return $this->getCurrentPage() - 1;
  }

  protected function getNextPages()
  {
      return $this->getCurrentPage() + 1;
  }

  protected function getDisplay()
  {
      return $this->display;
  }
  
  protected function getBaseUrl()
  {
      return get_permalink();
  }

  protected function getPages( $type, $pages )
  {
      $short_code = [
        '{%link%}'   => $this->getBaseUrl(),
        '{%page%}'   => $pages,
      ];
      return strtr( $type, $short_code );
  }

  protected function getMaxPage()
  {
      return round( 28 / $this->per_page * 179 ) - 1;
  }

  public function getCurrentPage()
  {
      $pages = Input::get('page');
      return ( !empty($pages) ) ? $pages : 1;
  }

  public function render()
  {
      $this->setLimitPages( round( $this->per_page / 28 * 179 ) );

      if( $this->getCurrentPage() > 2) {
          $this->output .= $this->getPages( $this->base_style, 1 );
      }

      if( $this->getCurrentPage() > 1) {
          $this->output .= $this->getPages( $this->base_style, $this->getPrevPages() );
      }

      $this->output .= $this->getPages( $this->active_style, $this->getCurrentPage() );

      if( $this->getCurrentPage() < $this->getTotalPages() ) {
          $this->output .= $this->getPages( $this->base_style, $this->getNextPages() );
      }

      if( $this->max_pages !== null && $this->max_pages < $this->getTotalPages() && $this->getCurrentPage() < $this->max_pages ) {
          $this->output .= $this->getPages( $this->base_style, $this->max_pages );
      }

      if( $this->getCurrentPage() < $this->getTotalPages() - 1) {
          $this->output .= $this->getPages( $this->base_style, $this->getTotalPages() );
      }

      return $this->output;
  }

}
