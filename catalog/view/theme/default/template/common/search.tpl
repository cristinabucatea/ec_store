<div id="search" class="input-group">
  <span class="input-group-btn">
    <a onclick="btnSearchConfig()" class="btn btn-default btn-lg"><i class="fa fa-cog"></i></a>
  </span>
  <input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_search; ?>" class="form-control input-lg search-autocomplete" />
  <span class="input-group-btn">
    <button type="button" class="btn btn-default btn-lg"><i class="fa fa-search"></i></button>
  </span>
</div>
<div id="search-autocomplete-cog" class="search-autocomplete-cog">
	<div id="form-option" class="row">
		<span class="col-sm-3 padding-bottom-5px">
			<b>Category :</b>
		</span>
		<div class="col-sm-9 padding-bottom-5px">
			<select name="option-category" class="form-control">
				<option value="0"><?php echo $text_category; ?></option>
	            <?php foreach ($categories as $category_1) { ?>
	            <?php if ($category_1['category_id'] == $category_id) { ?>
	            <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
	            <?php } else { ?>
	            <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
	            <?php } ?>
	            <?php foreach ($category_1['children'] as $category_2) { ?>
	            <?php if ($category_2['category_id'] == $category_id) { ?>
	            <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
	            <?php } else { ?>
	            <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
	            <?php } ?>
	            <?php foreach ($category_2['children'] as $category_3) { ?>
	            <?php if ($category_3['category_id'] == $category_id) { ?>
	            <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
	            <?php } else { ?>
	            <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
	            <?php } ?>
	            <?php } ?>
	            <?php } ?>
	            <?php } ?>
			</select>
		</div>

		<span class="col-sm-3 padding-bottom-5px">
			<b>Sort : </b>
		</span>

		<div class="col-sm-9 padding-bottom-5px">
			<select name="option-sort" class="form-control">
				<option value="ASC">A-Z</option>
				<option value="DESC">Z-A</option>
			</select>
		</div>

		<div class="col-sm-3 padding-bottom-5px">
		</div>
		<div class="col-sm-9 padding-bottom-5px">
			<button name="btn-clear-option" class="btn btn-default">Clear</button>
			<button name="btn-apply-option" class="btn btn-primary">Apply</button>
		</div>
	</div>
</div>
<div id="result-search-autocomplete" class="result-search-autocomplete">
	<ul class="show-result">
	</ul>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script  type="text/javascript" >
	var $searchAutoConfig = $('.search-autocomplete-cog');
	var categoryId, sort, check_amount = false;
	function btnSearchConfig()
	{
		event.preventDefault();
		$searchAutoConfig.show();
		$('.result-search-autocomplete').css({"display":"none"});
		$('input[name=search]').val('');
	}
	// Reset option filter
	$('button[name=btn-clear-option]').on('click', function(){
		$('select[name=option-category] option').prop('selected', function() {
        	return this.defaultSelected;
    	});
    	$('select[name=option-sort] option').prop('selected', function() {
        	return this.defaultSelected;
    	});
    	categoryId = 0, sort = 'ASC';
    	check_amount = false;
    	$('input[name=check_amount]').prop( "checked", false );
	})
	// Apply filter
	$('button[name=btn-apply-option]').on('click', function(){
    	categoryId = $('select[name=option-category').val();
    	sort       = $('select[name=option-sort').val();
    	if($('input[name=check_amount]').prop("checked") == true){
            check_amount = true;
        }else
        {
        	check_amount = false;
        }
    	$searchAutoConfig.hide();
	})
	var width_search = document.getElementById("search").offsetWidth;
	$('.result-search-autocomplete').css({"width":width_search});
	
	$('.search-autocomplete').keyup(function(event) {
		/* Act on the event */
		$('.result-search-autocomplete  ul').css({"overflow" : "hidden"});
		var search = $('input[name=search]').val();
		$.ajax({
		  method: "GET",
		  url: "<?php echo $search_action; ?>",
		  data: { search : search, categoryId : categoryId, sort : sort, amount : amount, check_amount : check_amount }
		}).done(function( result ) {
			var html = '';
			if(result && search != '')
			{
				var count = 0
				$.each(JSON.parse(result), function( index, value ) {
				  	
				  	html += '<li>';
				  	html += '<a href="'+value.href.replace('amp;', '')+'">';
				  	html += '<div class="row">';
				  	html += '<div class="col-md-3 row-result-search-autocomplete-image">';
				  	html += '<img class="result-search-autocomplete-image" src="'+value.thumb+'">';
				  	html += '</div>';
				  	html += '<div class="col-md-6 result-info">';
				  	html += '<h4>'+value.name+'</h4>';
				  	if(value.special == false)
				  	{
				  		html += '<h5>'+value.price+' <i></i></h5>';
				  	}else{
				  		html += '<h5>'+value.special+' <i>'+value.price+'</i></h5>';
				  	}
				  	
				  	html += '</div>';
				  	//html += '<div style="text-align:right" class="col-md-3 result-button">';
				  	//html += '<button type="button" class="btn tagdattruoc"><?php echo $button_cart; ?></button>';
				  	//html += '<h6>Xem them</h6>';
				  	//html += '</div>';
				  	html += '</div>';
				  	html += '</a>';
				  	html += '</li>';
				  	count++;
				});
					$('.result-search-autocomplete').css({"display":"block"});
				  	if(count > 5)
					{
						$('.result-search-autocomplete  ul').css({"overflow" : "scroll"});
					}else{
						$('.result-search-autocomplete  ul').css({"overflow" : "hidden"});
					}
			}else{
				html = '';
				$('.result-search-autocomplete').css({"display":"none"});
			}

			$('.show-result').html(html);
		});
	});
