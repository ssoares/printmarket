function addRange(objectClass, baseUrl){
    
    nextId = $('.' + objectClass + '_from').size();
    
    newObject = '<li id="'+objectClass+'_'+nextId+'"><input type="text" name="'+objectClass+'['+nextId+'][from]" id="'+objectClass+'_'+nextId+'_from" value="" class="'+objectClass+'_from"> @ <input type="text" name="'+objectClass+'['+nextId+'][to]" id="'+objectClass+'_'+nextId+'_to" value="" class="'+objectClass+'_to">&nbsp;&nbsp;<img class="action_icon" alt="Supprimer" src="'+baseUrl+'/icons/del_icon_16x16.png" onclick="removeRange(\''+objectClass+'_'+nextId+'\');" align="absMiddle" /></li>';
    
    $('#'+objectClass+'_placeholder ul#'+objectClass+'_ranges').append(newObject);
    $('#'+objectClass+'_'+nextId+'_from').datepicker();
    $('#'+objectClass+'_'+nextId+'_to').datepicker();
    $('#'+objectClass+'_'+nextId+'_from').mask('9999-99-99');
    $('#'+objectClass+'_'+nextId+'_to').mask('9999-99-99');
}

function removeRange(range)
{
    if( confirm('Êtes vous sûr de vous vouloir supprimer cette plage?') )
        $('li#' + range).remove();
}