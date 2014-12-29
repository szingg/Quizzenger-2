<?php
	if(isset($this->_ ['questionhistory'])){
		if(empty($this->_ ['questionhistory'])){
			echo("Noch keine Daten vorhanden");
		}
		foreach ( $this->_ ['questionhistory'] as $history ){
			echo($history['timestamp']." - ".$history['action']." durch ".$history['username']."<br>");
		}
	}elseif(isset($this->_['questionhistoryByUser'])){
		foreach ( $this->_ ['questionhistoryByUser'] as $history ){
			$qtxt = $history['questiontext'];
			echo($history['timestamp'].' - '.$history['action']." durch ".$history['username'].'<br><a href="index.php?view=question&id='.$history['question_id'].'">'.((strlen($qtxt) > 40) ? substr($qtxt,0,40)."..." : $qtxt)	.'</a><br><br>');
		}
	}else{
		echo("Fehlende Daten");
	}
?>
