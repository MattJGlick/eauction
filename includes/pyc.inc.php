<?php
/*! \file pyc.inc.php
 *  \brief This page contains all the PYC-specific methods.
 */
 
require_once '../includes/class.googlevoice.php';

/*! \fn getPYCList()
 *  \brief Pulls data from the PYCList table.
 *
 *  \returns All data from PYCList table as array.
 */
function getPYCList()
{
    $sql = "SELECT * FROM PYCList;";
    $result = query($sql);
    
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

/*! \fn getPYCMsgData($problemType)
 *  \brief Gets relevant data for problemType.
 *
 *  \returns Data from PYCList where row ID is $problemType, as array.
 */
function getPYCMsgData ($problemType)
{
    $sql = "SELECT * FROM PYCList
            WHERE id = :problemType;";
            
    $params = array(':problemType' => $problemType);
    $result = query($sql, $params);
    
    return fetch($result);
}

/*! \fn pycLog($problemType, $message);
 *  \brief Logs Ping Your Captain message in PYCLog table.
 * 
 */
function pycLog($problemType, $message)
{
    // Get data for $problemType.
    $problemData = getPYCMsgData($problemType);

    // Get user data.
    $userData = getMachineData();
    $userData['user'] = $_SESSION['user']['id'];

    // Put data in PYCLog.
    $sql = "INSERT INTO pycLog(user, equipment, pyclist, message)
            VALUES(:user, :equipment, :pyclist, :message);";

    $params = array(
                    ':user'         =>      $userData['user'],
                    ':equipment'    =>      $userData['id'], 
                    ':pyclist'      =>      $problemData['id'], 
                    ':message'      =>      $message
                    );

    query($sql, $params);        
}

/*! \fn pinCaptain($problemType, $message)
 *  \brief Send PYC message to appropriate groupme.
 *
 * \returns TRUE if message was successful, false otherwise.
 */
function pingCaptain ($problemType, $message)
{
    // Get phone number.
    $number = getPYCMsgData($problemType)['Number'];
    
    $smsHandler = new GoogleVoice('thonpass13@gmail.com', 'qcVpQhWFhCZvfbViJiBa');
    $status = $smsHandler->sms($number, $message);

    $result = strpos(strtolower($status), 'text sent');
    
    if (!$result)
    {
        return false;
    }
    else
    {
        pycLog($problemType, $message);
        return true;
    }
}



?>
