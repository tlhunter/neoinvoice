<?php
/**
 * @author Thomas Hunter
 * @copyright 2010 Renowned Media
 * @abstract Performs Wordpress related database operations
 */
class Wordpress_model extends Model {

    function __construct() {
        parent::Model();
    }

	/**
	 * @param string $category Wordpress category to select posts from
	 * @param int $limit Maximum number of entries to return
	 * @return aray Associative array of posts: id, date, content, title, url, comments
	 */
	function list_posts_by_category($category = 'updates', $limit = 5) {
		$limit += 0;
		$sql = "SELECT ID AS id, post_date AS `date`, post_content AS content, post_title AS title, CONCAT('docs/', post_name) AS url, comment_count AS comments FROM wp_posts WHERE ID IN (SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id = (SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE term_id = (SELECT term_id FROM wp_terms WHERE slug = " . $this->db->escape($category) . "))) AND post_status != 'trash' ORDER BY date DESC LIMIT $limit";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

}