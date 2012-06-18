<?php
/**
 * @mainpage
 * gChartPhp, a php wrapper for  the Google Chart Tools / Image Charts (aka Chart API) {@link http://code.google.com/apis/charttools/}
 * 
 * @version 0.5.3
 */

/**
 * @brief Utility class
 *
 * @version 0.2
 * @since 0.4
 */
class utility{
	public static function getMaxOfArray($ArrayToCheck){
		$maxValue = 0;
		
		foreach($ArrayToCheck as $temp){
			if(is_array($temp)){
				$maxValue = max($maxValue, utility::getMaxOfArray($temp));
			}
			else{
				$maxValue = max($maxValue, $temp);
			}
		}
		return $maxValue;
	}
}

/**
 * @brief Main class
 *
 * This is the mainframe of the wrapper
 *
 * @version 0.5.2
 */
class gChart{
	/**
	 * @brief This variable holds all the chart information.
	 * @var array
	 */
	private $chart;
	
	/**
	 * @brief API server URL
	 * @var string
	 * @usedby getUrl()
	 */
	private $baseUrl = "chart.apis.google.com/chart?";
	
	/**
	 * @brief Data set values.
	 * Every array entry is a data set.
	 * @var array
	 */
	protected $values = Array();
	
	/**
	 * @brief Widht of the chart
	 * @var Integer
	 */
	private $width;
	private function setWidth($width) {
		$this->width = $width;
	}
	public function getWidth() {
		return($this->width);
	}
	
	/**
	 * @brief Height of the chart
	 * @var Integer
	 */
	private $height;
	private function setHeight($height) {
		$this->height = $height;
	}
	public function getHeight() {
		return($this->height);
	}	
	
	/**
	 * @brief Data precision
	 * Defines the precision of the rounding in textEncodeData(). By default it is 2.
	 */
	private $precision = 2;
	public function setPrecision($precision) {
		$this->precision = $precision;
	} 
	public function getPrecision() {
		return $this->precision;
	}
	
	/**
	 * @brief Handles the number of items in the dataset.
	 */
	private $dataCount;
	public function setDataCount($dataCount){
		if (!isset($this->dataCount))
			$this->dataCount = $dataCount;
	}
	public function getDataCount(){
		return $this->dataCount;
	}
	
	/**
	 * @brief Data encoding char
	 * @var char
	 */
	private $dataEncodingType = 't';
	public function setEncodingType($newEncodeType) {
		$this->dataEncodingType = $newEncodeType;
	}
	public function getEncodingType() {
		return ($this->dataEncodingType);
	}
	protected function encodeData($data, $separator, $encodigData = ''){
		if ($encodigData == 's') {
			$data = $this->simpleEncodeData($data);
			$separator = '';
		} else if ($encodigData == 'e') {
			$data = $this->extendedEncodeData($data);
			$separator = '';
		} else if ($encodigData == 't') {
			$data = $this->textEncodeData($data);
		}
		$retStr = $this->separateData($data, $separator, "|");
		$retStr = trim($retStr, "|");
		return $retStr;                                
	}
	protected function separateData($data, $separator, $datasetSeparator){
		$retStr = "";
		if(!is_array($data))
			return $data;
		foreach($data as $currValue){
			if(is_array($currValue))
				$retStr .= $this->separateData($currValue, $separator, $datasetSeparator);
			else
				$retStr .= $currValue.$separator;
		}
		$retStr = trim($retStr, $separator);
		$retStr .= $datasetSeparator;
		return $retStr;
	}
	
	/**
	 * @brief Adds a data set
	 *
	 * @param $data Array Data Set values
	 */
	public function addDataSet($data){
		array_push($this->values, $data);
	}
	/**
	 * @brief Adds a hidden data set.
	 * 
	 * Use this function, used with addValueMarkers(), to build compound charts
	 *
	 * @param $hiddenData Array Data Set values
	 */
	public function addHiddenDataSet($hiddenData){
		$this->setDataCount(count($this->values));
		array_push($this->values, $hiddenData);
	}
	
