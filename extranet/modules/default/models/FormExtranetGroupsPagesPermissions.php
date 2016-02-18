<?php
  
?>
<form method="post">
    <?php echo $this->getView()->getCibleText('label_gestion_structure'); ?>
    <ul>
        <li>
            <input type="checkbox" id="checkbox_structure_0" name="checkbox_structure_0" onclick="checkChild('structure_0')">
            <a id="parent_structure_0_open" href="#" onclick="showChild('structure_0')">www.ciblesolutions.com&nbsp;+</a>
            <a id="parent_structure_0_close" href="#" onclick="hideChild('structure_0')" style="display:none;">www.ciblesolutions.com&nbsp;-</a>
    <?php   $this->checkBoxChildConstruct($administratorGroupID, $pagesArray, 0, 'structure'); ?>         
        </li>
    </ul>
    
   <?php echo $this->getView()->getCibleText('label_gestion_contenu'); ?>
    <ul>
        <li>
            <input type="checkbox" id="checkbox_data_0" name="checkbox_data_0" onclick="checkChild('data_0')">
            <a id="parent_data_0_open" href="#" onclick="showChild('data_0')">www.ciblesolutions.com&nbsp;+</a>
            <a id="parent_data_0_close" href="#" onclick="hideChild('data_0')" style="display:none;">www.ciblesolutions.com&nbsp;-</a>
    <?php   $this->checkBoxChildConstruct($administratorGroupID, $pagesArray, 0, 'data'); ?>    
        </li>
    </ul>
    
    <input type="submit" name="sauvegarder" value="Sauvegarder">
    <input type="button" value="Annuler" onclick="window.open('<?php echo($returnLink); ?>', '_self')">
</form>
