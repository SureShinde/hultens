<?php

namespace Proclient\PyramidAPI\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Validation extends AbstractHelper {

	function is_date($string) {
    return preg_match('/(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])/', $string);
  }

  function timestamp_to_pyrdate($string) {
    $date = explode(' ', $string)[0];
    return date('yWN', strtotime($date));
  }

  function phone_to_cust_no($string) {
    $string = preg_replace('/[^0-9]/', '', $string);
    if (substr($string, 0, 2) == '45' or substr($string, 0, 2) == '46') {
      $string = substr($string, 1);
      $string[0] = 0;
    }
    return $string;
  }
}
