<!-- Breadcrumb Area -->
<div class="bradcam-area area-padding">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="section-title white-title bradcam-title text-uppercase text-center">
                    <h2><?= !empty($frontend->privacy_label) ? htmlspecialchars($frontend->privacy_label) : 'Privacy Policy' ?></h2>
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
                    <span class="bradcam-item active text-uppercase"><?= !empty($frontend->privacy_label) ? htmlspecialchars($frontend->privacy_label) : 'Privacy Policy' ?></span>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumb Area -->

<!-- Privacy Policy Content -->
<section class="privacy-area area-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="privacy-content" style="background:#fff; padding:40px; border-radius:6px; line-height:1.9; color:#444;">

                    <h2 style="color:#333; margin-bottom:25px; font-size:26px; font-weight:700; text-transform:uppercase; border-bottom:3px solid #8dc63f; padding-bottom:12px;">
                        SCHOOL MOBILE APP &ndash; PRIVACY POLICY
                    </h2>

                    <p style="margin-bottom:28px;">
                        <strong><?= !empty($backend->sname) ? htmlspecialchars($backend->sname) : '' ?></strong> is committed to protecting the privacy of its students, parents, staff, and all users of the school mobile application. This Privacy Policy describes how we collect, use, and safeguard your personal information.
                    </p>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">1. Information We Collect</h4>
                        <p>We collect the following types of information when you use the app:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;"><strong>Personal Information:</strong> Name, date of birth, gender, contact details, and admission details of students.</li>
                            <li style="margin-bottom:6px;"><strong>Academic Data:</strong> Attendance records, examination marks, timetable, and homework details.</li>
                            <li style="margin-bottom:6px;"><strong>Financial Data:</strong> Fee payment history and transaction records.</li>
                            <li style="margin-bottom:6px;"><strong>Device Information:</strong> Device type, operating system, and app usage logs for technical support.</li>
                            <li style="margin-bottom:6px;"><strong>Communication Data:</strong> Messages, notices, and notifications sent through the app.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">2. How We Use Your Information</h4>
                        <p>The information collected is used solely for the following purposes:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">To manage student academic records and activities.</li>
                            <li style="margin-bottom:6px;">To facilitate communication between school, students, and parents.</li>
                            <li style="margin-bottom:6px;">To process fee payments and maintain financial records.</li>
                            <li style="margin-bottom:6px;">To send important school notifications and alerts.</li>
                            <li style="margin-bottom:6px;">To improve the functionality and performance of the app.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">3. Data Sharing &amp; Disclosure</h4>
                        <p>We do not sell, rent, or trade your personal information to any third party. Data may be shared only in the following circumstances:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">With authorized school staff for academic and administrative purposes.</li>
                            <li style="margin-bottom:6px;">With payment gateway providers strictly for processing fee transactions.</li>
                            <li style="margin-bottom:6px;">When required by law or government authorities.</li>
                        </ul>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">4. Data Security</h4>
                        <p>We implement appropriate technical and organizational measures to protect your personal data from unauthorized access, alteration, disclosure, or destruction. Access to personal data is restricted to authorized personnel only. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">5. Data Retention</h4>
                        <p>We retain personal data only for as long as necessary to fulfill the purposes for which it was collected, or as required by applicable laws and regulations. Academic records may be retained for the duration of a student's enrollment and for a defined period thereafter as per institutional policy.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">6. User Rights</h4>
                        <p>You have the right to:</p>
                        <ul style="margin:10px 0 0 20px; padding:0;">
                            <li style="margin-bottom:6px;">Access your personal data held by the school.</li>
                            <li style="margin-bottom:6px;">Request correction of inaccurate or incomplete data.</li>
                            <li style="margin-bottom:6px;">Request deletion of data where no longer required (subject to legal obligations).</li>
                            <li style="margin-bottom:6px;">Withdraw consent for optional data processing.</li>
                        </ul>
                        <p style="margin-top:10px;">To exercise these rights, please contact the school administration.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">7. Cookies &amp; Tracking</h4>
                        <p>The app may use session data and device identifiers to maintain login sessions and improve user experience. No third-party advertising cookies are used.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">8. Children's Privacy</h4>
                        <p>The app is designed for use by students under the supervision of their parents/guardians and authorized school staff. We do not knowingly collect personal data from children without parental or school authorization. All student accounts are created and managed by the school administration.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">9. Third-Party Services</h4>
                        <p>The app may integrate with third-party services such as payment gateways and SMS providers. These services have their own privacy policies, and we are not responsible for their data practices. We encourage users to review the privacy policies of any third-party services used.</p>
                    </div>

                    <div style="margin-bottom:28px;">
                        <h4 style="color:#8dc63f; margin-bottom:10px; font-size:16px; font-weight:700;">10. Changes to This Policy</h4>
                        <p>We may update this Privacy Policy from time to time. Any changes will be notified through the app or the school's official communication channels. Continued use of the app after such changes constitutes your acceptance of the updated policy.</p>
                    </div>

                    <div style="margin-bottom:10px; background:#f9f9f9; border-left:4px solid #8dc63f; padding:20px 25px; border-radius:4px;">
                        <h4 style="color:#333; margin-bottom:14px; font-size:16px; font-weight:700;">11. Contact Us</h4>
                        <p style="margin-bottom:4px;">For any queries or concerns regarding this Privacy Policy, please contact us:</p>
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
<!-- End Privacy Policy Content -->
