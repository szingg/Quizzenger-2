<?php

class CategoryModel{

	var $mysqli;
	var $logger;
	
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	
	function getEntryByID($id){
		$result = $this->mysqli->s_query("SELECT * FROM category where id=?",array('i'),array($id),true);
		return $this->mysqli->getSingleResult($result);
	}
	
	function removeCategory($category_id){
		$this->logger->log ( "Removing Category with associated questions, ".$category_id, Logger::INFO );
		$result = $this->mysqli->s_query("DELETE FROM category WHERE id = ?",array('i'),array($category_id));
	}

	function createCategory($name,$parent_id){
		$this->logger->log ( "Creating new Category with name: ".$name." and parent id: ".$parent_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO category (name,parent_id) VALUES (?,?)",array('s','i'),array($name,$parent_id));
	}
	
	function getNameByID($id) {
		$result = $this->mysqli->s_query("SELECT name FROM category WHERE id=?",array('i'),array($id),true);
		$obj=mysqli_fetch_object($result);
		return $obj->name;
	}
	
	function getChildren($id){ // get all direct children of a category
		$result = $this->mysqli->s_query("SELECT * FROM category WHERE parent_id=? ORDER BY name asc",array('i'),array($id));
		return $this->mysqli->getQueryResultArray($result);
	}
	

	function getAllChildren($id){
		$allChildren = array();
		$resultChildren = $this->mysqli->s_query("SELECT * FROM category WHERE parent_id=? ORDER BY name asc",array('i'),array($id));
		$resultChildren = $this->mysqli->getQueryResultArray($resultChildren);
		foreach ($resultChildren as $child){
			array_push($allChildren, $child);
			array_push($allChildren,$this->mysqli->getQueryResultArray($this->mysqli->s_query("SELECT * FROM category WHERE parent_id=? ORDER BY name asc",array('i'),array($child['id']))));
		}
		return $allChildren;
	}
	
	function getAllChildrenIDs($id){
		$allChildren = array();
		$resultChildren = $this->mysqli->s_query("SELECT * FROM category WHERE parent_id=? ORDER BY name asc",array('i'),array($id));
		$resultChildren = $this->mysqli->getQueryResultArray($resultChildren);
		foreach ($resultChildren as $child){
			array_push($allChildren, $child['id']);
			
			$resultSubChildren= $this->mysqli->s_query("SELECT * FROM category WHERE parent_id=? ORDER BY name asc",array('i'),array($child['id']));
			$resultSubChildren=$this->mysqli->getQueryResultArray($resultSubChildren);
			foreach ($resultSubChildren as $subChild){
				array_push($allChildren,$subChild['id']);
			}
			
		}
		return $allChildren;
	}
	
	function getAllTrueChildren(){ // gets all true children, meaning they aren't parents for anybody
		$result = $this->mysqli->s_query("SELECT * FROM category ct WHERE ct.id not in (SELECT parent_id FROM category)  ORDER BY name asc",array(),array());
		return $this->mysqli->getQueryResultArray($result);
	}
	
	function getAllMiddle(){ // gets all elements which have a parent and are parent for somebody else
		$result = $this->mysqli->s_query("SELECT * FROM category ct WHERE ct.parent_id IS NOT NULL AND ct.id in (SELECT parent_id FROM category)  ORDER BY name asc",array(),array());
		return $this->mysqli->getQueryResultArray($result);
	}
	
	
	function getQuestionsByCategoryID($id){
		$result = $this->mysqli->s_query("SELECT * FROM question WHERE category_id=?",array('i'),array($id),true);
		return $this->mysqli->getQueryResultArray($result);
	}
	
	function getQuestionsByCategoryIDCount($id){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM question WHERE category_id=?",array('i'),array($id),true);
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}
	
	function getTotalQuestionCount(){
		$result = $this->mysqli->s_query("SELECT COUNT(1) FROM question",array(),array());
		return $this->mysqli->getSingleResult($result)["COUNT(1)"];
	}
	
	function fillCategoryListWithQuestionCount($categories){
		foreach ($categories as $key => $category)
		{	
			$count = 0;
			$count += $this->getQuestionsByCategoryIDCount($category['id']);
			$subCategories=$this->getChildren($category['id']);
	
			foreach ($subCategories as $keyInner => $categoryInner){
				$count += $this->getQuestionsByCategoryIDCount($categoryInner['id']);
				$subSubCategories=$this->getChildren($categoryInner['id']);
				
				foreach ($subSubCategories as $keyInnerInner => $categoryInnerInner){
					$count += $this->getQuestionsByCategoryIDCount($categoryInnerInner['id']);
				}
			}
			$categories[$key]['questioncount'] = $count;
		}
		return $categories;
	}
	


	
}
?>