	public function clearDataSets(){
		$this->values = Array();
	}
	/**
	 * @brief Encodes the data as Basic Text and Text Format with Custom Scaling. 
	 *
	 * This specifies floating point values from 0-100, inclusive, as numbers. If user sets data range,
	 * with setDataRange(), the function will do nothig and Google API will render the inage in those
	 * boundaries.
	 * 
	 * @return Array The encoded data array, rounded to the decimal point defined by setPrecision(). By default it is 2.
	 */
	private function textEncodeData($data) {
		if (isset($this->chart['chds'])) {
			return $data;
		}
		$encodedData = array();
		$max = utility::getMaxOfArray($data);
		if ($max > 100) {
			$rate = $max / 100;
			foreach ($data as $array) {
				if (is_array($array)) {
					$encodedData2 = array();
					foreach ($array as $elem) {
						array_push($encodedData2, round($elem / $rate, $this->getPrecision()));
					}
					array_push($encodedData, $encodedData2);
				} else {
					array_push($encodedData, round($array / $rate, $this->getPrecision()));
				}
			}
		} else {
			$encodedData = $data;
		}
		return $encodedData;
	}
	/**
	 * @brief Encodes the data as Simple Text.
	 * This specifies integer values from 0-61, inclusive, encoded by a single alphanumeric character. 
	 * This results in the shortest data string URL of all the data formats.
	 *
	 * @todo Add support for missing values
	 */
	private function simpleEncodeData($data){
		$encode_string='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$max = utility::getMaxOfArray($data);
		$encodedData = array();
		if ($max > 61){
			$rate = $max / 61.0;
			foreach($data as $array) {
				if (is_array($array)) {
					$encodedData2 = array();
					foreach ($array as $elem){
						$index = (int)$elem/$rate;
						array_push($encodedData2, $encode_string[$index]);
					}
					array_push($encodedData, $encodedData2);
				} else {
					$index = (int)$array/$rate;
					array_push($encodedData, $encode_string[$index]);
				}
			}
		} else {
			foreach($data as $array) {
				if (is_array($array)) {
					$encodedData2 = array();
					foreach ($array as $elem){
						array_push($encodedData2, $encode_string[$elem]);
					}
					array_push($encodedData, $encodedData2);
				} else {
					array_push($encodedData, $encode_string[$array]);
				}
			}
		}
		return $encodedData;
	}
	/**
	 * @brief Encodes the data as Extended Text.
	 * 
	 * This specifies integer values from 0-4095, inclusive, encoded by two alphanumeric characters.
	 *
	 * @todo Add support for missing values
	 */
	private function extendedEncodeData($data){
		$encode_string='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
		$max = utility::getMaxOfArray($data);
		$encodedData = array();
		if ($max > 4095){
			$rate = $max/4095.0;
			foreach ($data as $array)
				if (is_array($array)) {
					$encodedData2 = array();
					foreach ($array as $elem){
						$toEncode=(int)$elem/$rate;
						$s='';
						for ($i=0;$i<2;++$i){
							$m = $toEncode%64;
							$toEncode/=64;
							$s = $encode_string[$m].$s;
						}
						array_push($encodedData2, $s);
					}
					array_push($encodedData, $encodedData2);
				} else {
					$toEncode=(int)$array/$rate;
					$s='';
					for ($i=0;$i<2;++$i){
						$m = $toEncode%64;
						$toEncode/=64;
						$s = $encode_string[$m].$s;
					}
					array_push($encodedData, $s);	
				}
		} else {
			foreach ($data as $array)
				if (is_array($array)) {
					foreach ($array as $elem){
						$s='';
						$toEncode = $elem;
						for ($i=0; $i<2; ++$i){
							$m = $toEncode%64;
							$toEncode /= 64;
							$s = $encode_string[$m].$s;
						}
						array_push($encodedData2, $s);
					}
					array_push($encodedData, $encodedData2);
				} else {
					$s='';
					$toEncode = $array;
					for ($i=0; $i<2; ++$i){
						$m = $toEncode%64;
						$toEncode /= 64;
						$s = $encode_string[$m].$s;
					}
					array_push($encodedData, $s);
				}
		}
		return $encodedData;
	}
	
	/**
	 * @brief Returns the applicable labels, based on the number of data sets of the chart.
	 */
	public function getApplicableLabels($labels){
		return array_splice($labels, 0, count($this->values));
	}
	
	/**
	 * @brief Server number processing the chart
	 * @var Integer
	 */
	private $serverNum;
	/**
	 * @brief Sets server number processing the chart.
	 * @param $newServerNum Integer The server number. The function will scale this number to the range 0-9
	 */
	public function setServerNumber($newServerNum){
		$this->serverNum = $newServerNum % 10;
	}
	/**
	 * @brief Returns the server number processing the chart
	 * @return Integer
	 */
	public function getServerNumber(){
		return ($this->serverNum);
	}
	
