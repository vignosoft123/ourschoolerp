<div class="footer-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="copyright text-center">
                    <?= !empty($backend->footer) ? $backend->footer : '' ?>
                    <?php echo ' -v'.VERSION; ?>
                </div>
                <div class="text-center" style="margin-top:8px; font-size:13px;">
                    <a href="<?= base_url('frontend/terms') ?>" style="color:#8dc63f; margin-right:15px;">
                        <?= !empty($frontend->terms_label) ? htmlspecialchars($frontend->terms_label) : 'Terms &amp; Conditions' ?>
                    </a>
                    <span style="color:#999;">|</span>
                    <a href="<?= base_url('frontend/privacy') ?>" style="color:#8dc63f; margin-left:15px;">
                        <?= !empty($frontend->privacy_label) ? htmlspecialchars($frontend->privacy_label) : 'Privacy Policy' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
