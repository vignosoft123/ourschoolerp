
<div class="box">
    <div class="box-header">
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active">Notification Config</li>
        </ol>
    </div>

    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">

                <div class="nav-tabs-custom" style="margin-bottom:15px;">
                    <ul class="nav nav-tabs">
                        <li>
                            <a href="<?=base_url('mailandsmstemplate/index')?>">
                                <i class="fa icon-template"></i> <?=$this->lang->line('panel_title')?>
                            </a>
                        </li>
                        <li>
                            <a href="<?=base_url('mailandsmstemplate/whatsapp_index')?>">
                                <i class="fa fa-whatsapp"></i> Whatsapp Templates
                            </a>
                        </li>
                        <li class="active">
                            <a href="<?=base_url('mailandsmstemplate/notification_config')?>">
                                <i class="fa fa-bell"></i> Notification Config
                            </a>
                        </li>
                    </ul>
                </div>

                <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $this->session->flashdata('success') ?>
                </div>
                <?php } ?>

                <form method="post" action="<?=base_url('mailandsmstemplate/notification_config')?>">

                    <table class="table table-bordered table-hover" style="margin-bottom:20px;">
                        <thead>
                            <tr style="background:#3c8dbc;color:#fff;">
                                <th style="width:50px;">#</th>
                                <th>Event Name</th>
                                <th class="text-center" style="width:130px;">
                                    <i class="fa fa-comment"></i> SMS
                                </th>
                                <th class="text-center" style="width:160px;">
                                    <i class="fa fa-whatsapp"></i> WhatsApp
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($events)) { $i = 1; foreach ($events as $event) { ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><strong><?= htmlspecialchars($event->event_name) ?></strong></td>
                                <td class="text-center">
                                    <input type="hidden" name="sms_enabled_<?= $event->event_key ?>" value="0">
                                    <input type="checkbox"
                                           name="sms_enabled_<?= $event->event_key ?>"
                                           value="1"
                                           <?= $event->sms_enabled ? 'checked' : '' ?>
                                           style="width:18px;height:18px;cursor:pointer;">
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="whatsapp_enabled_<?= $event->event_key ?>" value="0">
                                    <input type="checkbox"
                                           name="whatsapp_enabled_<?= $event->event_key ?>"
                                           value="1"
                                           <?= $event->whatsapp_enabled ? 'checked' : '' ?>
                                           style="width:18px;height:18px;cursor:pointer;">
                                </td>
                            </tr>
                            <?php } } else { ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No events found. Run <strong>/Schema_update/apply_updates</strong> to seed the table.
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