	/**
	 * @brief Sets the chart property
	 * @param $key String Name of the chart parameter
	 * @param $value String Value of the chart parameter
	 */
	public function setProperty($key, $value, $append = false, $dataSetSeparator = '|') {
		if ($append && isset($this->chart[$key])) {
			$this -> chart[$key] = $this -> chart[$key].$dataSetSeparator.$value;
		} else {
			$this -> chart[$key] = $value;
		}
	}
	/**
	 * @brief Sets chart dimensions.
	 *
	 * Sets chart dimension using chs parameter. This checks of $width and $height are 
	 * defined because in gFormula 0s are used as default values to let the server 
	 * autosize the final png image. If only $hegiht is not 0, then the server will use
	 * this value as the height of the png image and will autosize the width.
	 *
	 * @param $width Integer
	 * @param $height Integer
	 */
	public function setDimensions($width, $height) {
		$this -> setWidth($width);
		$this -> setHeight($height);
		if ($width && $height) {
			$this -> setProperty('chs', $width.'x'.$height);
		}
		else if ($height) 
			$this -> setProperty('chs', $height);
	}
	/**
	 * @brief Sets the colors for element of the chart.
	 *
	 * This is the basic function. The data in the array are interpreted as one color one data set.
	 *
	 * @param $colors Array Specifies colors using a 6-character string of hexadecimal values, 
	 *                      plus two optional transparency values, in the format RRGGBB[AA]. 
	 */
	public function setColors($colors) {
		$this -> setProperty('chco', $this->encodeData($this->getApplicableLabels($colors),","));
	}
	/**
	 * @brief Sets the labels for the legend
	 *
	 * @param $labels Array
	 */
	public function setLegend($labels) {
		$this -> setProperty('chdl', urlencode($this->encodeData($this->getApplicableLabels($labels),"|")));
	}
	/**
	 * @brief Sets the position and the order of the legend
	 *
	 * @param $position String Please refer to the documentation for the acceptable values
	 * @param $order String Please refer to the documentation for the acceptable values
	 */
	public function setLegendPosition($position, $order = null) {
		if (isset($order)) {
			$this -> setProperty('chdlp', $position.'|'.$order);
		} else {
			$this -> setProperty('chdlp', $position);
		}
	}
	/**
	 * @brief Sets the title.
	 * 
	 * You cannot specify where this appears.
	 *
	 * @param $title String Title to show for the chart. Use \r\n for a new line.
	 */
	public function setTitle($title) {
		$title = str_replace("\r\n", "|", $title);
		$title = str_replace(" ", "+", $title);
		$this -> setProperty('chtt', $title);
	}
	/**
	 *	@brief Sets font size and color of the title
	 * 
	 * @param $color String The title color, in RRGGBB hexadecimal format. Default color is black.
	 * @param $size Integer Font size of the title, in points.
	 */
	public function setTitleOptions($color, $size) {
		$this -> setProperty('chts', $color.','.$size);
	}
	/**
	 * @brief Specifies the size of the chart's margins, in pixels.
	 *
	 * You can specify the size of the chart's margins, in pixels. Margins are calculated inward from the 
	 * specified chart size (chs); increasing the margin size does not increase the total chart size, but 
	 * rather shrinks the chart area, if necessary.
	 *
	 * @param $chartMargins Array An array of four integers: <left_margin>, <right_margin>, <top_margin>, <bottom_margin>
	 * @param $legendMargins Array An array of two integers: <legend_width>, <legend_height>. Optional
	 */
	public function setChartMargins($chartMargins, $legendMargins = array()) {
		$this -> setProperty('chma', $this -> encodeData($chartMargins, ','));
		if (!empty($legendMargins))
			$this -> setProperty('chma', $this -> encodeData($legendMargins, ','), true);
	}
	/**
	 * @brief Sets visible axes.
	 *
	 * @param $visibleAxes Array Visible axis labels. Please refer to the documentation for the acceptable values
	 */
	public function setVisibleAxes($visibleAxes) {
		$this->setProperty('chxt', $this->encodeData($visibleAxes,','));
	}
	/**
	 * @brief Specifies the range of values that appear on each axis independently.
	 *
	 * @param $axisIndex Integer This is a zero-based index into the axis array specified by setVisibleAxes
	 * @param $startVal Integer A number, defining the low value for this axis.
	 * @param $endVal Integer A number, defining the high value for this axis.
	 * @param $stem Integer The count step between ticks on the axis. There is no default step value; the step 
	 *                      is calculated to try to show a set of nicely spaced labels.
	 */
	public function addAxisRange($axisIndex, $startVal, $endVal, $step = null) {
		if (is_null($step))
			$axisRange = array($axisIndex, $startVal, $endVal);
		else
			$axisRange = array($axisIndex, $startVal, $endVal, $step);
		$this -> setProperty('chxr', $this->encodeData($axisRange, ',') , true);
	}
	/**
	 * @brief Specifies the labels that appear on each axis independently.
	 *
	 * @param $axisIndex Integer This is a zero-based index into the axis array specified by setVisibleAxes
	 * @param $axisLabel Array One or more labels to place along this axis.
	 */
	public function addAxisLabel($axisIndex, $axisLabel) {
		$this->setProperty('chxl', $this->encodeData(array_merge(array($axisIndex.':'), $axisLabel), '|'), true);
	}
	/**
	 * @brief Specifies the label positions on each axis independently.
	 *
	 * You can specify which axis labels to show, whether using the default labels or custom labels 
	 * specified using this function. If you do not specify exact positions using this parameter, labels 
	 * will be spaced evenly and at a default step value along the axes. If you do not call this function, 
	 * then the tick mark labels will be the default values (typically data values, or the bar numbers 
	 * in bar charts).
	 *
	 * @param $axisIndex Integer This is a zero-based index into the axis array specified by setVisibleAxes()
	 * @param $labelPositions Array The position of the label along the axis. This is a comma-separated list of 
	 *                              numeric values, where each value sets the position of the corresponding label 
	 *                              in the addAxisLabel(): the first entry applies to the first label, and so on.
	 *                              The position is a value in the range for that axis. Note that this will always 
	 *                              be 0—100 unless you have specified a custom range using addAxisRange(). You 
	 *                              must have as many positions as you have labels for that axis.
	 */
	public function addAxisLabelPositions($axisIndex, $labelPositions) {
		$this->setProperty('chxp', $axisIndex.','.$this->encodeData($labelPositions, ','), true);
	}
	/**
	 * @brief Specifies the data range. 
	 * 
	 * Note that this does not change the axis range; to change the axis range, you must 
	 * use the setAxisRange function.
	 *
	 * @param $startVal Integer A number, definig the low value for the data set. Usually, it is the same as $startVal in addAxisRange
	 * @param $endVal Integer A number, definig the high value for the data set. Usually, it is the same as $endVal in addAxisRange
	 */
	public function setDataRange($startVal, $endVal) {
		$this->setProperty('chds', $startVal.','.$endVal);
	}
	/**
	 * @brief Adds the background fill 
	 *
	 * Specifies a solid fill for the background and/or chart area, or assign a transparency value to the whole chart.
	 *
	 * @param $fillType String The part of the chart being filled. Please refer to the documentation for the acceptable values
	 * @param $color String The fill color, in RRGGBB hexadecimal format. For transparencies, the first six digits are ignored, 
	 *                      but must be included anyway.
	 */
	public function addBackgroundFill($fillType, $color) {
		$this->setProperty('chf', $this->encodeData(array($fillType, 's', $color), ','), true);
	}
	/**
	 * @brief Applies one or more gradient fills to chart areas or backgrounds.
	 *
	 * Each gradient fill specifies an angle, and then two or more colors anchored to a specified location. The color varies 
	 * as it moves from one anchor to another. You must have at least two colors with different <color_centerpoint>  values, 
	 * so that one can fade into the other. Each additional gradient is specified by a <color>,<color_centerpoint>  pair.
	 *
	 * @param $fillType String The part of the chart being filled. Please refer to the documentation for the acceptable values
	 * @param $fillAngle Integer A number specifying the angle of the gradient from 0 (horizontal) to 90 (vertical). 
	 * @param $colors Array An array of couples <color> (The color of the fill, in RRGGBB hexadecimal format) and 
	 *                      <color_centerpoint> (Specifies the anchor point for the color. The color will start to fade from this 
	 *                      point as it approaches another anchor. The value range is from 0.0 (bottom or left edge) to 1.0 (top 
	 *                      or right edge), tilted at the angle specified by <angle>). Please define it in this way:
	 *                      array(<color_1>,<color_centerpoint_1>,...,<color_n>,<color_centerpoint_n>).
	 */
	public function setGradientFill($fillType, $fillAngle, $colors) {
		$this->setProperty('chf', $this->encodeData(array_merge(array($fillType, 'lg', $fillAngle), $colors), ','));
	}
	/**
	 * @brief Specifies a striped background fill for your chart area, or the whole chart. 
	 *
	 * @param $fillType String The part of the chart being filled. Please refer to the documentation for the acceptable values
	 * @param $fillAngle Integer A number specifying the angle of the gradient from 0 (horizontal) to 90 (vertical). 
	 * @param $colors Array An array of couples <color> (The color of the fill, in RRGGBB hexadecimal format) and <width>
     *                      (The width of this stripe, from 0 to 1, where 1 is the full width of the chart. Stripes are repeated 
     *                      until the chart is filled. Repeat <color> and <width> for each additional stripe. You must have at 
     *                      least two stripes. Stripes alternate until the chart is filled).	Please define it in this way:
     *                      array(<color_1>,<width_1>,...,<color_n>,<width_n>).
	 */
	public function setStripFill($fillType, $fillAngle, $colors) {
		$this->setProperty('chf', $this->encodeData(array_merge(array($fillType, 'ls', $fillAngle), $colors), ','));
	}
	/**
	 * @brief Fills the area below a data line with a solid color.
	 *
	 * @param $where Char Whether to fill to the bottom of the chart, or just to the next lower line. 
	 *                    Must be B or b. Please refer to the documentation for the acceptable values
	 * @param $color String An RRGGBB format hexadecimal number of the fill color
	 * @param $startLineIndex Integer The index of the line at which the fill starts. The first data 
	 *                                series specified in addDataSet() has an index of zero (0), the 
	 *                                second data series has an index of 1, and so on.
	 * @param $endLineIndex Integer Please refer to the documentation for the usage of this parameter.
	 */
	public function addLineFill($where, $color, $startLineIndex, $endLineIndex) {
		$this->setProperty('chm', $this->encodeData(array($where, $color, $startLineIndex, $endLineIndex, 0),','), true);
	}
	/**
	 * @brief Specifies solid or dotted grid lines on your chart
	 *
	 * @param $xAxisStepSize Ingeger Used to calculate how many x grid lines to show on the chart. 
	 *                               100 / step_size = how many grid lines on the chart.
	 * @param $yAxisStepSize Integer Used to calculate how many x or y grid lines to show on the chart. 
	 *                               100 / step_size = how many grid lines on the chart.
	 * @param $dashLength Integerthe Length of each line dash, in pixels. By default it is 4
	 * @param $spaceLength Integer The spacing between dashes, in pixels. Specify 0 for for a solid line. 
	 *                             By default it is 1
	 * @param $xOffset Integer The number of units, according to the chart scale, to offset the x grid line.
	 * @param $yOffset Integer The number of units, according to the chart scale, to offset the y grid line.
	 */
	public function setGridLines($xAxisStepSize, $yAxisStepSize, $dashLength = 4, $spaceLength = 1, $xOffset = 0, $yOffset = 0) {
		$this->setProperty('chg', $this->encodeData(array($xAxisStepSize, $yAxisStepSize, $dashLength, $spaceLength, $xOffset, $yOffset), ','));
	}
	/**
	 * @brief Labels specific points on your chart with custom text, or with formatted versions of the data at that point.
	 *
	 * Please note that this function has an variable number of input variables. The order of the variable 
	 * must be the following:
	 *	- marker_type: The type of marker to use. Please refer to the documentation for usage.
	 *	- color: The color of the markers for this set, in RRGGBB hexadecimal format.
	 *	- series_index: The zero-based index of the data series on which to draw the markers. The index is 
	 *    defined by the order of addDataSet()
	 *	- which_points: [Optional] Which point(s) to draw markers on. Default is all markers. Use '' (blank 
	 *    string) for default.
	 *	- size: The size of the marker in pixels.
	 *	- z_order: [Optional] The layer on which to draw the marker, compared to other markers and all other 
	 *    chart elements.
	 *	- placement: [Optional] Additional placement details describing where to put this marker, in relation 
	 *    to the data point. 
	 * You can omit the last two values when using this function.
	 */
	public function addValueMarkers() {
		$args = func_get_args();
		$this->setProperty('chm', $this->encodeData($args, ','), true);
	}
	
