
	<div class="searchBox">
	<div class="searchBoxLeft">
	<img src="images/semi-left.gif">
	</div>
	<input name="search" type="text" class="searchFormField" id="search" size="20" maxlength="255" autocomplete="off" onkeyup="if (this.value == '') { document.getElementById('searchQ').style.display='none'; } else { document.getElementById('searchQ').style.display='block'; searchQry(this.value); }">
	<div class="searchBoxCancel">
	<a href="#" title="Cancel search" onclick="document.getElementById('search').value = ''; document.getElementById('searchQ').style.display = 'none';"><img src="images/cancel.gif" border="0" alt="Cancel search"></a>
  </div>
	<div class="searchBoxRight">
	<img src="images/semi-right.gif">
	</div><br /><br />
	<div id="searchQ" class="searchQ"><img src="images/spinningwheel.gif" alt="Please wait..." width="30" height="30" align="absmiddle"> searching...</div>
    <input type="hidden" value="search" name="browse" />
    <input type="hidden" value="<?php echo $_GET['container']; ?>" name="container" />
    </div>
    <br />