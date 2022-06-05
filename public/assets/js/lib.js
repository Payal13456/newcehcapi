String.prototype.toProperCase = function () {
    return this.replace(/\w\S*/g, function (txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
};
 //var $j = jQuery.noConflict();
iconLoaderClass = "<i class='fa fa-spinner fa-spin requestLoader marged-left-5 marged-top_5 fa-2x'></i>";
iconLoaderClass2 = "<i class='fa fa-spinner fa-spin requestLoader  marged-left-5 '></i>";
loaderStr = "<i class='fa fa-spinner submitLoader marged-left-10 fa-spin' style='font-size:50px;'></i>";
var timeout;
var modal_commonVar;
errorType = 1;
isRequest = false;
inline_edit_flag = false;
inline_edit_value = "";
maxHeight = 0;
focusIndex = 0;
displaySingleTime = false;
isInlineRemove = false;
inline_error_flag = false;
inline_error_msg_arr = new Array();
var monthArray = new Array();
monthArray["Jan"] = 0;
monthArray["Feb"] = 1;
monthArray["Mar"] = 2;
monthArray["Apr"] = 3;
monthArray["May"] = 4;
monthArray["Jun"] = 5;
monthArray["Jul"] = 6;
monthArray["Aug"] = 7;
monthArray["Sep"] = 8;
monthArray["Oct"] = 9;
monthArray["Nov"] = 10;
monthArray["Dec"] = 11;
errorType = 1;
var today = new Date();
currentMonth = today.getMonth() + 1;
currentYear = today.getFullYear();
datedMonth = currentMonth;
datedYear = currentYear;
datatables = new Array();
datatablesConfig = new Array()
 var datatables = new Array();
function noFormSubmit() {
    formSubmit = false;
    close_popup('form_confirm_message');
}
function SIM(div, msg, className, close)
{
    if (className == 'alert alert-danger' && div != "popupmessage") {
       $("#error_element_title").html("Error");
        $("#error_element_message").html(msg);
        $("#btnErrorMessage").removeClass("btn-success").addClass("btn-danger");
        open_popup(320, 300, "error_message");
        $("#btnErrorMessage").focus();
    } else if ($("." + div).length >= 0) {
         $("#error_element_title").html("Success");
        $("#error_element_message").html(msg);
        if(close !="undefined" || close !=NULL){
            $('#btnErrorMessage').attr("data-redirect",close);
        }
        $("#btnErrorMessage").removeClass("btn-danger").addClass("btn-success");
        open_popup(320, 300, "error_message");
        $("#btnErrorMessage").focus();
    } else if ($("." + div).length > 0) {
       $("#error_element_title").html("Success");
            $("#error_element_message").html(msg);
             if(close !="undefined" || close !=NULL){
                 $('#btnErrorMessage').attr("data-redirect",close);
            }
       $("#btnErrorMessage").removeClass("btn-danger").addClass("btn-success");
       open_popup(320, 300, "error_message");
       $("#btnErrorMessage").focus();

    }
}
function remove_SIM(div) {
    $("." + div).children("div").removeAttr("class").html("");
}
function spanWrap(tc) {
    return " <span>" + tc + "</span>";
}
function disabledBtn(eObj) {
    eObj.attr("disabled", "disabled");
    eObj.after(iconLoaderClass2);
}
function unfreez(eObj) {
    eObj.removeAttr("disabled");
}
function freez(eObj) {
    eObj.attr("disabled", "disabled");
    eObj.after(iconLoaderClass2);
}
function hideAppHover(id) {
    $(id).hide();
}
function open_popup(DW, DH, div_name)
{
   windowWidth = $(window).width();
    windowHeight = $(window).height();
    DT = (windowHeight - DH) / 2;
    DL = (windowWidth - DW) / 2;
    $(".modal_" + div_name + "_face").modal("show");
}
function static_open_popup(DW, DH, div_name)
{
    windowWidth = $(window).width();
    windowHeight = $(window).height();
    DT = (windowHeight - DH) / 2;
    DL = (windowWidth - DW) / 2;
    $(".modal_" + div_name + "_back").show();
    $(".modal_" + div_name + "_face").css({'width': DW, 'height': DH, 'top': DT, 'left': DL}).show();
}
function close_popup(div_name)
{
    var redirect = $(".modal_" + div_name + "_face").find("#btnErrorMessage").attr("data-redirect");
    if ($(".modal_" + div_name + "_face").length) {
        visitDelete = false;
        $(".modal_" + div_name + "_face").modal("hide");
        if(typeof redirect !="undefined"){
              window.location.href = redirect;
              return false;
        }
    }

}
function hideRemoveIcon(eObj) {
    eObj.find(".remvoeIcon").hide();
}
function showRemoveIcon(eObj) {
    eObj.find(".remvoeIcon").show();
}
function showIconsButton(eObj) {
    eObj.children(".tdIconsTab").children("i").css("visibility", "visible");
    showDeleteButton(eObj);
}
function open_static_popup(obj) {
    popupDiv = $('#' + obj.attr("data-div"));
    popupForm = popupDiv.find("form");
    remove_SIM("msg_" + popupForm.attr("id"));
    $('#' + obj.attr("data-div")).find($("[data-norest='true']")).each(function () {
        $(this).attr("data-old-val", $(this).val());
    });
    popupForm[0].reset();
    $('#' + obj.attr("data-div")).find($("[data-norest='true']")).each(function () {
        if ($(this).is("select")) {
            $(this).select2("val", $(this).attr("data-old-val"));
        } else {
            $(this).val($(this).attr("data-old-val"));
        }
    });
    popupDiv.show();
    $("#" + obj.attr("data-id")).val(obj.attr("data-id-value"));
    $('#' + obj.attr("data-div")).find($("[data-table^='data-table-']")).each(function () {
        if ($(this).is("div")) {
            //          if(tdExist) {
            $(this).html(obj.attr($(this).attr("data-table")));
//        }
        } else if ($(this).is("select")) {
            tVal = obj.attr($(this).attr("data-table"));
            selectObj = $(this);
            selectObj.children("option").each(function () {
                if ($.trim($(this).html()) == tVal) {
                    selectObj.select2("val", $(this).val());
                }
            });
        } else {
            $(this).val(obj.attr($(this).attr("data-table")));
        }
    });
    popupDiv.find(".modal").modal();
}
function open_extra_popup(obj) {
    popupDiv = $('#' + obj.attr("data-div"));
    trObj = obj.parent().parent();
    tdExist = true;
    if (!trObj.is("tr")) {
        trObj = obj.parent();
        tdExist = false;
    }
    popupForm = popupDiv.find("form");
    $('#' + obj.attr("data-div")).find($("[data-norest='true']")).each(function () {
        $(this).attr("data-old-val", $(this).val());
    });
    popupForm[0].reset();
    $('#' + obj.attr("data-div")).find($("[data-norest='true']")).each(function () {
        if ($(this).is("select")) {
            $(this).select2("val", $(this).attr("data-old-val"));
        } else {
            $(this).val($(this).attr("data-old-val"));
        }
    });
 //   popupDiv.show();
  popupDiv.modal("show");
   $('#' + obj.attr("data-div")).find($("[data-table^='data-table-']")).each(function () {

        if ($(this).is("div")) {

            //          if(tdExist) {
            $(this).html(trObj.children("td").eq(obj.attr($(this).attr("data-table"))).html());
//        }
        } else if ($(this).is("select")) {
            tVal = trObj.children("td").eq(obj.attr($(this).attr("data-table"))).html();
            selectObj = $(this);
            selectObj.children("option").each(function () {
                if ($.trim($(this).html()) == tVal) {
                    selectObj.select2("val", $(this).val());
                }
            });
        } else if ($(this).is("input[type=radio]")) {
            tVal = trObj.children("td").eq(obj.attr($(this).attr("data-table"))).html();
            radioName = $.trim($(this).parent().children("span.radioName").html());
            if (radioName == tVal) {
                $(this).parent().click();
            }
        } else {
            $(this).val(trObj.children("td").eq(obj.attr($(this).attr("data-table"))).clone().children().remove().end().html());
        }
    });
     popupDiv.find(".modal").modal();
}
function openPrintDiet(prtContent) {
    hrefInNewTab(prtContent.attr("data-url"));
}
function test_for_blank(a) {
    if (a.val() == "") {
        a.css("border-color", "red");
        return false
    } else {
        a.css("border-color", INPUT_BORDER_COLOR);
        return true
    }
}
function addMeFocus(eObj) {
    eObj.parent().addClass("focusedDiv");
}
function removeMeFocus(eObj) {
    eObj.parent().removeClass("focusedDiv");
}
function NoRecord() {
    window.alert("No Record Found.....");
}
function redirectMe(url) {
    window.location.href = url;
}
formSubmit = false;
currentFormObjActive = null;
function confirmFormSubmit(e) {
    if (!formSubmit) {
        currentFormObjActive = e;
        formConfirmation(e);
        formSubmit = true;
        return false;
    }
    close_popup('form_confirm_message');
    return true;
}
function noFormSubmit() {
    formSubmit = false;
    close_popup('form_confirm_message');
}
function renderAjax(response, e, freset) {
    $(".requestLoader").remove();
    if (e != null) {
        e.find("[data-submit=submit]").removeAttr("disabled");
    }
    $(".submitLoader").remove();                
    if (response['status'] == 1)
    {
        if (freset && typeof response['noreset'] == "undefined") {
            if (e != null) {
               e[0].reset();
            }
        }
        if (e != null) {
            if (e.attr("data-form-type") == "popup")
                $("#myModal_body").html(response['msg']);
        }
    }
     if (typeof response['script'] != "undefined" && response['script'] != "") {
            eval(response['script']);
    }
    if (typeof response['error'] != "undefined" && response['error'] != "") {
         $.each(response['error'], function(key, value ) {
            console.log(key);
           $('.'+key+'_err').text(value);
           $('.'+key+'_err').css('display','block');
        });
    }
    if (response['validate_error'] == false && typeof response['msgCommon'] != "undefined" && response['msgCommon'] != "") {
        SIM("msg_topCommon", response['msgCommon'], response['class']);
        if (typeof response['timeout'] != "undefined" && response['timeout'] != "") {
            if (response['class'] != ERROR_CLASS) {
                setTimeout(function () {
                    close_popup('error_message');
                }, 1500);
            }
        }

    }
    // if (response['datatable'] != "undefined" && response['datatable'] != ""){
    //      if(response['datatableID'] == 1){
    //            datatables = $("." + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //      }else{
    //           datatables = $("#" + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //     }
    // }
    // if (response['datatableSecond'] != "undefined" && response['datatableSecond'] != "") {
    //    if(response['datatableID'] == 1){
    //            datatables = $("." + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //      }else{
    //           datatables = $("#" + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //     }
    // }
    // if (response['datatableThird'] != "undefined" && response['datatableThird'] != "") {
    //      if(response['datatableID'] == 1){
    //           datatables = $("." + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //      }else{
    //           datatables = $("#" + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //     }
    // }
    // if (response['datatableFore'] != "undefined" && response['datatableFore'] != "") {
    //      if(response['datatableID'] == 1){
    //           datatables = $("." + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15,'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //      }else{
    //           datatables = $("#" + response['datatable']).dataTable({"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true});
    //     }
    // }
    if (typeof response['redirect'] != "undefined") {
        window.location.href = response['redirect'];
        return false;
    }
    if (typeof response['popup_msg'] != "undefined" && response['popup_msg'] != "") {
        $("#myModalLabel").html(response["title"]);
        $("#myModalBody").html(response["popup_msg"]);
        $('#myModal').modal();
    }
    loadJquery();
}
function form_submit_old(e, ev)
{
     if (e.validationEngine("validate"))
    {
        moveAhead = true;
        if (typeof e.attr("data-pre-check") != "undefined") {
            if (!eval(e.attr("data-pre-check") + "(e)")) {
                moveAhead = false;
            }
        }
        if (moveAhead) {
           var formData = new FormData(e[0]);
           var data = $('form').serialize();
            e.find("[data-submit=submit]").attr("disabled", "disabled").after(loaderStr);
            e.find(".has-error").removeClass("has-error");
            e.find(".help-block").remove();
            $.ajax({
                 headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: e.attr("action"), //server script to process data
                type: 'POST',
                data: formData,
                cache: false,
                processData: false,
                contentType : false,
                success: function (response)
                {
                 response = JSON.parse(response);
                  renderAjax(response, e, true);
                },
                error: function (res) {
                    $(".submitLoader").remove();
                    $(".requestLoader").remove();
                  //  SIM("msg_topCommon", res.status +"  "+ res.statusText, "alert alert-danger");
                    SIM("msg_topCommon", "Please try again!", "alert alert-danger");
                    e.find("[data-submit=submit]").removeAttr("disabled");
                },
                complete: function () {
                    $("[data-submit-button='submit']").removeAttr("disabled");
                },
                conctentType: false,
                processData: false
            });

        }
    }

    if (typeof ev != "undefined" && ev != null) {
        ev.preventDefault();
    }
}

function form_submit(e, ev)
{

        moveAhead = true;
        // if (typeof e.attr("data-pre-check") != "undefined") {
        //     if (!eval(e.attr("data-pre-check") + "(e)")) {
        //         moveAhead = false;
        //     }
        // }
    if (moveAhead) {
           // var formData = new FormData(e[0]);
           var data =$('form').serialize();
          // var FormData = new FormData($(data)[1]);

            e.find("[data-submit=submit]").attr("disabled", "disabled").after(loaderStr);
            e.find(".has-error").removeClass("has-error");
            e.find(".help-block").remove();
            $.ajax({
                 headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: e.attr("action"), //server script to process data
                type: 'POST',
                dataType: 'json',
                data:data,
                cache: false,
                success: function (response)
                {
                   renderAjax(response, e, true);
                },
                error: function (res) {
                    $(".submitLoader").remove();
                    $(".requestLoader").remove();
                    SIM("msg_topCommon", "Please try again!", "alert alert-danger");
                    e.find("[data-submit=submit]").removeAttr("disabled");
                },
                complete: function () {
                    $("[data-submit-button='submit']").removeAttr("disabled");
                },
                conctentType: false,
                processData: false
            });
        }
    if (typeof ev != "undefined" && ev != null) {
        ev.preventDefault();
    }
}

function returnNumber(v) {
    v = parseInt(v);
    if (isNaN(v)) {
        return 0;
    } else {
        return v;
    }
}
function returnTwoDigit(v) {

    intNumber = returnNumber(v);
    rOff = (v - intNumber) * 100;

    if (rOff > 0) {
        rOffStr = rOff + "";
        rOff = returnNumber(rOff)
        if (rOffStr.length > 3) {
            rOffPre = returnNumber(rOffStr[3]);
            if (rOffPre >= 5) {
                rOff++;
            }
        }
        rOff = rOff / 100;
        return intNumber + rOff;
    } else {
        return v;
    }
}
function returnFloat(v) {
    ov = v;
    v = parseFloat(v);

    //   if()
    if (isNaN(v)) {
        return 0;
    } else {
        if (ov.toString().lastIndexOf(".") !== (ov.length - 1)) {
            tovl = ov.length;
            tdecC = ov.toString().lastIndexOf(".");
            if (tovl - tdecC > 3) {
//        ov.toString().lastIndexOf(".")
                return (Math.round(v * 100) / 100);
            } else {
                OVSUB = ov.toString().substr(ov.toString().lastIndexOf("."))
                if (OVSUB == ".0" || OVSUB == ".00") {
                    return ov;
                } else {
                    return v;
                }

            }
        } else {
            if (isNaN(v)) {
                return 0;
            } else {

                // if (ov != 0) {
                if (ov.toString().lastIndexOf(".") !== (ov.length - 1)) {
                    return (Math.round(v * 100) / 100);
                } else {
                    return (v.toString() + ".");
                }
//        } else {
//            return 0;
//        }
            }
        }
    }
}
function isNumberKey(key) {
    if ((key > 47 && key < 58) || (key > 95 && key < 106) || key == 0 || key == 8 || key == 9) {
        return true;
    } else {
        return false;
    }
}
function isNavigationKey(key) {
    if ((key > 26 && key < 41)) {
        return true;
    } else {
        return false;
    }
}
function openPopup(id) {
    $('#' + id).modal();
}
function closePopup(id) {
    $('#' + id).modal('hide');
}
function returnBlank(val) {
    if (typeof val == "undefined" || val == "" || val == 0) {
        return "";
    } else {
        return val;
    }
}
Dataparam = "";
function run_remove_element_form(div, type, id, table, form)
{
    Dataparam = $(form).serialize();
    run_remove_element(div, type, id, table);
}
function run_remove_element(div, type, id, table)
{
        close_popup('remove_element');

    extraParam = new Array();
    dataCehckObj = $("input[name^='deleteParam']");
    if (dataCehckObj.length > 0) {
        dataCehckObj.each(function () {
            if ($(this).is(":checked")) {
                extraParam.push($(this).val());
            } else {
                extraParam.push("");
            }

        });
    }
    $(div).hide('slow');
     $.ajax({
        headers:{
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        type: 'post',
        url: base_url+'/inlinedelete', //server script to process data
        dataType: 'json',
        data: {div: div, type: type, id: id, table: table, dp: Dataparam, EP: extraParam},
        cache: false,
        success: function (response)
        {
        data = response;
            SIM("msg_topCommon", data["msg_topCommon"], data["class"]);
            if (data['status'] == "1")
            {
                // if (typeof response['redirect'] != "undefined") {
                //     window.location.href = response['redirect'];
                //     return false;
                // }
                if (data['script'] != "")
                    eval(data['script']);
                // var oTable = $('#'+data['datatable']).dataTable(); // JQuery dataTable function to delete the row from the table
                // oTable.fnDeleteRow(oTable.fnGetPosition(rows));// JQuery dataTable function to delete the row from the table
                //  oTable.fnDraw(true);
                if (typeof data['datatable'] != "undefined" && data['datatable'] != "") {
                    datatables[data['datatable']].row($(data['row'])).remove().draw();
                }
                if (table != "")
                {
                    tables = table.split(",");
                    if (tables.length > 1)
                    {
                        for (i in tables)
                        {
                        }
                    } else {
                    }
                }
            } else {
                $(data['div']).show('slow');
            }

        },
        error: function () {
            $('.loading_div').hide();
            SIM("msg_topCommon", "Please try again!", "alert alert-danger");
        },
    });
}

print_css = true;
function hrefInNewTab(url) {
    window.open(url, '_blank');
}
function deleteRow(obj) {
    yesValue = 0;
    if (obj.parent().parent().hasClass("trDeleteRow") || obj.parent().parent().hasClass("trTempDeleteRow")) {
        tableObj = $("#" + obj.attr("data-parent"));
        rowObj = obj.parent().parent();
         $("#remove_element_message").html(rowObj.attr("data-row-remove-message"));
            $("#remove_element_options").html("");
            $("#remove_element_button").attr("onclick", "run_remove_element('#" + rowObj.attr("id") + "','" + rowObj.attr("data-type") + "','" + rowObj.attr("data-id") + "','" + "#" + obj.attr("data-parent") + "')");
            open_popup(320, 300, "remove_element");
       // }
    }
}
function deleteRowDatatable(obj) {
   yesValue = 0;
    if (obj.hasClass("tdDeleteColumnData")) {
        tableObj = $("#" + obj.attr("data-parent"));
        tdObj = obj;
        rowObj = obj.parent().parent();
        $("#remove_element_message").html(tdObj.attr("data-row-remove-message"));
            $("#remove_element_options").html("");
            $("#remove_element_button").attr("onclick", "run_remove_element('#" + rowObj.attr("id") + "','" + tdObj.attr("data-type") + "','" + tdObj.attr("data-id") + "','" + "#" + obj.attr("data-parent") + "')");
            open_popup(320, 300, "remove_element");

    }
}
function deleteRowCal(obj) {
    yesValue = 0;
    if (obj.parent().hasClass("trDeleteRow") || obj.parent().hasClass("trTempDeleteRow")) {

 //   if (obj.parent().parent().hasClass("trDeleteRow") || obj.parent().parent().hasClass("trTempDeleteRow")) {
        tableObj = $("#" + obj.attr("data-parent"));
        trRowObj = obj.parent();
        rowObj = obj.parent().parent().parent();
         $("#remove_element_message").html(rowObj.attr("data-row-remove-message"));
            $("#remove_element_options").html("");
            $("#remove_element_button").attr("onclick", "run_remove_element('#" + rowObj.attr("id") + "','" + rowObj.attr("data-type") + "','" + trRowObj.attr("data-id") + "','" + "#" + obj.attr("data-parent") + "')");
            open_popup(320, 300, "remove_element");
       // }
    }
}
function loadJquery() {
    
    $("[data-class=data-ajax-link]").each(function () {
        if (typeof $(this).attr("data-click") == "undefined") {
            $(this).attr("data-click", "true");
            $(this).click(function () {
                moveAhead = true;
                if (typeof $(this).attr("data-pre-check") != "undefined") {
                    if (!eval($(this).attr("data-pre-check"))) {
                        moveAhead = false;
                    }
                }
                if (moveAhead) {
                    param = "";
                    if (typeof $(this).attr("data-form-param") != "undefined") {
                        param = $("#" + $(this).attr("data-form-param")).serialize();
                    }
                    sendRequest($(this), $(this).attr("data-link"),method, param);
                }
                return false;
            });
        }

    });
    if ($(".ajaxTdDeleteRow").length) {
        $(".ajaxTdDeleteColumn").click(function () {
           deleteRow($(this));
        });
    }
    if ($(".ajaxtdExtraButtons").length) {
        $(".ajaxtdExtraButtons").children("i").click(function () {
            if ($(this).attr("data-function") == "popup") {
                open_extra_popup($(this));
            } else if ($(this).attr("data-function") == "popupRequest" || $(this).attr("data-function") == "popupNoneRequest") {
                trObj = $(this).parent().parent();
                sendRequest($(this), $(this).attr("data-data-link") + trObj.attr("data-id"),method, null);
            }
        });
        $(".ajaxtdExtraButtons").removeClass("ajaxtdExtraButtons");
    }
    if ($('.ajaxSelect2').length) {
        $('.ajaxSelect2').select2({width: "100%"});
        $(".ajaxSelect2").removeClass("ajaxSelect2");
    }
    if ($(".ajaxTooltip").length) {
        $(".ajaxTooltip").tooltip({html: true, container: 'body'});
        $(".ajaxTooltip").removeClass("ajaxTooltip");
    }
    if ($("[ajax-data-proper-class=toProperCase]").length) {
        $("[ajax-data-proper-class=toProperCase]").blur(function (event) {
            $(this).val($(this).val().toProperCase());
        });
        $("[ajax-data-proper-class=toProperCase]").removeAttr("ajax-data-proper-class");
    }
    if ($(".ajaxInlineEdit").length) {

        $(".ajaxInlineEdit").click(function () {
            if ($(this).attr("data-status") == "close") {
                $(this).html("<input onkeydown='saveInlineData($(this),event)' type='text' data-id='" + $(this).attr("data-id") + "' data-type='" + $(this).attr("data-type") + "'  value='" + $(this).attr("data-old-value") + "' class='form-control'/>");
                $(this).attr("data-status", "open");
                $(this).children("input").focus();
            }
        });
        $(".ajaxInlineEdit").removeClass("ajaxInlineEdit");
    }
}
$(document).ready(function () {
    $("form").submit(function (event) {
           form_submit($(this), event);
    });
    if ($('.select-combobox').length) {
        $('.select-combobox').each(function () {
            dropClassName = "";
            dropClass = $(this).attr("data-drop-class");
            if (typeof dropClass != "undefined") {
                dropClassName = dropClass;
            }
            $(this).select2({
                width: "100%",
                dropdownCssClass: dropClassName
            });
        })

    }
    $(".common_back").click(function () {
        close_popup($(this).attr("data-div-name"));
    })
    if ($(".dataTableClass").length) {
        $(".dataTableClass").each(function () {
            var attr = $(this).attr('data-scroll');
            var attr2 = $(this).attr('data-count');
            if (typeof attr !== typeof undefined && attr !== false) {
                if (typeof attr2 !== typeof undefined && attr2 !== false) {
                    config = {
                  //      "scrollY": 500,
                        "scrollX": true,
                        "order": [[0, "desc"]], 'bDestroy': true, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': false}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                } else {
                    config = {
                      //  "scrollY": 500,
                        "scrollX": true,
                        "order": [[0, "desc"]], 'bDestroy': true, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfo': '', 'sInfoFiltered': '', 'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': false}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                }
            } else {
                if (typeof attr2 !== typeof undefined && attr2 !== false) {
                    config = {'bDestroy': true, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': false}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                } else {
                    config = {'bDestroy': true, 'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfo': '', 'sInfoFiltered': '', 'sInfoEmpty': '', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': false}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                }

            }
        });
    }
    if ($(".dataTableClassWithPaginate").length) {
        $(".dataTableClassWithPaginate").each(function () {
            var attr = $(this).attr('data-scroll');
            var attr2 = $(this).attr('data-count');
            if (typeof attr !== typeof undefined && attr !== false) {
                if (typeof attr2 !== typeof undefined && attr2 !== false) {
              config = {
                        //"scrollY": false,
                     "scrollX": false,
                     "order": [[0, "desc"]],"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15,
                     'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                } else {
                   config = {
                    //    "scrollY": false,
                    "scrollX": false,
                    "order": [[0, "desc"]],"lengthChange": false, 'bDestroy': true,'iDisplayLength': 15,
                    'aoColumnDefs': [{bSortable: false}], 'oLanguage': {'sInfoEmpty': '<span class=no_record_found>No record found!</span>', 'sZeroRecords': '<span class=no_record_found>No record found!</span>', 'sSearch': '<span>Search</span>_INPUT_','sSearchPlaceholder':'Search Here'}, 'bPaginate': true}
                    datatables[$(this).attr("id")] = $(this).DataTable(config);
                    datatablesConfig[$(this).attr("id")] = config;
                }
            }
        });
    }
    $(".tdDeleteColumn").click(function () {
             deleteRow($(this));
    });
    $(".tdDeleteColumnData").click(function () {
        deleteRowDatatable($(this));
    });
    $(".tdDeleteColumnCaln").click(function () {
            deleteRowCal($(this));
    });
    if ($(".trDeleteRow").length) {
        $(".tdDeleteColumn").click(function () {
            deleteRow($(this));
        });
    }
    $(".common_back").click(function () {
        close_popup($(this).attr("data-div-name"));
    })
    loadJquery();
});
function calculate_age(birth_month,birth_day, birth_year) {
    var age = 0;
    var date2 = new Date("01/01/2018");
    var mg = birth_month + '/'+ birth_day+'/'+birth_year;
    var date1 = new Date(mg);

    var hours =  Math.max(0,(date2 - date1) / 36e5);
    var years =  Math.max(0,Math.floor((hours / 8766)));
    age = years;
    return age;
}
function calculate_age_effectivee(birth_month,birth_day, birth_year,effective_date) {
    var age = 0;
    var date2 = new Date(effective_date);
    var mg = birth_month + '/'+ birth_day+'/'+birth_year;
    var date1 = new Date(mg);

    var hours =  Math.max(0,(date2 - date1) / 36e5);
    var years =  Math.max(0,Math.floor((hours / 8766)));
    age = years;
    return age;
}
function formatNumber (num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}
function numberFormate(Phone_Number) {
    var ph = Phone_Number;
    var number = ph.replace(/[^\d]/g, '')
    if (number.length == 7) {
        number = number.replace(/(\d{3})(\d{4})/, "$1-$2");
    } else if (number.length == 10) {
        number = number.replace(/(\d{3})(\d{3})(\d{4})/, "($1) $2-$3");
    }
    return number;
}
function formatDate(date) {
    var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;

    return [day, month, year].join('/');
}
function sendRequest(e, url,method, param) {

 //console.log(e.attr("data-div"));
    if (!isRequest) {
        isRequest = true;
        if (e != null && typeof e.attr("data-popup") != "undefined") {
            if (e.attr("data-popup") == 2) {
                $('#myModalBackNone').modal();
            } else {
                $('#'+e.attr("data-div")).modal();
            }
        }
        if (e != null) {
            if (typeof e.attr("data-submit-button") != "undefined") {
                e.after(iconLoaderClass);
                e.attr("disabled", "disabled");
            } else {
                e.find("[data-submit-button='submit']").after(iconLoaderClass);
                e.find("[data-submit-button='submit']").attr("disabled", "disabled");
            }
        }
         $.ajax({
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               url: url, //server script to process data
               type: method,
               data: {data: param},
               cache: false,
            success: function (response)
            {
               $(".requestLoader").remove();
                isRequest = false;
                try {
                     response = JSON.parse(response);
                     renderAjax(response, e, false);

                } catch (eCatch) {
                    if (e != null && typeof e.attr("data-placement") != "undefined") {
                        var updateDiv = e.attr('data-placement').split(',');
                        for (i = 1; i <= updateDiv.length; i++) {
                            var resDiv = '#res_div_' + i;
                            if ($(resDiv, response).html() == null) {
                                $('#' + updateDiv[0]).html(response);
                            } else {
                                $('#' + updateDiv[i - 1]).html($(resDiv, response).html());
                            }
                        }
                    }
                }
            },
            error: function ()
            {
                $(".requestLoader").remove();
                isRequest = false;
            },
            complete: function () {
                $("[data-submit-button='submit']").removeAttr("disabled");
            },
             //alert(method);
        });
    }
}
function tdExtraButtonsClick(extaBTNObj) {
    if (extaBTNObj.attr("data-function") == "popup") {
       if (extaBTNObj.attr("data-approve") == "1") {
           $('.modalBackgroundNone').modal('show');
        } else {
           open_extra_popup(extaBTNObj);
        }
    } else if (extaBTNObj.attr("data-function") == "popupRequest" || extaBTNObj.attr("data-function") == "popupNoneRequest") {
        trObj = extaBTNObj.parent().parent();
        if (trObj.is("td")) {
            trObj = extaBTNObj.parent().parent().parent();
        }
        if (extaBTNObj.attr("data-function") == "popupNoneRequest") {
             open_extra_popup(extaBTNObj);
        }
        if (typeof extaBTNObj.attr("data-bigModalBox") == "undefined") {
            $("#myModalBackNone .modal-dialog").removeClass("bigModalBox");
        } else {
            $("#myModalBackNone .modal-dialog").addClass("bigModalBox");
        }
        var method = extaBTNObj.attr("data-method");
       sendRequest(extaBTNObj, extaBTNObj.attr("data-data-link") + extaBTNObj.attr("data-id"),method, null);
    }
}
function onClickableRow(eObj) {
    if (eObj.hasClass('trScrollSelectedRow')) {
        eObj.removeClass('trScrollSelectedRow');
    } else {
        $(".trScrollSelectedRow").removeClass('trScrollSelectedRow');
        eObj.addClass('trScrollSelectedRow')
    }
}
function showDeleteButton(eObj) {
    if (eObj.hasClass("trDeleteRow") || eObj.hasClass("trTempDeleteRow")) {
        eObj.children("td").eq(-1).html('&times;');
    }
}
function hideDeleteButton(eObj) {
    if (eObj.hasClass("trDeleteRow") || eObj.hasClass("trTempDeleteRow")) {
        eObj.children("td").eq(-1).html('&nbsp;');
    }
}
function cb(start, end) {
    $('#reportrange span').html(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
}