	/**
	 * @brief Prepares the Data Set String
	 */
	protected function setDataSetString() {
		if(!empty($this->values)) {
			$this -> setProperty('chd', $this->getEncodingType().$this->getDataCount().':'.$this->encodeData($this->values,',',$this->getEncodingType()));
		}
	}
	
	/**
	 * @brief Returns the url code for the image.
	 */
	public function getUrl(){
        $fullUrl = "http://";
        if(isset($this->serverNum))
            $fullUrl .= $this->getServerNumber().".";
        $fullUrl .= $this->baseUrl;
        $this -> setDataSetString();
        $parms = array();
        foreach ($this -> chart as $key => $value) {
            $parms[] = $key.'='.$value;
        }
        return $fullUrl.implode('&amp;', $parms);
}

	/**
	 * @brief Returns the html img code.
	 * 
	 * This code is HTML 4.01 strict compliant.
	 */
	public function getImgCode(){
		$code = '<img src="';
		$code .= $this->getUrl().'"';
		$code .= 'alt="gChartPhp Chart" width='.$this->width.' height='.$this->height.'>';
		print($code);
	}
	/**
	 * @brief Serversite chart renderer
	 *
	 * See view.html and img.php for an example of how to use this function.
	 * Please refer to the API documentation for further examples.
	 * 
	 * @param $post Bool If true, the renderer will use a POST request for the image. If false, the 
	 *                   renderer will use the standard url request. By default, the renderer will use
	 *                   the url request.
	 */
	public function renderImage($post = false){
		header('content-type: image/png');
		if ($post) {
			$this->setDataSetString();
			$url = 'http://chart.apis.google.com/chart?chid=' . md5(uniqid(rand(), true));
			$context = stream_context_create(
		    array('http' => array(
		      'method' => 'POST',
			  'content' => urldecode(http_build_query($this->chart)))));
		  	fpassthru(fopen($url, 'r', false, $context));
		} else {
	       	$url = str_replace('&amp;','&',$this->getUrl());
	       	readfile($url);
		}
	}
}

