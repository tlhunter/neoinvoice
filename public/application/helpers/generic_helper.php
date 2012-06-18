<?php
/**
 *
 * @param Time $time
 * @return String
 * @abstract Takes a PHP time integer, and returns the age in human readable time
 */
function time_ago($time) {
  $ago = time() - $time;

  if ($ago < 60) {
    return "$ago seconds";
  } else {
    $ago = round($ago / 60);
    if ($ago < 60) {
      return "$ago minutes";
  } else {
      $ago = round($ago / 60);
      if ($ago < 24) {
        return "$ago hours";
      } else {
        $ago = round($ago / 24);
        if ($ago < 7) {
          return "$ago days";
        } else {
          $ago = round($ago / 7);
          if ($ago < 4) {
            return "$ago weeks";
          } else {
            $ago = round($ago / 4);
            if ($ago < 12) {
              return "$ago months";
            } else {
              $ago = round($ago / 12);
              return "$ago years";
            }
          }
        }
      }
    }
  }
}

/**
 *
 * @param strig $message Logs message to Firebug console if this is localhost
 */
function firelog($message) {
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
		$CI =& get_instance();
		$CI->firephp->log($message);
	}
}

/**
 *
 * @return bool Whether or not this is an AJAX request
 * @abstract Checks the current request header and determins whether or not this is an AJAX request
 */
function xhr_request() {
	if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') !== FALSE) {
		return TRUE;
	}
	return FALSE;
}

/**
 *
 * @return bool Whether or not this is an HTTP request
 * @abstract Checks the current request header and determins whether or not this is an HTML request
 */
function html_request() {
	if (strpos($_SERVER['HTTP_ACCEPT'], 'text/javascript') === FALSE && strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== FALSE) {
		return TRUE;
	}
	return FALSE;
}

/**
 * @param String $item Table name
 * @param Integer $selected ID of selected item
 * @param String $label POST name of item, defaults to $item
 * @param String $owner_column Name of column specifying owner
 * @param Integer $owner_id ID value of owner column
 * @param String $id Name of PK column
 * @param String $name Name of column to be displayed as name
 * @param Array $where Associative array of a where clause (e.g. active => 1)
 * @return String HTML dropdown
 * @abstract Generates dropdown menus from database tables with specified criteria
 */
