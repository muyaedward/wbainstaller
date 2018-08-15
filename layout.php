<!DOCTYPE html>
<html>
    <head>
        <title>WritersBay App install Process</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link href="assets/css/app.css" rel="stylesheet">
    </head>
    <body>
        <div id="writersbayapp" class="wrapper">
            <div class="version">Version <?php echo $currentversion; ?></div>
            <div class="hidden-xs" style="margin:30px; width: 350px;">
                <a href="/install"><img src="assets/img/logo.jpg" class="img-responsive" alt="WritersBay App- Academic Writing Software" border="0" /></a>
            </div>
                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger" role="alert" id="requirementsSummary">
                        <strong><i class="fa fa-times"></i> The following errors occurred:</strong>
                        <div style="font-size:0.9em;padding:2px;">
                            <ol>
                                <?php foreach ($errors as $error) { ?>
                                    <li><?php echo $error; ?></li>
                                <?php } ?>                            
                            </ol>
                        </div>
                    </div>
                <?php } ?>
                <?php include($template); ?>            
            <br>
            <br>
            <div align="center"><small><b>Copyright &copy; <?php echo date("Y"); ?> WritersBay App</b><br>
                <a href="https://www.writersbayapp.com" target="_blank"><b>WritersBay App</b></a></small>
            </div>
        </div>
    </body>
    <script type="text/javascript" src="assets/js/app.js"></script>
</html>