class gPieChart extends gChart{
	function __construct($width = 350, $height = 200) {
		$this -> setProperty('cht', 'p');
		$this -> setDimensions($width, $height);
	}
	public function getApplicableLabels($labels) {
		return array_splice($labels, 0, count($this->values[0]));
	}
	public function set3D($is3d = true, $resize = true){
		if($is3d){
			$this -> setProperty('cht', 'p3');
			if ($resize)
				$this -> setDimensions($this->getWidth() * 1.5, $this->getHeight());
		}
		else {
			$this -> setProperty('cht', 'p');
			if ($resize)
				$this -> setDimensions($this->getWidth() / 1.5, $this->getHeight());
		}
	}
	/**
	 * @brief Sets the labels for Pie Charts
	 *
	 * @param $labels Array
	 */
	public function setLabels($labels) {
		$this -> setProperty('chl', urlencode($this->encodeData($this->getApplicableLabels($labels),"|")));
	}
	/**
	 * @brief Rotates the chart.
	 *
	 * By default, the first series is drawn starting at 3:00, continuing clockwise around the chart, but 
	 * you can specify a custom rotation using this function.
	 *
	 * @param $angle Integer A floating point value describing how many radians to rotate the chart clockwise. 
	 *                       One complete turn is 2 pi radiants (2 pi is about 6.2831).
	 * @param $degree Bool Specifies if $angle is in degrees and not in radians. The function will to the conversion.
	 */
	public function setRotation($angle, $degree = false) {
		if ($degree)
			$angle = ($angle / 360) * 6.2831;
		$this -> setProperty('chp', $angle);
	}
	/**
	 * @brief Sets the colors for element of the chart.
	 *
	 * This is the basic function. The data in the array are interpreted as one color one slice. If you are
	 * using gConcentricPieChart(), consider using setColors() for more customization.
	 *
	 * @param $colors Array Specifies colors using a 6-character string of hexadecimal values, 
	 *                      plus two optional transparency values, in the format RRGGBB[AA]. 
	 */
	public function setColors($colors) {
		$this -> setProperty('chco', $this->encodeData($this->getApplicableLabels($colors), "|"), true);
	}
	/**
	 * @brief Sets colors for each data set.
	 *
	 * This function allows you to specify colors for each individual slice of the chart or to specify a 
	 * color gradient. Usage:
	 * - One color one slice: addColors(array($colorSlice1, .., $colorSliceN)). If there are less color than
	 *   slices, the colors are repeated
	 * - Gradient filling: addColors(array($gradientColor)). The chart will be colored in a gradient of
	 *   $gradientColor
	 * If you are using gConcentricPie class, run an instance of this function for each data set.
	 *
	 * @param $colors Array Specifies colors using a 6-character string of hexadecimal values,
	 *                      plus two optional transparency values, in the format RRGGBB[AA]
	 */
	public function addColors($colors) {
		$this -> setProperty('chco', $this->encodeData($colors, "|"), true, ",");
	}
}
/**
 * @brief 3-dimensional Pie Chart
 */
