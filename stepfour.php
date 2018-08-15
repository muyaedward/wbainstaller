<h1>New WritersBay App Installation</h1>
<div class="alert alert-success text-center" role="alert">
    <strong><i class="fa fa-check-circle"></i>Installation Completed Successfully!</strong>
</div>
<h2>Next Steps</h2>
<p><strong>1. Delete the Install Folder</strong></p>
<p>You should now delete the <em>/install/</em> directory from your web server. This is to disallow another installation which will overwrite your current installation.</p>
<p><strong>2. Setup a Cron Job</strong></p>
<p>You should setup a cron job in your control panel to run using the following command every 1 minute. If you do not set up cron jobs some features will not work eg emails, updates e.t.c. <a href="https://writersbayapp.com/tutorials/cron">How to set up cron jobs in cpanel</a>
    <br />
    <br />
    <input type="text" value="/usr/local/bin/php <?php echo dirname(dirname(dirname(__FILE__))); ?>/writersbayapp/wbapp/artisan schedule:run >> /dev/null 2>&1" class="form-control" readonly>
</p>
<p><strong>4. Setting up WritersBay App</strong></p>
<p>Setting up WritersBay App is very easy. Login to the dashboard with this details. <b>Email: help@writersbayapp.com</b> and <b>Password: secret</b>. These are default login details. Make sure you change them when you login. <a href="/settings">Change account details</a></p>
<div class="alert alert-success text-center" role="alert">
	There are lot of useful <strong>resources and tutorials</strong> on how to use WritersBay App find them at <a href="https://writersbayapp.com" target="_blank">https://writersbayapp.com/tutorials</a>.
</div>
<br>
<p align="center">
    <a href="/" id="btnGoToAdminArea" class="btn btn-default">Go to the Admin Area Now &raquo;</a>
</p>
<br>
<h2>Thank you for choosing WritersBay App</h2>