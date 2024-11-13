<?php
require_once 'hosts.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css">
    <link rel="stylesheet" href="default.css">
    <title>WoL Service</title>
</head>
<body>
    <div id="globalAlert" class="alert alert-dismissible fade show" role="alert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 1050;">
        <span id="alertMessage"></span>
        <button type="button" class="close" onclick="hideAlert()">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Wake On LAN Service</h1>
            <p class="lead"></p>
        </div>
    </div>

    <div class="container">
        <div id="hostList" class="row">
            <?php foreach ($hosts as $host): ?>
                <div class="col-12 col-sm-12 col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <i class="card-title fa fa-desktop" style="font-size:30px;"></i>
                                <h5 class="card-title"><?php echo htmlspecialchars($host['hostName']); ?></h5>
                            </div>
                            <div class="btn-group">
                                <button type="button" onclick="sendAction('<?php echo $host['macAddress']; ?>', '<?php echo $host['ipAddress']; ?>', '<?php echo $host['hostName']; ?>', 'Wake', '')" class="btn btn-primary">Wake</button>
                                <button type="button" onclick="sendAction('<?php echo $host['macAddress']; ?>', '<?php echo $host['ipAddress']; ?>', '<?php echo $host['hostName']; ?>', 'Restart', '<?php echo $host['pw']; ?>')" class="btn btn-warning">Restart</button>
                                <button type="button" onclick="sendAction('<?php echo $host['macAddress']; ?>', '<?php echo $host['ipAddress']; ?>', '<?php echo $host['hostName']; ?>', 'Shutdown', '<?php echo $host['pw']; ?>')" class="btn btn-danger">Shutdown</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
