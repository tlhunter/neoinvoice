<?php
/*
Description: Twitter PHP code
Author: Andrew MacBean
Version: 1.0.0
Forked By Renowned Media
*/

class Twitter {
	function  __construct($params) {
		$this->twitter_id = $params['twitter_id'];
		$this->nooftweets = isset($params['nooftweets']) ? $params['nooftweets'] : 5;
		$this->dateFormat = isset($params['dateFormat']) ? $params['dateFormat'] : "M j";
		$this->includeReplies = isset($params['includeReplies']) ? $params['includeReplies'] : FALSE;
		$this->dateTimeZone = isset($params['dateTimeZone']) ? $params['dateTimeZone'] : "America/Detroit";
	}

	/** Method to make twitter api call for the users timeline in XML */
	function twitter_status($twitter_id) {
		$response = file_get_contents("http://twitter.com/statuses/user_timeline/$twitter_id.xml");
		if (class_exists('SimpleXMLElement')) {
			$xml = new SimpleXMLElement($response);
			return $xml;
		} else {
			return $response;
		}
	}

	/** Method to add hyperlink html tags to any urls, twitter ids or hashtags in the tweet */
	function process_links($text) {
		$text = utf8_decode( $text );
		$text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $text);
		$text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" target=\"_blank\" >@\\2</a>'", $text);
		$text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://twitter.com/#!/search?q=\\2\" target=\"_blank\" >#\\2</a>'", $text);
		return $text;
	}

	/** Main method to retrieve the tweets and return html for display */
	function execute() {
		date_default_timezone_set($this->dateTimeZone);
		if ($twitter_xml = $this->twitter_status($this->twitter_id)) {
			$result = "<ul id='tweets'>\n";
			$i = 0;
			foreach ($twitter_xml->status as $key => $status) {
				if ($this->includeReplies == true | substr_count($status->text,"@") == 0 | strpos($status->text,"@") != 0) {
					$message = $this->process_links($status->text);
					$result .= "<li class='tweet'><strong>" . date($this->dateFormat, strtotime($status->created_at)) . "</strong>: " . $message . "</li>\n";
					++$i;
					if ($i == $this->nooftweets) break;
				}
			}
			$result .= "</ul>\n";
		} else {
			$result .= "<ul id='tweets'>\n<li class='tweet'>Twitter seems to be unavailable at the moment</li></ul>\n";
		}
		return $result;
	}

}