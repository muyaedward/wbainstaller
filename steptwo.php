<h1>New WritersBay App Installation</h1>
<div class="alert alert-danger text-center" role="alert" id="requirementsSummary"><strong><i class="fa fa-warning"></i> System Requirements Check Failed</strong>
    <div style="font-size:0.9em;padding:6px;">Your system <strong>does not</strong> meet the requirements necessary to run this version of Writerzbay platform.
        <br />You must resolve the issues below before you can continue with installation.</div>
</div>
<script>
jQuery(function() {
    jQuery('[data-toggle="tooltip"]').tooltip()
})
</script>
<table class="table table-striped requirements">
    <tr>
        <th>#</th>
        <th>Reason for error</th>
        <th>Help</th>
    </tr>
    <?php
            $start = 0;
            foreach ($install->requirements() as $req) { $start ++;?>
        <tr>
            <td>
                <?php echo $start; ?>
            </td>
            <td>
                <?php echo $req; ?>
            </td>
            <td>
                <button type="button" class="btn btn-info btn-xs help-icon" data-toggle="tooltip" data-placement="right" title="<?php echo $req; ?>"><i class="fa fa-question"></i></button>
            </td>
        </tr>
        <?php } ?>
</table>
<p>Please address the issues listed above and then click the button below to recheck the requirements and continue. If you need help <a href="https://writersbayapp.com/contact">Contact us</a></p>
<p align="center"><a href="?step=two" id="btnRecheckRequirements" class="btn btn-success">Recheck Requirements</a></p>