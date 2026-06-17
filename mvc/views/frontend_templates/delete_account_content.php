<!-- Breadcrumb Area -->
<div class="bradcam-area area-padding">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="section-title white-title bradcam-title text-uppercase text-center">
                    <h2>Delete Account</h2>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                </div>
            </div>
            <div class="bradcam-wrap text-center">
                <nav class="bradcam-inner">
                    <?php if(customCompute($homepage)): ?>
                        <?php $hometype = (isset($homepage->pagesID) ? 'page' : (isset($homepage->postsID) ? 'post' : '')); ?>
                        <a class="bradcam-item text-uppercase" href="<?= base_url('frontend/'.$hometype.'/'.$homepage->url) ?>">Home</a>
                    <?php else: ?>
                        <a class="bradcam-item text-uppercase" href="<?= base_url('frontend/index') ?>">Home</a>
                    <?php endif; ?>
                    <span class="brd-separetor">/</span>
                    <span class="bradcam-item active text-uppercase">Delete Account</span>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb Area -->

<!-- Delete Account Content -->
<section class="delete-account-area area-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="delete-account-content" style="background:#fff; padding:40px; border-radius:6px; line-height:1.9; color:#444;">

                    <h2 style="color:#333; margin-bottom:25px; font-size:26px; font-weight:700; text-transform:uppercase; border-bottom:3px solid #8dc63f; padding-bottom:12px;">
                        ACCOUNT DELETION REQUEST
                    </h2>

                    <p style="margin-bottom:28px;">
                        <strong><?= !empty($backend->sname) ? htmlspecialchars($backend->sname) : '' ?></strong> respects your right to control your personal data. Use the form below to submit an account deletion request, or you may also request deletion directly from within the mobile app under <em>Settings &rarr; Delete Account</em>.
                    </p>

                    <!-- Request Form -->
                    <div style="background:#f9f9f9; border:1px solid #e8e8e8; border-radius:6px; padding:30px; margin-bottom:36px;">
                        <h4 style="color:#333; margin-bottom:20px; font-size:17px; font-weight:700;">Submit Deletion Request</h4>

                        <div id="dar-alert" style="display:none; padding:14px 18px; border-radius:4px; margin-bottom:20px; font-size:14px;"></div>

                        <form id="dar-form">
                            <div class="row">
                                <div class="col-sm-6" style="margin-bottom:16px;">
                                    <label style="font-weight:600; font-size:13px; color:#555; margin-bottom:6px; display:block;">Account Type <span style="color:#e74c3c;">*</span></label>
                                    <select name="type" id="dar-type" class="form-control" required style="height:42px; border-radius:4px; border:1px solid #ddd;">
                                        <option value="">-- Select Type --</option>
                                        <option value="student">Student / Parent</option>
                                        <option value="teacher">Teacher</option>
                                        <option value="user">Staff / User</option>
                                    </select>
                                </div>
                                <div class="col-sm-6" style="margin-bottom:16px;">
                                    <label style="font-weight:600; font-size:13px; color:#555; margin-bottom:6px; display:block;">Registered Mobile Number <span style="color:#e74c3c;">*</span></label>
                                    <input type="text" name="phone" id="dar-phone" class="form-control" placeholder="e.g. 9876543210" maxlength="15" required style="height:42px; border-radius:4px; border:1px solid #ddd;" />
                                </div>
                            </div>
                            <div style="margin-bottom:16px;">
                                <label style="font-weight:600; font-size:13px; color:#555; margin-bottom:6px; display:block;">Reason for Deletion <span style="color:#999; font-weight:400;">(optional)</span></label>
                                <textarea name="reason" id="dar-reason" class="form-control" rows="3" placeholder="Briefly describe why you want to delete your account..." style="border-radius:4px; border:1px solid #ddd; resize:vertical;"></textarea>
                            </div>
                            <button type="submit" id="dar-submit" style="background:#8dc63f; color:#fff; border:none; padding:11px 30px; border-radius:4px; font-size:14px; font-weight:600; cursor:pointer;">
                                Submit Request
                            </button>
                        </form>
                    </div>
                    <!-- End Request Form -->

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">1. What Happens After You Submit</h4>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">Your request is logged and marked as <strong>Pending</strong>.</li>
                            <li style="margin-bottom:6px;">The school administration will review and verify your identity.</li>
                            <li style="margin-bottom:6px;">Once approved, your account will be <strong>deactivated within 7 working days</strong>.</li>
                            <li style="margin-bottom:6px;">A confirmation will be sent to your registered phone number or email.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">2. What Data Will Be Deleted</h4>
                        <p>Upon approval of your request, the following data will be permanently removed:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">Your login credentials (username and password).</li>
                            <li style="margin-bottom:6px;">Personal contact details such as email address and phone number.</li>
                            <li style="margin-bottom:6px;">Push notification tokens and device registration data.</li>
                            <li style="margin-bottom:6px;">In-app messages, communication history, and app preferences.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">3. What Data May Be Retained and For How Long</h4>
                        <p>Certain records are retained as required by law and institutional policy:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;"><strong>Academic Records</strong> (attendance, marks, report cards) — retained for a minimum of <strong>5 years</strong> as required by educational regulations.</li>
                            <li style="margin-bottom:6px;"><strong>Fee &amp; Financial Records</strong> — retained for a minimum of <strong>7 years</strong> in compliance with financial and tax regulations.</li>
                            <li style="margin-bottom:6px;"><strong>Admission Records</strong> — retained for the period required under applicable education laws.</li>
                            <li style="margin-bottom:6px;"><strong>Legal Compliance Data</strong> — retained for the legally mandated period.</li>
                        </ul>
                        <p style="margin-top:12px;">Retained data is securely archived and not used for any active processing or marketing.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">4. Effect of Account Deletion</h4>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">You will lose access to the mobile app and all its features.</li>
                            <li style="margin-bottom:6px;">You will no longer receive school notifications, circulars, or alerts.</li>
                            <li style="margin-bottom:6px;">Access to fee payment, homework, timetable, and attendance features will be revoked.</li>
                            <li style="margin-bottom:6px;">Re-enrollment requires fresh registration through the school administration.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:10px; background:#f9f9f9; border-left:4px solid #8dc63f; padding:20px 25px; border-radius:4px;">
                        <h4 style="color:#333; margin-bottom:14px; font-size:16px; font-weight:700;">5. Contact Us</h4>
                        <p style="margin-bottom:10px;">For any queries regarding account deletion, please reach us at:</p>
                        <p style="margin-bottom:6px;">
                            <i class="fa fa-envelope-o" style="color:#8dc63f; margin-right:8px;"></i>
                            <strong>Email:</strong> <?= !empty($backend->email) ? htmlspecialchars($backend->email) : '' ?>
                        </p>
                        <p style="margin-bottom:6px;">
                            <i class="fa fa-phone" style="color:#8dc63f; margin-right:8px;"></i>
                            <strong>Phone:</strong> <?= !empty($backend->phone) ? htmlspecialchars($backend->phone) : '' ?>
                        </p>
                        <p style="margin-bottom:0;">
                            <i class="fa fa-map-marker" style="color:#8dc63f; margin-right:8px;"></i>
                            <strong>Address:</strong> <?= !empty($backend->address) ? htmlspecialchars($backend->address) : '' ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Delete Account Content -->