class gPie3DChart extends gPieChart {
	function __construct($width = 500, $height = 200) {
		$this -> setProperty('cht', 'p3');
		$this -> setDimensions($width, $height);
	}
}
/**
 * @brief Concentric Pie Chart
 */
class gConcentricPieChart extends gPieChart {
	function __construct($width = 350, $height = 200) {
		$this -> setProperty('cht', 'pc');
		$this -> setDimensions($width, $height);
	}
	/**
	 * @brief Returns the applicable labels for the chart.
	 * 
	 * This function counts recursively the numeber of values in the $values array.
	 * @return Array Applicable labels
	 */
	public function getApplicableLabels($labels) {
		return array_splice($labels, 0, count($this->values, COUNT_RECURSIVE));
	}
	/**
	 * @brief Adds the legend for Concentric Pie Charts
	 *
	 * Run an instance of this function for each data set.
	 *
	 * @param $labels Array
	 */
	public function addLegend($labels) {
		$this -> setProperty('chdl', urlencode($this->encodeData($this->getApplicableLabels($labels),"|")), true);
	}
}

class gLineChart extends gChart{
		
	function __construct($width = 200, $height = 200){
		$this -> setProperty('cht', 'lc');
		$this -> setDimensions($width, $height);		
	}
	public function getUrl() {
		$retStr = parent::getUrl();
		return $retStr;	
	}
}

