<div id="border-top" class="h_blue">
		<div>
			<div>
				<span class="title"><?php if (strlen($row_container['name']) < 50) { echo $row_container['name']; } else { echo substr_replace($row_container['name'],'...',50); } ?></span>
			</div>
		</div>
	</div>