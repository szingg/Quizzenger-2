<?php
$class="";
foreach ( $this->_ ['categories'] as $category ) {
	if ($this->_ ['container'] == "categories") { 
		$onclick = "onclick=\"showCategories(this," . $category ['id'] . ", 'subcategories')\"";
		if( isset($this->_ ['mode'])){
			$onclick = "onclick=\"setParentCategory('".$category ['id']."');showCategories(this," . $category ['id'] . ", 'subcategories', '". $this->_ ['mode'] ."' )\"";
		}
		$href = "javascript:void(0)";
	}else { // Subcategories
		if(isset($this->_['mode'])){
			if($this->_['mode'] == 'add_question'){
				$onclick = "onclick=\"chooseOnlyCategory('".$category ['id']."', this, '".$category['name']."');\"";
				$href = "javascript:void(0)";
			}
			else if($this->_['mode'] == 'generator'){
				$onclick = "onclick=\"addChosenCategory('".$category ['id']."', '".$category['name']."')\"";
				$href = "javascript:void(0)";
			}
		} else{
			$onclick = "";
			$href = "?view=questionlist&amp;category=" . $category['id'];
		}
	}
	if((isset($this->_['mode']) && $this->_['mode'] == 'add_question') || $category['questioncount']>0){ ?>
		<a href="<?php echo $href; ?>" <?= $onclick; ?> class="list-group-item"><?= htmlspecialchars($category['name']); ?>
			<span class="badge">
				<?php if(isset($category['questioncount'])){ echo($category['questioncount']);};?>
			</span>
		</a><?php
	}
}

// Create new Subcategory
if ($this->_ ['container'] != "categories" && isset($this->_['mode']) && $this->_['mode'] == 'add_question'){ ?>
	<a href="#" id="createNewCategoryInList" name="createNewCategoryInList" class="list-group-item" onclick="createNewCategoryInList(this);">
		Neuer Themenbereich
		<span class="badge">
			<span class="glyphicon glyphicon-plus"></span>
		</span>
		<input type="text" style="width:290px;" autofocus="" required="required" placeholder="Themenbereich Name" name="categorylist_ajax_new_category" id="categorylist_ajax_new_category" class="form-control">
	</a><?php 
}?>
