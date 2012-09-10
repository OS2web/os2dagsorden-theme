function bullet_point_add_expand_behaviour(){
   jQuery(document).ready(function() {   
	jQuery(".bullet-point-attachments .view-content .item-list .ul-item-list-dagsordenspunkt").each(function(index) {
	  jQuery(this).attr("id","attachments_container_"+index);
	  jQuery(this).hide();
	  
	  jQuery(this).parent().parent().parent().children(".hide_show_button_container").append("<input type='button' class='button' id='btn_hide_show_attachments_"+index+"' value='⇓'></a>");
	  jQuery("#btn_hide_show_attachments_"+index).click(function(){
 	    jQuery("#attachments_container_"+index).toggle();
	    
	    if (jQuery("#btn_hide_show_attachments_"+index).val() == "⇓")
		jQuery("#btn_hide_show_attachments_"+index).val("⇑");
	    else
		jQuery("#btn_hide_show_attachments_"+index).val("⇓");
 	  });
	  
	  attachment_add_expand_all_behaviour(this, index);  
	  attachment_add_expand_behaviour(this,index);
	});
   });
}

//is used on Dagsorden details view
function bullet_point_details_init(){
  jQuery(document).ready(function() {   
    jQuery(".item-list-dagsordenspunkt .ul-item-list-dagsordenspunkt").each(function(index) {
	attachment_add_expand_all_behaviour(this, index);  
	attachment_add_expand_behaviour(this, index);
    });
  });
}


function attachment_add_expand_all_behaviour(bulletPoint, bulletPointIndex){
  jQuery(bulletPoint).prepend("<input type='button' class='button hide_show_all_attachments_text' id='btn_hide_show_all_attachments_text_"+bulletPointIndex+"' value='⇊'></a>");
  jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).click(function(){
    if (jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).val() == "⇊"){
	jQuery("[id^=attachment_text_container_"+bulletPointIndex+"_]").show();
	jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).val("⇈");
	
	//handle single expand
	jQuery("[id^=btn_hide_show_attachment_text_"+bulletPointIndex+"_]").val("⇑");
    } else {
	jQuery("[id^=attachment_text_container_"+bulletPointIndex+"_]").hide();
	jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).val("⇊");
	
	//handle single expand
	jQuery("[id^=btn_hide_show_attachment_text_"+bulletPointIndex+"_]").val("⇓");
    }
  });
}

function attachment_add_expand_behaviour(bulletPoint, bulletPointIndex){
  jQuery(bulletPoint).children("li").children(".attachment_text_container").each(function(index_attachment){
    jQuery(this).attr("id","attachment_text_container_"+bulletPointIndex+"_"+index_attachment);
    jQuery(this).hide();

    jQuery(this).parent().prepend("<input type='button' class='button hide_show_attachment_text' id='btn_hide_show_attachment_text_"+bulletPointIndex+"_"+index_attachment+"' value='⇓'></a>");
    jQuery("#btn_hide_show_attachment_text_"+bulletPointIndex+"_"+index_attachment).click(function(){
      jQuery("#attachment_text_container_"+bulletPointIndex+"_"+index_attachment).toggle();
      
      if (jQuery("#btn_hide_show_attachment_text_"+bulletPointIndex+"_"+index_attachment).val() == "⇓")
	jQuery("#btn_hide_show_attachment_text_"+bulletPointIndex+"_"+index_attachment).val("⇑");
      else
	jQuery("#btn_hide_show_attachment_text_"+bulletPointIndex+"_"+index_attachment).val("⇓");
      
      //handle expand all
      if (jQuery("[id^=btn_hide_show_attachment_text_"+bulletPointIndex+"_][value='⇓']").length > 0)
	jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).val("⇊");
      else
	jQuery("#btn_hide_show_all_attachments_text_"+bulletPointIndex).val("⇈");
    });
  });	
}

function bullet_point_attachment_add_notes_indicator(ids){
  jQuery(document).ready(function() {
	jQuery(".indicator-has-no-notes").each(function(){
	  if (ids.indexOf(parseInt(this.id)) != -1){
	    jQuery(this).attr("class","indicator-has-notes");
	  }
	});
   });
}