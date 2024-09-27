<?php
class Bitcoin
{
    private $username;
    private $password;
    private $host;
    private $port;
    private $proto;
    private $url;
    private $CACertificate;

    public $status;
    public $error;
    public $raw_response;
    public $response;
    private $id = 0;

    public function __construct($username, $password, $host = 'localhost', $port = 8332, $url = null)
    {
        $this->username      = $username;
        $this->password      = $password;
        $this->host          = $host;
        $this->port          = $port;
        $this->url           = $url;
        $this->proto         = 'http';
        $this->CACertificate = null;
    }

    public function setSSL($certificate = null)
    {
        $this->proto         = 'https';
        $this->CACertificate = $certificate;
    }

    public function __call($method, $params)
    {
        $this->status       = null;
        $this->error        = null;
        $this->raw_response = null;
        $this->response     = null;

        $params = array_values($params);
        $this->id++;

        $request = json_encode(array(
            'method' => $method,
            'params' => $params,
            'id'     => $this->id
        ));

        $curl = curl_init("{$this->proto}://{$this->host}:{$this->port}/{$this->url}");
        $options = array(
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->username . ':' . $this->password,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => array('Content-type: application/json'),
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $request
        );

        if (ini_get('open_basedir')) {
            unset($options[CURLOPT_FOLLOWLOCATION]);
        }

        if ($this->proto == 'https' && !empty($this->CACertificate)) {
            $options[CURLOPT_CAINFO] = $this->CACertificate;
            $options[CURLOPT_CAPATH] = dirname($this->CACertificate);
        } elseif ($this->proto == 'https') {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
        }

        curl_setopt_array($curl, $options);
        $this->raw_response = curl_exec($curl);
        $this->response = json_decode($this->raw_response, true);
        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        if (!empty($curl_error)) {
            $this->error = $curl_error;
        }

        if (!empty($this->response['error'])) {
            $this->error = $this->response['error']['message'];
        } elseif ($this->status != 200) {
            switch ($this->status) {
                case 400: $this->error = 'HTTP_BAD_REQUEST'; break;
                case 401: $this->error = 'HTTP_UNAUTHORIZED'; break;
                case 403: $this->error = 'HTTP_FORBIDDEN'; break;
                case 404: $this->error = 'HTTP_NOT_FOUND'; break;
            }
        }

        if ($this->error) {
            return false;
        }

        return $this->response['result'];
    }
}

// Database connection settings
$db_host = 'localhost';
$db_user = 'your_db_user'; // Replace with your database username
$db_pass = 'your_db_password'; // Replace with your database password
$db_name = 'your_db_name'; // Replace with your database name

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT bitcoin_rpc_username, bitcoin_rpc_password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($bitcoin_rpc_username, $bitcoin_rpc_password);
        $stmt->fetch();
        
        // Verify password (assumes passwords are hashed with bcrypt)
        if (password_verify($password, $bitcoin_rpc_password)) {
            $host = $_POST['host'] ?: 'localhost';
            $port = $_POST['port'] ?: 8332;
            $certificate = $_POST['certificate'];

            $bitcoin = new Bitcoin($bitcoin_rpc_username, $password, $host, $port);
            if (!empty($certificate)) {
                $bitcoin->setSSL($certificate);
            }

            $info = $bitcoin->getinfo();

            if ($info) {
                echo "<h2>Bitcoin Server Info:</h2>";
                echo "<pre>" . print_r($info, true) . "</pre>";
            } else {
                echo "<h2>Error:</h2>";
                echo "<pre>" . $bitcoin->error . "</pre>";
            }
        } else {
            echo "<h2>Error:</h2><pre>Invalid username or password.</pre>";
        }
    } else {
        echo "<h2>Error:</h2><pre>User not found.</pre>";
    }

    $stmt->close();
}

$conn->close();
?>