</script>
<style type="text/css" media="screen">
#form-option
{
	padding: 10px;
}
.padding-bottom-5px
{
	padding-bottom: 5px;
}
.result-search-autocomplete, .search-autocomplete-cog
{
	display: none;
	position: absolute;
	z-index: 1000;
	background-color: #FFF;
	border: 1px solid #ddd;
	top:40px;
	max-height:468px;
}
.search-autocomplete-cog
{
	height: 180px;
	width: 94%;
}
.result-search-autocomplete h4
{
	  display: block;
	  width: 72%;
	  line-height: 1.3em;
	  color: #333;
	  font-size: 14px;
	  font-weight: 700;
	  overflow: hidden;
	  text-overflow: ellipsis;
	  white-space: nowrap;
}
.result-search-autocomplete h5
{
	font-size: 14px;
    margin-top: 8px;
    color: red;
}
.result-search-autocomplete h5 i
{
	color: #999;
	font-style: normal;
	font-size: 11px;
	text-decoration: line-through;
}
.result-search-autocomplete h6
{
	text-transform: uppercase;
  	font-size: 9px;
  	font-weight: 700;
  	color: #0876e6;
  	display: block;
  	margin-top: 8px;
  	text-align: right;
}
.result-search-autocomplete ul, li
{
	list-style-type: none;
	margin: 0;
	padding: 0;
}
.result-search-autocomplete-image
{
	height: 50px;
	padding-left: 15px;
}
.result-search-autocomplete > ul
{
	max-height:468px;
	overflow: hidden;
	/*overflow: scroll;
	overflow-x:none;*/
}
.result-search-autocomplete > ul >li >a
{
	position: relative;
  	display: block;
  	overflow: hidden;
  	padding: 6px;
  	text-decoration: none;
}
.result-search-autocomplete > ul >li 
{
	display: block;
  	background: #fff;
  	overflow: hidden;
  	list-style: none;
  	border-bottom: 1px dotted #ccc;
  	float: none;
}
.result-search-autocomplete > ul >li > a:hover button
{
	color: #FFF;
}
.tagdattruoc {
  background: #3498db;
  border: 1px solid #0679c6;
  font-size: 11px;
  color: #fff;
  border-radius: 0px;
  margin-top: 18px;
}
.tagdattruoc :hover
{
	color: #FFF;
}
@media (max-width: 767px) {
		.result-button {
			width: 30%;
			float: left;
		}
		.row-result-search-autocomplete-image
		{
			width: 30%;
			float: left;
		}
		.result-info
		{
			width: 40%;
			float: left;
		}
	}

</style>