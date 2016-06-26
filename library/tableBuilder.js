/**
 * Created by nick on 4/23/16.
 */

function errorPage(data){
    $("#title").append("<H2>" + data.error + "</H2>");
    // if(data.propId === null || data.propid == 0){
    //     $("#title").append("<H2>" + data.error + "</H2>");
    // } else {
    //     $("#title").append("<H2>No Comparable Hits found for " + data.propId + "</H2>");
    // }
}

function addTitle(){
    var tableType = getUrlParameter("Submit");

    if(tableType != undefined) {
        if (tableType === "Search"){
            $("#title").append("Property details");
            return;
        } else if (tableType === "Sales"){
            tableType = "Sales";
        } else if (getUrlParameter("style") === "sales"){
            tableType = "Sales";
        }
    } else {
        //default to equity
        tableType = "Equity";
    }

    $("#title").append("Comp "+ tableType + " Grid - Five Stone - " + new Date($.now()));
};

function drawTable(data) {
    var totalCol = data.compCount + 2; // one for desc column and one for subject
    var totalComps = data.compCount;
    var maxCompCol = 4;
    //Commented out portion is to handle max table lengths and page wraps

//            if(totalComps < maxCompCol) {
    var tableId = insertTable(1);
    drawHeader(tableId, data.compCount);
    for (var i = 0; i < data.rows.length; i++) {
        drawRow(tableId, data.rows[i], totalCol);
    }
//            } else {
//                var currComp = 1;
//                for(var i = 0; i < (totalComps % maxCompCol); i++){
//                    var tableId = insertTable(i);
//                    drawHeader(tableId, data.compCount, currComp, currComp*maxCompCol);
//                    for (var j = 0; j < data.rows.length; j++) {
//                        drawRow(tableId, data.rows[j], totalCol);
//                    }
//                    insertPage();
//                    currComp += maxCompCol;
//                }
//            }
};

function insertPage(){
    $('body').append(document.createElement('p'));
}

function insertTable(tableNum){
    var table = document.createElement('table');
    table.id = "subjCompTable"+tableNum;
    $('body').append(table);
    return '#'+table.id;
}

function drawHeader(tableId, comps, start, end){
    var start = typeof start !== 'undefined' ?  start : 1;
    var end = typeof end !== 'undefined' ?  end : comps;
    var row = $("<tr />")
    $(tableId).append(row); //this will append tr element to table... keep its reference for a while since we will add cells into it
    row.append($("<th></th>")); // cell 0,0 empty
    row.append($("<th class='colhead'> Subject <div id='subject'/></th>"));
    for(var i=start; i <= end; i++){ // Start count naturally
        row.append($("<th class='colhead'> Comp #" + i + "</th>"));
    }
};

function drawRow(tableId, rowData){
    var row = $("<tr />")
    $(tableId).append(row); //this will append tr element to table... keep its reference for a while since we will add cels into it
    var rowClass;
    $.each(rowData, function(key, value) {
        if(key === 'description'){
            if(value) {
                rowClass = value.replace(/ /g, '');
                row.append($("<th>" + value + "</th>"));
            } else {
                row.append($("<td class='unknown'>&nbsp</td>"));
            }
        } else if(value && typeof value == 'object'){
            //We have a multi value cell
            var cell = "<td>";
            if(value.value !== null) {
                cell += "<div class='value'>" + value.value + "</div>";
            }
            if(value.subvalue && value.subvalue !== null) {
                cell += "<div class='subvalue'>" + value.subvalue + "</div>";
            }
            if(value.delta !== null) {
                if(key !== 'col1') { // Don't display deltas in subject column
                    cell += "<div class='delta'>" + value.delta + "</div>";
                }
            }
            cell += "</td>";
            row.append($(cell));
        } else {
            if(value !== null) {
                if( rowClass === 'PropertyID'){
                    row.append($("<td class='" + rowClass + "'>" + value + "<button id='removeMe' onclick='removeMe("+value+")'/></td>"));
                } else {
                    row.append($("<td class='" + rowClass + "'>" + value + "</td>"));
                }
            } else {
                row.append($("<td/>"));
            }
        }
    });
};