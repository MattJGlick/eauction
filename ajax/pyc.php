<?php
/*! \file pyc.php
*  \brief This page runs the Ping Your Captain function as described in 3.2.11 of the SRS.
*
*  This page is the user interface for the PYC Function, most of the actual logic is defined in the include file.
*/

$page_title = "Ping Your Captain";

require_once '../includes/php.header.inc.php';
require_once '../includes/pyc.inc.php';

// Set Initial Variables
$display = 0;

/*
*    This page has three displays.
*    Display = 0: User selects problem type (which determines recipients) and adds a brief message.
*    Display = 1: System confirms user's message and recipients.
*    Display = 2: System confirms whether message was successfully sent or not.
*/

if (isset($_REQUEST['messageSubmitted']))      // If user submitted a message, display confirmation.

{
    $display = 1;
    
    $messageData = getPYCMsgData($_REQUEST['problemType']);
}

elseif (isset($_REQUEST['messageConfirmed']))     // If user confirmed message, send message and display confirmation.

{

    if ($_REQUEST['messageConfirmed'] == 'Confirm Message')

    {   
        $display = 2;

        // Build PYC Message.
        $machineData = getMachineData();     
        $finalMessage = 'PC: ' . $machineData['name'] . ' LOC: ' . $machineData['location'] . 
                        ' MSG: ' . $_REQUEST['PYCMessage'];

        // Attempt to send message.
        $result = pingCaptain($_REQUEST['problemType'], $finalMessage);

        // Change Captain MOTD if location is PR/ENT or PASS Info Booth.
        if ($machineData['location'] == 'PR/ENT Entrance' || 
            $machineData['location'] == 'Gate A') {

            $sql = "UPDATE globalVars SET value = :message WHERE variable = 'CAP_MOTD';";

            $params = array(':message'  => $finalMessage);

            query($sql, $params);
        }

        if ($result)
        {
            message('success','Message successfully sent, a Captain will assist you shortly.');
        }
        else
        {
            message('error', 'Message failed to send. Please try again later.');
        }
    }
    else
    {
        $display = 0;

        $PYCList = getPYCList();
    }

}

elseif ($display == 0)                           // Otherwise display submission form.
{
    $PYCList = getPYCList();
}

   

require_once '../includes/html.header.inc.php';

// Format messages for display

$messages = formatMessages();



echo (isset($messages)) ? $messages : '';



if ($display == 0)

{

?>

    



    <div class="section_title">Ping Your Captain - Submit Message</div>

    <div class="section_title_divider"></div>

    <div class="section_description">Select the type of problem you are having and input a message describing your issue (40 characters or less).</div>

    <div class="section_content">

        <form id="pingYourCaptain" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">

            <?php foreach ($PYCList as $row) {?>

                <input type = "radio" name = "problemType" value = "<?php echo $row['id']?>"><?php echo $row['Description']?></input><br />                

            <?php }?>

            <br /><input class = "pyctext" type = "text" maxlength = "40" size = "40" name = "PYCMessage" title = "Enter your message (40 characters or less)." />

            <span class="countdown"></span><br /><br />

            <input type = "submit" name = "messageSubmitted" value = "Submit Message"/>

        </form>

    </div>

    <script type="text/javascript">

    

        function updateCountdown() {

            // 40 is the max message length

            var remaining = 40 - jQuery('.pyctext').val().length;

            jQuery('.countdown').text(remaining + ' characters remaining.');

        }

        

        jQuery(document).ready(function($) {

            updateCountdown();

            $('.pyctext').live('input', updateCountdown);

            $('.pyctext').keyup(updateCountdown);

        });

    

    </script>

<?php 

}

elseif ($display == 1)

{

?>

    <div class="section_title">Ping Your Captain - Confirm Message</div>

    <div class="section_title_divider"></div>

    <div class="section_description">Review your message and either confirm submission or cancel.</div>

    <div class="section_content">

        <table>

            <tr><td><b>Problem Type:</b></td><td><?php echo $messageData['Description']?></td></tr>

            <tr><td><b>Message Recipients:</b></td><td><?php echo $messageData['Name']?></td></tr>

            <tr><td><b>Message Text:</b></td><td><?php echo $_REQUEST['PYCMessage']?></td></tr>            

        </table><br />

        <form id="pingYourCaptain" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">            

            <input type = "submit" name = "messageConfirmed" value = "Confirm Message" />

            <input type = "submit" name = "messageConfirmed" value = "Go Back" />

            <input type = "hidden" name = "problemType" value = "<?php echo $_REQUEST['problemType']?>" />

            <input type = "hidden" name = "PYCMessage" value = "<?php echo $_REQUEST['PYCMessage']?>" />

        </form>

    </div>



    

<?php }



require '../includes/footer.inc.php';



?>