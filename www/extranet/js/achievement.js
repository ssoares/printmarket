function loadAchievementTypeList($link, $value, $elementToUpdate)
{
    if ($value > 0){
        $.getJSON($link,
        function(data){
          removeAllOptions($elementToUpdate);
          $list = document.getElementById($elementToUpdate);
          
          if ($elementToUpdate == 'AchievementCategory'){
            option = document.createElement('option');
            option.appendChild(document.createTextNode('-- Choisissez un type de réalisation --'));
            option.setAttribute('value', 0);
            $list.appendChild(option);
            
            removeAllOptions('AD_CategoryID'); 
            $list3 = document.getElementById('AD_CategoryID');
            option = document.createElement('option');
            option.appendChild(document.createTextNode('-- Choisissez un type de réalisation --'));
            option.setAttribute('value', 0);
            $list3.appendChild(option);        
            
          }
          else{
            option = document.createElement('option');
            option.appendChild(document.createTextNode('-- Choisissez un sous-type de réalisation --'));
            option.setAttribute('value', 0);
            $list.appendChild(option);    
          }
          
          $.each(data, function(i,item){
            option = document.createElement('option');
            option.appendChild(document.createTextNode(item["C_Title"]));
            option.setAttribute('value', item["C_ID"]);
            
            $list.appendChild(option);
            
          });
        });    
    }
    else{
        if($elementToUpdate == 'AchievementCategory'){
            option = document.createElement('option');
            option.appendChild(document.createTextNode('-- Choisissez une division --'));
            option.setAttribute('value', 0);
            
            option2 = document.createElement('option');
            option2.appendChild(document.createTextNode('-- Choisissez une division --'));
            option2.setAttribute('value', 0);
            
            removeAllOptions('AchievementCategory');
            $list = document.getElementById('AchievementCategory');
            $list.appendChild(option);  
            
            removeAllOptions('AD_CategoryID');
            $list2 = document.getElementById('AD_CategoryID');
            $list2.appendChild(option2);
        }
        else{
            option = document.createElement('option');
            option.appendChild(document.createTextNode('-- Choisissez un type de réalisation --'));
            option.setAttribute('value', 0);                
            
            removeAllOptions('AD_CategoryID');
            $list = document.getElementById('AD_CategoryID');
            $list.appendChild(option);        
        }    
    }
    
}

function removeAllOptions($elementToUpdate)
{
  var elSel = document.getElementById($elementToUpdate);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
      elSel.remove(i);
  }
}

