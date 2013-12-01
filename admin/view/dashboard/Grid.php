<?php
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
    header('Location: /');
    die();
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/css/styles.css" />
<script type="text/javascript" charset="utf-8" src="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf-8" src="<?php echo MYMIGSPAYMENTGATEWAYURL; ?>/static/js/TableTools.min.js"></script>
<script type="text/javascript" charset="utf-8">
    jQuery(document).ready(function() {
        TableTools.BUTTONS.download = {
            "sAction": "text",
            "sTag": "default",
            "sFieldBoundary": "",
            "sFieldSeperator": "\t",
            "sNewLine": "<br>",
            "sToolTip": "Export payment list",
            "sButtonClass": "DTTT_button_text",
            "sButtonClassHover": "DTTT_button_text_hover",
            "sButtonText": "Download",
            "mColumns": "all",
            "bHeader": true,
            "bFooter": true,
            "sDiv": "",
            "fnMouseover": null,
            "fnMouseout": null,
            "fnClick": function(nButton, oConfig) {
                var iframe = document.createElement('iframe');
                iframe.style.height = "0px";
                iframe.style.width = "0px";
                iframe.src = oConfig.sUrl + "?action=my_bank_action&getexport=1&security=<?php echo $ajax_nonce ?>";
                document.body.appendChild(iframe);
            },
            "fnSelect": null,
            "fnComplete": null,
            "fnInit": null
        };
        jQuery('#payment_logs').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bJQueryUI": true,
            "aaSorting": [[11, "desc"]],
            "sPaginationType": "full_numbers",
            "sServerMethod": "POST",
            "sDom": 'T<"clear">lfrtip',
            "sAjaxSource": ajaxurl,
            "oTableTools": {
                "aButtons": [{
                        "sExtends": "download",
                        "sButtonText": "Export to xls",
                        "sUrl": ajaxurl
                    }]
            },
            "fnServerParams": function(aoData) {
                aoData.push({"name": "id", "value": 1});
                aoData.push({"name": "action", "value": "my_bank_action"});
                aoData.push({"name": "security", "value": "<?php echo $ajax_nonce ?>"});
                aoData.push({"name": "typeofcall", "value": "<?php echo MigsPaymentGatewayServlet::GETAPPLICATIONSBYADMIN; ?>"});
            },
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                /* Append the grade to the default row class name */
                var id = aData[0];
                jQuery('td:eq(0)', nRow).html('(<b>' + id + '</b>)');
                var paid = aData[1];
                var cssclass = "";
                if (paid === "Paid"){
                    cssclass = "class='paidgrid'";
                }
                jQuery('td:eq(1)', nRow).html('<span '+cssclass+'>' + paid + '</span>');
                return nRow;
            }
        });
    });
</script>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="payment_logs">
    <thead>
        <tr>
            <th>#</th>
            <th>Paid</th>
            <th width="100px">Post</th>
            <th>User</th>
            <th>Amount</th>
            <th>Transaction Ref.</th>
            <th>IP</th>
            <th>Receipt number</th>
            <th>Transection number</th>
            <th>Batch number</th>
            <th>Bank response desc</th>
            <th>Date of order</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5" class="dataTables_empty">Loading data from server</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th>#</th>
            <th>Paid</th>
            <th>Post</th>
            <th>User</th>
            <th>Amount</th>
            <th>Transaction Ref</th>
            <th>IP</th>
            <th>Receipt number</th>
            <th>Transection number</th>
            <th>Batch number</th>
            <th>Bank response desc</th>
            <th>Date of order</th>
        </tr>
    </tfoot>
</table>