class gBarChart extends gChart{
	/**
	 * @brief Constructor for the gBarChart
	 *
	 * With this constructor you can specify all the type of Bar Charts.
	 *
	 * @param $width Integer Width of the chart, in pixels. Default value is 200.
	 * @param $height Integer Height of the chart, in pixels. Default value is 200.
	 * @param $type String Chooses the type of chart. Use 'g' for grouped chart, 's' for stacked, 'o' for overlapped
	 * @param $direction String Chooses the direction of the chart. Use 'v' for vertical, 'h' for horizontal
	 */
	function __construct($width = 200, $height = 200, $type = 'g', $direction='v'){
		$this -> setChartType($type, $direction);
		$this -> setDimensions($width, $height);	
	}
	protected function setChartType($type, $direction) {
		$this-> setProperty('cht', 'b'.$direction.$type);
	}
	public function getUrl(){
		$retStr = parent::getUrl();
		return $retStr;
	}
	/**
	 * @brief Specifies custom values for bar widths and spacing between bars and groups.
	 *
	 * You can only specify one set of width values for all bars. If you don't set this, all bars will be 23 pixels wide, 
	 * which means that the end bars can be clipped if the total bar + space width is wider than the chart width.
	 *
	 * @param $barWidth Integer The width of the bar. You can specify widths and spacing absolutely. Default 
	 *                          value is 23 pixels, absolute value.
	 * @param $spaceBetweenBars Integer Space between bars in the same group. This is a width in pixels. Default value is 4 pixels 
	 *                                  for absolute values.
	 * @param $spaceBetweenGroups Integer Space between bar groups in the same group. This is a width in pixels; Default value 
	 *                                    is 8 pixels for absolute values.
	 */
	public function setBarWidth($barWidth, $spaceBetweenBars = 4,$spaceBetweenGroups = 8){
		$this -> setProperty('chbh', $this->encodeData(array($barWidth, $spaceBetweenBars,$spaceBetweenGroups), ','));
	}
	/**
	 * @brief Resize values automatically
	 */
	public function setAutoBarWidth() {
		$this -> setProperty('chbh', 'a');
	}
	/**
	 * @brief Specify custom values for bar widths and spacing between bars and groups.
	 *
	 * You can specify widths and spacing absolutely or relatively, by entering one of the following values.
	 *
	 * @param $barScale String You can specify widths and spacing absolutely or relatively, by entering one of the following values:
	 *                         - a: space_between_bars and space_between_groups  are given in absolute units (or default absolute 
	 *                             values, if not specified). Bars will be resized so that all bars will fit in the chart.
	 *                         - r: space_between_bars and space_between_groups are given in relative units (or default relative values, 
	 *                              if not specified) Relative units are floating point values compared to the bar width, where the bar 
	 *                              width is 1.0: for example, 0.5 is half the bar width, 2.0 is twice the bar width. Bars can be clipped 
	 *                              if the chart isn't wide enough.
	 *                         Default value is 'a'
	 * @param $spaceBetweenBars Integer Space between bars in the same group. This is a width in pixels. Default value is 4 pixels 
	 *                                  for absolute values.
	 * @param $spaceBetweenGroups Integer Space between bar groups in the same group. This is a width in pixels; Default value 
	 *                                    is 8 pixels for absolute values.
	 */
	public function setBarScale($barScale = 'a', $spaceBetweenBars = '4',$spaceBetweenGroups = '8') {
		$this -> setProperty('chbh', $this->encodeData(array($barScale, $spaceBetweenBars,$spaceBetweenGroups), ','));
	}
	/**
	 * @brief Sets colors for each data set.
	 *
	 * This function allows you to specify colors for each individual slice of the chart or to specify a 
	 * color gradient. Usage:
	 * - One color one bar: addColors(array($colorBar1, .., $colorBarN)). If there are less colors than
	 *   bars, colors will be repeated
	 *
	 * @param $colors Array Specifies colors using a 6-character string of hexadecimal values,
	 *                      plus two optional transparency values, in the format RRGGBB[AA]
	 */
	public function addColors($colors) {
		$this -> setProperty('chco', $this->encodeData($colors, "|"), true, ",");
	}
}
class gGroupedBarChart extends gBarChart{
	function __construct($width = 200, $height = 200){
		$this -> setChartType('g', 'v');
		$this -> setDimensions($width, $height);	
	}
	public function setHorizontal($isHorizontal = true){
		if($isHorizontal) {
			$this -> setChartType('g', 'h');
		} else {
			$this -> setChartType('g', 'v');
		}
	}	
}
class gStackedBarChart extends gBarChart{
	function __construct($width = 200, $height = 200){
		$this -> setChartType('s', 'v');
		$this -> setDimensions($width, $height);			
	}
	public function setHorizontal($isHorizontal = true){
		if($isHorizontal){
			$this -> setChartType('s', 'h');
		}
		else{
			$this -> setChartType('s', 'v');
		}
	}
}
class gOverlappedBarChart extends gBarChart{
	function __construct($width = 200, $height = 200){
		$this -> setChartType('o', 'v');
		$this->setDimensions($width, $height);			
	}
	public function setHorizontal($isHorizontal = true){
		if($isHorizontal){
			$this -> setChartType('o', 'h');
		}
		else{
			$this -> setChartType('o', 'v');
		}
	}
}

class gVennDiagram extends gChart{
	
	private $sizes;
	private $intersections;
	private $numData;
		
	function __construct($width = 200, $height = 200){
		$this -> setProperty('cht', 'v');
		$this -> setDimensions($width, $height);
		$this -> sizes = array(0,0,0);	
		$this -> intersections = array(0,0,0,0);
		$this->numData = 2;	
	}
	public function setSizes($A=0, $B=0, $C=0){
		if ($C)
			$this->numData = 3;
		$this->sizes = array($A, $B, $C);
	}
	public function setIntersections($AB=0, $AC=0, $BC=0, $ABC=0){
		$this->intersections = array($AB, $AC, $BC, $ABC);
	}
	public function setDataSetString(){
		$fullDataSet = array_merge($this->sizes, $this->intersections);
		$this -> setProperty('chd', $this->getEncodingType().":".$this->encodeData($fullDataSet, ',', $this->getEncodingType()));
	}
	public function getApplicableLabels($labels) {
		return array_splice($labels, 0, $this->numData);
	}
}

