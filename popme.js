jQuery(function($) {
	$(document).ready(function(){
		$(document).on('click', '.person_card_insert', function () {
            tb_show("Insert Staff Member", "#TB_inline?inlineId=insert_cranleigh_person", "");
        });
	});
});
