$(document).ready(function() {
    $("#addAssociation").live('click',function(event){
        event.preventDefault();
        var associationSetContent = $(this).parents('.associationSetContent');
        var associationSetID = associationSetContent.attr('associationSetID');

        var associationContent   = associationSetContent.children('.associationContent');
        var associationTable     = associationContent.children('table');

        var associationCountID   = parseInt(associationContent.children('#associationCountID').val());
        var associationAction    = "new";

        var url = $("#ajaxLink").val();

        $.getJSON(url,{associationAction : associationAction, associationID : (associationCountID+1), associationSetID : associationSetID},
            function(data){
                var row = '';
                if ((associationCountID+1)%2)
                    row = 'even';
                else
                    row = 'odd';

                newAssociation   = ""
                newAssociation += "<tr class='association' associationID='"+(associationCountID+1)+"'>";
                newAssociation +=    data['newElement'];
                newAssociation += "  <td class='row_"+row+"'>";
                newAssociation += "      <div class='action'>";
                newAssociation += "          <fieldset id='fieldset-actions-association'>";
                newAssociation += "              <ul class='actions-buttons'>";
                //newAssociation += "                  <li><button name='deleteAssociation' id='deleteAssociation' type='button' class='stdButton deleteAssociation'>"+$('#linkDeleteAssociation').val()+"</button></li>";
                newAssociation += "                  <li><button name='deleteAssociation' id='deleteAssociation' type='button' class='stdButton delAssociation'>Supprimer</button></li>";
                newAssociation += "              </ul>";
                newAssociation += "          </fieldset>";
                newAssociation += "      </div>";
                newAssociation += "  </td>";
                newAssociation += "</tr>";

                associationTable.append(newAssociation);
                associationContent.children('#associationCountID').val(associationCountID+1);
                associationContent.children('#associationCount').val(parseInt(associationContent.children('#associationCount').val())+1);

            }
        );
    });

    $("#addAssociationSeq").live('click',function(event){
        event.preventDefault();
        var associationSetContent = $(this).parents('.associationSetContent');
        var associationSetID = associationSetContent.attr('associationSetID');

        var associationContent   = associationSetContent.children('.associationContent');
        var associationTable     = associationContent.children('table');

        var associationCountID   = parseInt(associationContent.children('#associationCountID').val());
        var associationAction    = "new";

        var url = $("#ajaxLink").val();

        $.getJSON(url,{associationAction : associationAction, associationID : (associationCountID+1), associationSetID : associationSetID},
            function(data){
                var row = '';
                if ((associationCountID+1)%2)
                    row = 'even';
                else
                    row = 'odd';

                var newAssociation   = ""
                newAssociation += "<tr class='association' associationID='"+(associationCountID+1)+"'>";
                newAssociation += "  <td class='entities-cell row_"+row+"'>";
                newAssociation +=        data['newElement'];
                newAssociation += "  </td>";
                newAssociation += "  <td class='row_"+row+"'>&nbsp;</td>";
                newAssociation += "  <td class='row_"+row+"'><strong>Sequence : </strong><input type='text' name='"+associationSetID+"Set["+(associationCountID+1)+"][seq]' class='shortTextInput row_"+row+"' value=''/></td>";
                newAssociation += "  <td class='tdAssociationAction row_"+row+"'>";
                newAssociation += "      <div class='action'>";
                newAssociation += "          <fieldset id='fieldset-actions-association'>";
                newAssociation += "              <ul class='actions-buttons'>";
                //newAssociation += "                  <li><button name='deleteAssociation' id='deleteAssociation' type='button' class='stdButton deleteAssociation'>"+$('#linkDeleteAssociation').val()+"</button></li>";
                newAssociation += "                  <li><button name='deleteAssociation' id='deleteAssociation' type='button' class='stdButton delAssociation'>Supprimer</button></li>";
                newAssociation += "              </ul>";
                newAssociation += "          </fieldset>";
                newAssociation += "      </div>";
                newAssociation += "  </td>";
                newAssociation += "</tr>";

                associationTable.append(newAssociation);


                associationContent.children('#associationCountID').val(associationCountID+1);
                associationContent.children('#associationCount').val(parseInt(associationContent.children('#associationCount').val())+1);

            }
        );
    });

    $(".delAssociation").live('click',function(event){
        event.preventDefault();
        var associationSetContent = $(this).parents('.associationSetContent');
        var associationContent   = associationSetContent.children('.associationContent');
        var associationSetID = associationSetContent.attr('associationSetID');
        var association      = $(this).parents('.association');
        var associationID    = association.attr('associationID');

        associationContent.children('#associationCountID').val(associationContent.children('#associationCountID').val()-1);
        associationContent.children('#associationCount').val(associationContent.children('#associationCount').val()-1);

        association.remove();

        var rows = associationContent.children('table').children('tbody').children('tr');
        var cells = '';
        var rowClass = 'even';
        rows.each(function(index){
            cells = $(this).children('td');
            cells.each(function(index2){
                $(this).removeClass('row_odd row_even');
                $(this).addClass('row_'+rowClass);
            });
            if(rowClass == 'even')
                rowClass = 'odd';
            else
                rowClass = 'even';
        });
    });
});