<script>
(function(){
    var form    = document.getElementById('dar-form');
    var alert   = document.getElementById('dar-alert');
    var submitBtn = document.getElementById('dar-submit');

    function showAlert(msg, success) {
        alert.style.display = 'block';
        alert.style.background   = success ? '#eafaf1' : '#fdf0ef';
        alert.style.border       = '1px solid ' + (success ? '#27ae60' : '#e74c3c');
        alert.style.color        = success ? '#1a7a40' : '#c0392b';
        alert.innerHTML = msg;
    }

    form.addEventListener('submit', function(e){
        e.preventDefault();
        var type   = document.getElementById('dar-type').value;
        var phone  = document.getElementById('dar-phone').value.trim();
        var reason = document.getElementById('dar-reason').value.trim();

        if (!type || !phone) {
            showAlert('Please select account type and enter your registered mobile number.', false);
            return;
        }

        submitBtn.disabled    = true;
        submitBtn.textContent = 'Submitting...';
        alert.style.display   = 'none';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '<?= base_url('frontend/submit_delete_request') ?>', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            submitBtn.disabled    = false;
            submitBtn.textContent = 'Submit Request';
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.success) {
                    showAlert('&#10003; ' + res.message, true);
                    form.reset();
                } else {
                    showAlert('&#10007; ' + res.message, false);
                }
            } catch(err) {
                showAlert('&#10007; Something went wrong. Please try again.', false);
            }
        };
        xhr.send('type=' + encodeURIComponent(type) +
                 '&phone=' + encodeURIComponent(phone) +
                 '&reason=' + encodeURIComponent(reason));
    });
})();
</script>
