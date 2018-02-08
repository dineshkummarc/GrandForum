<?php

/**
 * @package GrandObjects
 */

class BibTexExporter {
	var $document_type;
	var $authors;
	var $title;
	var $journal;
	var $year;
	var $pages;
	var $doi;
	var $note;
	var $url;
	var $source;

	/**
	* Takes a Paper object and converts to BibTeX format
	* returns a String
	*/
	static function exportProduct($paper) {
		$bibtex = "@";
		$data = $paper->data;
		$type = self::getType($paper->type);
		$bibtex .= $type . "{" . $paper->bibtex_id . ",\n";
		$bibtex .= "author=";
		print_r($paper->getAuthors()[0]->name);
		// foreach($paper->getAuthors() as $person) {
		// 	print_r($person->getReversedName() . ". and ");
		// }
		print_r($bibtex);
		exit;
	}

	private static function getType($type) {
		// switch different product type to get first thing in bibtex
		switch ($type) {
			case "Conference Paper":
				return "CONFERENCE";
			default:
				return $type;
		} 
	}

	private static function formatAuthors($authors) {

	}
}
?>