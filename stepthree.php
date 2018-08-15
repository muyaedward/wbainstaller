<form method="post" action="index.php?step=four" id="AddspinCopyfiles">
    <h1>WritersBay App License Key</h1>
    <p>Please provide the licence Key that you received when you bought the application. You can buy a licence as low as <b>$199</b> from <a href="https://www.writersbayapp.com" target="_blank">WritersBay App website</a> or alternatively if you obtained a license from a reseller, they should have already provided a license key to you.</p>
    <table class="table-padded">
        <tr>
            <td width="200">
                <label for="licensekey">License Key:</label>
            </td>
            <td width="350">
                <input type="text" name="licensekey" value="" id="licensekey" value="" class="form-control" required>
            </td>
        </tr>
    </table>
    <br>
    <h1>Cpanel Connection Details</h1>
    <p>WritersBay App requires to connect to ssh server in order to perform command tasks. Cpanel details were provided to you by your hosting provider. <a href="https://www.writersbayapp.com" target="_blank">Get help on how to fill this section</a></p>
    <table class="table-padded">
        <tr>
            <td width="200">
                <label for="ipaddress">Ip address:</label>
            </td>
            <td width="200">
                <input type="text" name="ipaddress" size="20" value="<?php echo $_SERVER['SERVER_ADDR']; ?>" class="form-control" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="cpanelusername">Cpanel Username:</label>
            </td>
            <td>
                <input type="text" value="" name="cpanelusername" size="15" class="form-control" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="sshport">SSH Port:</label>
            </td>
            <td>
                <input type="text" placeholder="22" value="" name="sshport" size="15" class="form-control" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="keypassword">Cpanel Password:</label>
            </td>
            <td>
                <input type="password" name="keypassword" value="" size="20" value="" class="form-control" autocomplete="off" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="sitepath">Website path:</label>
            </td>
            <td>
                <input type="text" name="sitepath" size="20" value="" class="form-control" placeholder="public_html" required>
            </td>
        </tr>
    </table>
    <br>
    <h1>Database Connection Details</h1>
    <p>WritersBay App requires a MySQL database. If you have not already created one, please do so now. <a href="https://www.writersbayapp.com" target="_blank">How to create a mysql database and user</a></p>
    <table class="table-padded">
        <tr>
            <td width="200">
                <label for="databaseHost">Database Host:</label>
            </td>
            <td width="200">
                <input type="text" name="databaseHost" size="20" value="localhost" class="form-control" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="databaseUsername">Database Username:</label>
            </td>
            <td>
                <input type="text" name="databaseUsername" id="databaseUsername" size="20" value="" class="form-control" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="databasePassword">Database Password:</label>
            </td>
            <td>
                <input type="password" name="databasePassword" id="databasePassword" size="20" value="" class="form-control" autocomplete="off" required>
            </td>
        </tr>
        <tr>
            <td>
                <label for="databaseName">Database Name:</label>
            </td>
            <td>
                <input type="text" name="databaseName" id="databaseName" size="20" value="" class="form-control" required>
            </td>
        </tr>
    </table>
    <br />
    <p align="center">
        <button type="submit" class="btn btn-lg btn-primary">Continue &raquo;</button>
    </p>
</form>