function dropdown_generic($item, $selected = 1, $label = '', $owner_column = 'company_id', $owner_id = 0, $id = 'id', $name = 'name', $where = array()) {
	$CI =& get_instance();
	if (!$owner_id)
		$owner_id = $CI->session->userdata('company_id');
	if (empty($label))
		$label = $item;
	if (!empty($where)) {
		$where_clause = '';
		foreach($where AS $key => $value) {
			$where_clause .= "$key = " . $CI->db->escape($value) . " AND ";
		}
	} else {
		$where_clause = '';
	}
	$sql = "SELECT $name, $id FROM $item WHERE $where_clause $owner_column = $owner_id ORDER BY $name ASC";
	$query = $CI->db->query($sql);
	$result = $query->result_array();
	$str = "<select name=\"$label\" id=\"dropdown_$label\" class=\"dropdown textual\">\n";
	$str .= "<option></option>";
	foreach($query->result_array() AS $row) {
		$str .= "<option ";
		if ($selected == $row[$id])
			$str .= "selected ";
		$str .= "value=\"" . $row[$id] . "\">" . ucfirst($row[$name]) . "</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

/**
 *
 * @param String $name_hour POST name of hour dropdown
 * @param String $name_minute POST name of minute dropdown
 * @param Integer $selected_hour Selected hour, 0 - 24
 * @param Integer $selected_minute Selected minute, 0 - 60 in 15 minute increments
 * @return String HTML dropdown code
 * @abstract Creates time dropdown menus
 */
function dropdown_time($name_hour, $name_minute, $selected_hour = 100, $selected_minute = 0) {
	$str = "<select name=\"$name_hour\" class=\"dropdown textual\">\n<option></option>\n";
	for ($i = 0; $i < 12; $i++) {
		$label = $i ? "$i AM" : "Midnight";
		$sel = $i == $selected_hour ? ' selected' : '';
		$str .= "\t<option value=\"$i\"$sel>$label</option>\n";
	}
	for ($i = 0; $i < 12; $i++) {
		$label = $i ? "$i PM" : "Noon";
		$sel = ($i+12) == $selected_hour ? ' selected' : '';
		$str .= "\t<option value=\"" . ($i+12) . "\"$sel>$label</option>\n";
	}
	$str .= "</select> : <select name=\"$name_minute\" class=\"dropdown textual\">\n";
	for ($i = 0; $i < 60; $i+=15) {
		$sel = $i == $selected_minute ? ' selected' : '';
		$value = $i ?: '00';
		$str .= "\t<option value=\"$value\"$sel>$value</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

/**
 *
 * @param bool $selected True means Yes is selected, False means No
 * @param string $name The name attribute of the form option element
 * @return string HTML dropdown menu
 * @abstract Generates a simple yes/no dropdown menu
 */
function dropdown_yes_no($selected, $name) {
	$str = "<select name=\"$name\" class=\"dropdown textual\">\n";
	$str .= "<option"; if ($selected) $str .= ' selected'; $str .= " value=\"1\">Yes</option>\n";
	$str .= "<option"; if (!$selected) $str .= ' selected'; $str .= " value=\"0\">No</option>\n";
	$str .= "</select>\n";
	return $str;
}

/**
 *
 * @param string $name Name of value for POST purposes
 * @param array $entries Associative array of dropdown values, e.g. array('english' => 'English', 'german' => 'Deutsch')
 * @param string $selected The key of the item to be selected, leave blank for none
 * @abstract Generates a configurable dropdown menu
 */
function dropdown_manual($name, $entries, $selected = FALSE) {
	$str = "<select name=\"$name\" class=\"textual\">\n";
	foreach($entries AS $key => $value) {
		if ($key == $selected) {
			$sel = " selected=\"selected\"";
		} else {
			$sel = '';
		}
		$str .= "<option value=\"$key\"$sel>$value</option>\n";
	}
	$str .= "</select>\n";
	return $str;
}

/**
 *
 * @param string $name The name attribute of the form input element
 * @param bool $checked Whether or not the checkbox should be checked
 * @param string $class The CSS Class to be applied to the element
 * @param string $onchange Javascript function to be executed during change event
 * @return string HTML checkbox form element
 * @abstract Generates an HTML checkbox
 */
function checkbox($name, $checked, $class = '', $onchange='') {
	$str = "<input name=\"$name\" type=\"checkbox\" class=\"$class\" id=\"$name\"";
	if ($checked)
		$str .= " checked";
	if ($onchange) {
		$str .= " onchange=\"$onchange\"";
	}
	$str .= " />";
	return $str;
}

/**
 *
 * @param bool $value The operator to check
 * @return string Yes for True and No for False
 * @abstract Displays a Yes or No
 * @todo Needs to be based on users current language
 */
function yes_no($value) {
	if ($value)
		return "Yes";
	else
		return "No";
}

/**
 *
 * @param char $selected Gender, 'M' or 'F'
 * @param string $name The name to be applied to the HTML form select element
 * @return string HTML dropdown code
 * @abstract Generates a male or female dropdown menu
 * @todo Needs to be based on users current language
 */
function dropdown_gender($selected, $name) {
	$str = "<select name=\"$name\">\n";
	$str .= "<option"; if ($selected == 'M') $str .= ' selected'; $str .= " value=\"M\">Male</option>\n";
	$str .= "<option"; if ($selected == 'F') $str .= ' selected'; $str .= " value=\"F\">Female</option>\n";
	$str .= "</select>\n";
	return $str;
}

/**
 *
 * @param string $post_item The POST name of the form input being checked
 * @return enum '1' or '0'
 * @abstract Checks whether or not the corresponding checkbox was checked
 * @todo Should this be returning a True or False?
 */
function post_checkbox($post_item) {
	$CI =& get_instance();
	if ($CI->input->post($post_item)) {
		return '1';
	} else {
		return '0';
	}
}

/**
 *
 * @param string $string The string to be trimmed
 * @param int $length The maximum length of the string
 * @return string The input string, up to the maximum specified length, with an elipses if applicable, HTML encoded
 * @abstract Prevents a string from exceeding the maximum length, for human readability purposes
 */
function trimmer($string, $length=999) {
    if (strlen($string) > $length)
        return htmlspecialchars(trim(substr($string, 0, $length))) . '&#0133;';
    else
        return htmlspecialchars($string);
}

/**
 *
 * @param string $date MySQL formatted date string (e.g. 2010-11-29)
 * @return string Date formatted in user's locale, e.g. Nov 29, 2010
 * @abstract Converts a MySQL date into a localized human readable date
 */
function local_date($date) {
	$CI =& get_instance();
	return date($CI->lang->line('date_format_short'), strtotime($date));
}

/**
 *
 * @param array $array The array to be converted
 * @return stdClass An object version of the array
 * @abstract Converts an (associative) array into a simple object, e.g. $a['x'] = 1 -> $a->x = 1
 */
function arrayToObject($array) {
	if(!is_array($array)) {
		return $array;
	}
	$object = new stdClass();
	if (is_array($array) && count($array) > 0) {
	  foreach ($array as $name=>$value) {
	     $name = strtolower(trim($name));
	     if (!empty($name)) {
	        $object->$name = arrayToObject($value);
	     }
	  }
      return $object;
	}
    else {
      return FALSE;
    }
}

/**
 *
 * @param int $number A number to be padded on the left (e.g. 5)
 * @param int $zeros The size the string should be (e.g. 2)
 * @return string Zero padded number (e.g. 05)
 * @abstract Pads a number with zero's on the left, useful for database date and time functions
 */
function zero_fill($number, $zeros) {
	return str_pad((string)$number, $zeros, "0", STR_PAD_LEFT);
}

/**
 *
 * @param string $time MySQL formatted time (e.g. 05:15:00)
 * @return string Human readable time (e.g. 5:15)
 * @abstract Converts a MySQL time into a human readable time
 */
function mysql_time($time) {
	$data = '';
	preg_match('/(?:(?:[0]?)([0-9]{1,2})):([0-9]{2}):([0-9]{2})/', $time, $data);
	$obj = Array();
	$obj['human_readable'] = $data[1] . ':' . $data[2];
	$obj['hour_float'] = $data[1] + ($data[2] / 60);
	return $obj;
}

/**
 *
 * @param float $hour_float Mathematically safe hour representation (e.g. 5.5)
 * @return string Human readable hour representation (e.g. 5:30)
 * @abstract Converts Hour-Floats into human readable time
 */
function hour_float_readable($hour_float) {
	// Input: 5.5, Output: 5:30
	$remainder = $hour_float - floor($hour_float);
	$hours = $hour_float - $remainder;
	$minutes = $remainder * 60;
	return $hours . ":" . zero_fill($minutes, 2);
}

/**
*
* Allow models to use other models
*
* This is a substitute for the inability to load models
* inside of other models in CodeIgniter.  Call it like
* this:
*
* $salaries = model_load_model('salary');
* ...
* $salary = $salaries->get_salary($employee_id);
*
* @param string $model_name The name of the model that is to be loaded
*
* @return object The requested model object
*
*/
function model_load_model($model_name) {
	$CI =& get_instance();
	$CI->load->model($model_name);
	return $CI->$model_name;
}

/**
 * @param string $name The name of the input element for POST purposes
 * @param string $value The supplied date (YYYY-MM-DD)
 * @param string $class The HTML class(es) to be applied to the element
 * @param string $id The HTML id to use, defaults to a random string
 * @return string HTML code for the input element, with a script tag to trigger the calendar
 * @abstract Generates a calendar HTML code
 * @todo Make it locale aware, instead of defaulting to MySQL format
 */
function calendar($name = 'date', $value = null, $class = 'textual calendar', $id = null) {
	if ($value === null) {
		$value = date('Y-m-d');
	}
	if ($id === null) {
		$id = 'calendar-' . rand(10000,99999);
	}
	$html = <<<EOT
<input id="$id" name='$name' class='$class' value='$value' maxlength='10' size='10' />
<script type="text/javascript">new CalendarEightysix('$id', { 'format': '%Y-%m-%d' });</script>
EOT;
	return $html;
}

/**
 *
 * @param array $raw_array Associative Array of data, usually from a database
 * @param string $group_column The name of the column to re-group items by
 * @return array Associative array, where the first level deep is the groups, and within each group is the rows
 * @abstract This function is used for taking database results and displaying them in different groups
 */
function array_regroup($raw_array, $group_column) {
	$groups = array();
	foreach($raw_array AS $array_row) {
		$groups[$array_row[$group_column]][] = $array_row;
	}
	ksort($groups);
	return $groups;
}
