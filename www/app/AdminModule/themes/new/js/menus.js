$(document).ready(function(){
   $('#frmformNewMenuItem-link_type-0').live('click', function(){
    $('#frmformNewMenuItem-internal_url').show();
    $('#frmformNewMenuItem-external_url').hide();    
   });
   $('#frmformNewMenuItem-link_type-1').live('click', function(){
    $('#frmformNewMenuItem-internal_url').hide();
    $('#frmformNewMenuItem-external_url').show();    
   });
});