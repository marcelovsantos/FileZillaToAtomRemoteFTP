<?php
// Default
$source = 'FileZilla.xml';

// Teste second argument
if (empty($argv[1]) == FALSE) {
    $source = $argv[1];
}

function search_server($root){

    if (empty($root->Folder)) 
    {
        foreach ($root as $server) {
            parse_server($server);
        }
    }
    else 
    {
        foreach($root->Folder as $folder)
        {
            search_server($folder);
        }
    }
}

function parse_server($server){
    
    $config = array(
        "protocol" =>  '"ftp"',
        "host" =>  '"example.com"', // string - Hostname or IP address of the server. Default: 'localhost'
        "port" =>  '22', // integer - Port number of the server. Default: 22
        "user" =>  '"user"', // string - Username for authentication. Default: (none)
        "pass" =>  '"pass"', // string - Password for password-based user authentication. Default: (none)
        "promptForPass" =>  'false', // boolean - Set to true for enable password/passphrase dialog. This will prevent from using cleartext password/passphrase in this config. Default: false
        "remote" =>  '"/"', // try to use absolute paths starting with /
        "agent" =>  '""', // string - Path to ssh-agent's UNIX socket for ssh-agent-based user authentication. Windows users: set to 'pageant' for authenticating with Pageant or (actual) path to a cygwin "UNIX socket." Default: (none)
        "privatekey" =>  '""', // string - Path to the private key file (in OpenSSH format). Default: (none)
        "passphrase" =>  '""', // string - For an encrypted private key, this is the passphrase used to decrypt it. Default: (none)
        "hosthash" =>  '""', // string - 'md5' or 'sha1'. The host's key is hashed using this method and passed to the hostVerifier function. Default: (none)
        "ignorehost" =>  'true',
        "connTimeout" =>  '10000', // integer - How long (in milliseconds) to wait for the SSH handshake to complete. Default: 10000
        "keepalive" =>  '10000', // integer - How often (in milliseconds) to send SSH-level keepalive packets to the server (in a similar way as OpenSSH's ServerAliveInterval config option). Set to 0 to disable. Default: 10000
        "watch" => '[]', // array - Paths to files, directories, or glob patterns that are watched and when edited outside of the atom editor are uploaded. Default : []
        "watchTimeout" => '500' // integer - The duration ( in milliseconds ) from when the file was last changed for the upload to begin.
    );
    
    $map = array(
        'host' => 'Host',
        'port' => 'Port',
        'user' => 'User',
        'pass' => 'Pass',
    );
    
    $name = (string) trim($server);
    
    echo "/* {$name} */";
    
    echo "\n{";
    foreach ($config as $name => $default) {
        if (empty($map[$name]) == FALSE && isset($server->$map[$name])) 
        {
            $default = (string) $server->$map[$name];
            if ($default !== (string) (int) $default) {
                $default = var_export($default, TRUE);
                $default = str_replace("'", '"', $default);
            }
        }
        
        echo "\n\t\"{$name}\": {$default},";
    }
    echo "\n}\n\n";
    
    $config = json_encode($config);

}

$root = simplexml_load_file($source);
search_server($root->Servers);
