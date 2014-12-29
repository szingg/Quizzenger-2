<div class="row" style="margin:0px;">
	<div id="themes" class="col-md-4 list-group">
	<?php foreach ( $this->_ ['roots'] as $root ) { 
		if((isset($this->_['mode']) && $this->_['mode'] == 'add_question') || $root['questioncount']>0){?>		
		<a href="javascript:void(0)" onclick="showCategories(this, <?php echo $root['id'] ?>, 'categories' <?php
				if(isset($this->_['mode'])){
					echo ", '". $this->_['mode'] ."'";
				}
				?>)" class="list-group-item">
				<?php echo $root['name']; ?> 
			<span class="badge"><?php if(isset($root['questioncount'])){ echo($root['questioncount']);};?></span>
		</a>
	<?php 
		}
	}
	?> 
	</div>
	<div id="categories" class="col-md-4 list-group">
	</div>
	<div id="subcategories" class="col-md-4 list-group">
	</div>
</div>
