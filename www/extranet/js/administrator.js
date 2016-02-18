function showChild($divID)
{
    document.getElementById('group_'+$divID).style.display = '';
    document.getElementById('parent_'+$divID+'_open').style.display = 'none';
    document.getElementById('parent_'+$divID+'_close').style.display = 'inline';
}

function hideChild($divID)
{
    document.getElementById('group_'+$divID).style.display = 'none';
    document.getElementById('parent_'+$divID+'_close').style.display = 'none';
    document.getElementById('parent_'+$divID+'_open').style.display = 'inline';
}

function checkChild($divID, $action)
{
    $group = document.getElementById('group_'+$divID)
    if($action == 'tick'){
        answer = confirm("Désirez-vous cocher toutes les sous-sections de cette page?");
        if (answer !=0){
            $inputObject = $group.getElementsByTagName("input");
            for ($i=0; $i<$inputObject.length; $i++){
                $inputObject[$i].checked = 'checked';
            }
        }
    }
    else if($action == 'untick'){
        answer = confirm("Désirez-vous décocher toutes les sous-sections de cette page?");
        if (answer !=0){
            $inputObject = $group.getElementsByTagName("input");
            for ($i=0; $i<$inputObject.length; $i++){
                $inputObject[$i].checked = '';
            }
        }
    }
}