class gFormula extends gChart{
	/**
	 * @param $widht Integer It is set by default to 0 because the server will size the png automatically
	 * @param $height Integer It is set by default to 0 because the server will size the png automatically
	 */
	function __construct($width = 0, $height = 0){
		$this -> setDimensions($width, $height);
		$this -> setProperty('cht','tx');
	}
	
	public function setLatexCode($latexCode){
		$this -> setProperty('chl', urlencode($latexCode));
	}
	public function setTextColor($textColor){
		$this -> setProperty('chco', $textColor);
	}
	
	public function getImgCode(){
		$code = '<img src="';
		$code .= $this->getUrl().'"';
		$code .= 'alt="gChartPhp Chart"'; 
		if($this->width)
			$code .= ' width='.$this->width;
		if($this->height)
			$code .= ' height='.$this->height;
		$code .= '>';
		print($code);
	}
}

class gQRCode extends gChart{
	
	function __construct($width = 150, $height = 150){
		$this -> setDimensions($width, $height);
		$this -> setProperty('cht','qr');
	}
	
	public function setQRCode($QRCode){
		$this -> setProperty('chl', urlencode($QRCode));
	}
	/**
	 * @param $newOutputEncoding String Please refer to the documentation for the acceptable values
	 */
	public function setOutputEncoding($newOutputEncoding){
		$this -> setProperty('choe', $newOutputEncoding);
	}
	/**
	 * @param $newErrorCorrectionLevel String Please refer to the documentation for the acceptable values
	 * @param $newMargin Integer Please refer to the documentation for the acceptable values
	 */
	public function setErrorCorrectionLevel($newErrorCorrectionLevel, $newMargin){
		$this -> setProperty('chld', $newErrorCorrectionLevel.'|'.$newMargin);
	}
}

class gMeterChart extends gChart{
	/**
	 * @brief Google-o-Meter Chart constructor.
	 *
	 * Please see documentation for specia usage of functions setVisibleAxes(), addAxisLabel(), and setColors().
	 */
	function __construct($width = 200, $height = 200){
		$this -> setDimensions($width, $height);
		$this -> setProperty('cht','gom');
	}
	public function getApplicableLabels($labels) {
		return array_splice($labels, 0, count($this->values[0]));
	}
	/**
	 * @brief Sets the labels for each arrow
	 *
	 * You can obtain the same result of this function by setting visible axis x and adding the labels on that axis.
	 */
	public function setLabels($labels) {
		$this -> setProperty('chl', urlencode($this->encodeData($this->getApplicableLabels($labels),"|")));
	}
}

class gMapChart extends gChart {
	/**
	 * @brief Map Chart constructor. 
	 * 
	 * Maximum size for a map is 440x220, this is the defaul size.
	 */
	function __construct($width = 440, $height = 220){
		$this -> setDimensions($width, $height);
		$this -> setProperty('cht','t');
	}
	
	/**
	 * @brief Geographical area shown in the chart.
	 *
	 * @param $zoomArea String One of the following values: africa, asia, europe, middle_east, south_america, usa, world
	 */
	public function setZoomArea($zoomArea) {
		$this -> setProperty('chtm', $zoomArea);
	}
	/**
	 * @brief A list of countries or states to which you are applying values. 
	 *
	 * @param $stateCodes Array These are a set of two-character codes. Use either of the following types (you cannot mix types):
	 *                          ISO 3166-1-alpha-2 codes for countries, {@link http://www.iso.org/iso/english_country_names_and_code_elements}
     *                          USA state code
	 */
	public function setStateCodes($stateCodes){
		$this -> setProperty('chld', $this->encodeData($stateCodes, ''));
	}
	/**
	 * @brief Specifies the colors of the chart
	 *
	 * @param $defaultColor String The color of regions that do not have data assigned. An RRGGBB format 
	 *                             hexadecimal number. Suggested value is BEBEBE (medium gray). 
	 * @param $gradientColors Array Optional. The colors corresponding to the gradient values in the data 
	 *                              format range. RRGGBB format hexadecimal numbers. The default values are
	 *                              0000FF and FF0000.
	 */
	public function setColors($defaultColor) {
		$gradientColors = (func_num_args() > 1) ? func_get_arg(1) : array('0000FF', 'FF0000');
		$this -> setProperty('chco', $this->encodeData(array_merge(array($defaultColor), $gradientColors), ','));
	}

	public function getApplicableLabels($labels) {
		return array_splice($labels, 0, count($this->values[0]));
	}
}
class gScatterChart extends gChart{
	function __construct($width = 200, $height = 200){
		$this -> setDimensions($width, $height);
		$this -> setProperty('cht','s');
	}
	/**
	 * @brief Returns the applicable labels
	 *
	 * There is no reason to use this function. Please refer to the documentation to know how to use colors and legend.
	 */
	public function getApplicableLabels($labels) {
		return $labels;
	}
	/**
	 * @brief Sets the colors for the chart.
	 * 
	 * It has a different separator than the one in the parent class
	 */
	public function setColors($colors) {
		$this -> setProperty('chco', $this->encodeData($this->getApplicableLabels($colors),"|"));
	}
}
?>